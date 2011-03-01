<?php
/*------------------------------------------------------------------------------
CCTM = Custom Content Type Manager

This is the main class for the Custom Content Type Manager plugin.

This class handles the creation and management of custom post-types (also 
referred to as 'content-types').  It requires the FormGenerator.php, 
StandardizedCustomFields.php, and the CCTMtests.php files/classes to work.

Post Thumbnails support is post-type specific:
http://markjaquith.wordpress.com/2009/12/23/new-in-wordpress-2-9-post-thumbnail-images/
------------------------------------------------------------------------------*/
class CCTM
{	
	// Name of this plugin
	const name 		= 'Custom Content Type Manager';
	
	// Required versions (referenced in the CCTMtest class).
	const wp_req_ver 	= '3.0.1';
	const php_req_ver 	= '5.2.6';
	const mysql_req_ver = '4.1.2';

	// Used to uniquely identify an option_name in the wp_options table 
	// ALL data describing the post types and their custom fields lives there.
	// DELETE FROM `wp_options` WHERE option_name='custom_content_types_mgr_data'; 
	// would clean out everything this plugin knows.
	const db_key 	= 'custom_content_types_mgr_data';
	
	// Used to uniquely identify this plugin's menu page in the WP manager
	const admin_menu_slug = 'custom_content_type_mgr';

	// These parameters identify where in the $_GET array we can find the values
	// and how URLs are constructed, e.g. some-admin-page.php?a=123&pt=xyz
	const action_param 			= 'a';
	const post_type_param 		= 'pt';

	// integer iterator used to uniquely identify groups of field definitions for 
	// CSS and $_POST variables
	public static $def_i = 0; 

	// Built-in post-types that can have custom fields, but cannot be deleted.
	public static $built_in_post_types = array('post','page');
	
	// Names that are off-limits for custom post types b/c they're already used by WP
	public static $reserved_post_types = array('post','page','attachment','revision'
		,'nav_menu','nav_menu_item');
	
	// Custom field names are not allowed to use the same names as any column in wp_posts
	public static $reserved_field_names	= array('ID','post_author','post_date','post_date_gmt',
		'post_content','post_title','post_excerpt','post_status','comment_status','ping_status',
		'post_password','post_name','to_ping','pinged','post_modified','post_modified_gmt',
		'post_content_filtered','post_parent','guid','menu_order','post_type','post_mime_type',
		'comment_count');
	
	// Future-proofing: post-type names cannot begin with 'wp_'
	// See: http://codex.wordpress.org/Custom_Post_Types	
	// FUTURE: List any other reserved prefixes here (if any)
	public static $reserved_prefixes = array('wp_');

	public static $Errors;	// used to store WP_Error object (FUTURE TODO)
	
	/*------------------------------------------------------------------------------
	This var stores the big definition for the forms that allow users to define 
	custom post-types. The form is generated in a way so that when it is posted, it 
	can be easily passed to WP's register_post_type() function.

	We populate the value via the setter function, _set_post_type_form_definition(), 
	but we do not have a getter. Since we are lazy, and PHP doesn't require 
	getters/setters, we would have forgone the setter function if possible, but we 
	had to use a setter simply to avoid the PHP syntax errors that would have 
	errupted had we tried something like this:
	
		public $myvar = array( 'val' => __('somevalue') );	
	
	That fails because we can't use the __() function nakedly when declaring a class 
	variable. :(
	------------------------------------------------------------------------------*/
	public static $post_type_form_definition = array();
	
	/*------------------------------------------------------------------------------
	This array defines the form used for all new custom field definitions.
	The variable is populated via a setter: _set_custom_field_def_template() for
	the same reason as the $post_type_form_definition var above (see above).
	
	See the _page_manage_custom_fields() function for when and how these forms 
	are used and handled.
	------------------------------------------------------------------------------*/
	public static $custom_field_def_template = array();

	
	//! Private Functions
	/*------------------------------------------------------------------------------
	Generate HTML portion of our manage custom fields form. This is in distinction
	to the JS portion of the form, which uses a slightly different format.

	self::$def_i is used to track the definition #.  All 5 output fields will use 
	the same $def_i number to identify their place in the $_POST array.
	
	INPUT: $custom_field_defs (mixed) an array of hashes, each hash describing
	a custom field.
	
	Array
	(
	    [1] => Array
	        (
	            [label] => Rating
	            [name] => rating
	            [description] => MPAA rating
	            [type] => dropdown
	            [options] => Array
	                (
	                    [0] => G
	                    [1] => PG
	                    [2] => PG-13
	                )
	
	            [sort_param] => 
	        )
	
	)

	OUTPUT: An HTML form, length depends on the # of field defs.
	------------------------------------------------------------------------------*/
	private static function _get_html_field_defs($custom_field_defs)
	{	
		$output = '';
		foreach ($custom_field_defs as $def)
		{
			FormGenerator::$before_elements = '
			<div id="generated_form_number_'.self::$def_i.'">';
			
			FormGenerator::$after_elements = '
				<br/>
				<span class="button custom_content_type_mgr_remove" onClick="javascript:removeDiv(this.parentNode.id);">'.__('Remove This Field').'</span>
				<hr/>
			</div>';
			
			$translated = self::_transform_data_structure_for_editing($def);
			$output .= FormGenerator::generate($translated);
			self::$def_i++;
		}
		
		return $output;
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _get_icons()
	{
		
		$icons = array();
		if ($handle = opendir(CCTM_PATH.'/images/icons/default')) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if ( !preg_match('/^\./', $file) )
				{
					$icons[] = $file;
				}
			}
			closedir($handle);
		}
		
		$output = '';
		$tpl = CCTM_PATH.'/tpls/settings/icon.tpl';
		if ( file_exists($tpl) ) 
		{ 
			$tpl = file_get_contents($tpl);
			
		}
		foreach ( $icons as $img )
		{
			$output .= FormGenerator::parse($tpl, array('title'=> $img, 'src'=> CCTM_URL.'/images/icons/default/'.$img) );
		}
		
		return $output;
	}
	
	//------------------------------------------------------------------------------
	/**
	Gets a field definition ready for use inside of a JS variable.  We have to over-
	ride some of the names used by the _get_html_field_defs() function so the 
	resulting HTML/Javascript will inherit values from Javascript variables dynamically
	as the user adds new form fields on the fly.
	
	Here +def_i+ represents a JS concatenation, where def_i is a JS variable.
	*/
	private static function _get_javascript_field_defs()
	{
		$def = self::$custom_field_def_template;
		foreach ($def as $row_id => &$field)
		{
			$name = $row_id;
			// alter the Extra part of this for the listener on the dropdown
			if($name == 'type')
			{
				$field['extra'] = str_replace('[+def_i+]', "'+def_i+'", $field['extra']);
			}
			$field['name'] = "custom_fields['+def_i+'][$name]";
		}
		
		FormGenerator::$before_elements = '<div id="generated_form_number_\'+def_i+\'">';
		FormGenerator::$after_elements = '
			<a class="button" href="#" onClick="javascript:removeDiv(this.parentNode.id);">'
			.__('Remove This Field', CCTM_TXTDOMAIN).'</a>
			<hr/>
		</div>';
		
		$output = FormGenerator::generate($def, 'javascript');

		// Javascript chokes on newlines...
		return str_replace( array("\r\n", "\r", "\n", "\t"), ' ', $output);
	}
		
	/*------------------------------------------------------------------------------
	Designed to safely retrieve scalar elements out of a hash. Don't use this 
	if you have a more deeply nested object (e.g. an array of arrays).
	INPUT: 
		$hash : an associative array, e.g. array('animal' => 'Cat');
		$key : the key to search for in that array, e.g. 'animal'
		$default (optional) : value to return if the value is not set. Default=''
	OUTPUT: either safely escaped value from the hash or the default value
	------------------------------------------------------------------------------*/
	private static function _get_value($hash, $key, $default='')
	{
		if ( !isset($hash[$key]) )
		{
			return $default;
		}
		else
		{	
			if ( is_array($hash[$key]) )
			{
				return $hash[$key];
			}
			// Warning: stripslashes was added to avoid some weird behavior
			else
			{
				return esc_html(stripslashes($hash[$key]));
			}
		}
	}
		
	/*------------------------------------------------------------------------------
	SYNOPSIS: checks the custom content data array to see if $post_type exists.
		The $data array is structured something like this:

		$data = array(
			'movie' => array('name'=>'movie', ... ),
			'book' => array('name'=>'book', ... ),
			...
		);
	
	So we can just check the keys of the main array to see if the post type exists.
	
	Built-in post types 'page' and 'post' are considered valid (i.e. existing) by 
	default, even if they haven't been explicitly defined for use by this plugin
	so long as the 2nd argument, $search_built_ins, is not overridden to false.
	
	INPUT: 
		$post_type (string) the lowercase database slug identifying a post type.
		$search_built_ins (boolean) whether or not to search inside the 
			$built_in_post_types array. 
			
	OUTPUT: boolean true | false indicating whether this is a valid post-type
	------------------------------------------------------------------------------*/
	private static function _is_existing_post_type($post_type, $search_built_ins=true)
	{
		$data = get_option( self::db_key );
		
		// If there is no existing data, check against the built-ins
		if ( empty($data) && $search_built_ins ) 
		{
			return in_array($post_type, self::$built_in_post_types);
		}
		// If there's no existing $data and we omit the built-ins...
		elseif ( empty($data) && !$search_built_ins )
		{
			return false;
		}
		// Check to see if we've stored this $post_type before
		elseif ( array_key_exists($post_type, $data) )
		{
			return true;
		}
		// Check the built-ins
		elseif ( $search_built_ins && in_array($post_type, self::$built_in_post_types) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//! Links
	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_activate($post_type)
	{
		return sprintf(
			'<a href="?page=%s&%s=6&%s=%s" title="%s">%s</a>'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
			, __('Activate this content type', CCTM_TXTDOMAIN)
			, __('Activate',CCTM_TXTDOMAIN) 
		);
	}
	
		
	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_deactivate($post_type)
	{
		return sprintf(
			'<a href="?page=%s&%s=7&%s=%s" title="%s">%s</a>'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
			, __('Deactivate this content type', CCTM_TXTDOMAIN)
			, __('Deactivate',CCTM_TXTDOMAIN) 
		);
	}

	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_delete($post_type)
	{
		return sprintf(
			'<a href="?page=%s&%s=3&%s=%s" title="%s">%s</a>'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
			, __('Delete this content type', CCTM_TXTDOMAIN)
			, __('Delete',CCTM_TXTDOMAIN) 
		);
	}
	
	///------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_manage_custom_fields($post_type)
	{
		return sprintf(
			'<a href="?page=%s&%s=4&%s=%s" title="%s">%s</a>'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
			, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
			, __('Manage Custom Fields',CCTM_TXTDOMAIN) 
		);
			
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_edit($post_type)
	{				
		return sprintf(
			'<a href="?page=%s&%s=2&%s=%s" title="%s">%s</a>'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
			, __('Edit this content type', CCTM_TXTDOMAIN )
			, __('Edit', CCTM_TXTDOMAIN)
		);
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	*/
	private static function _link_view_sample_templates($post_type)
	{
		return sprintf('<a href="?page=%s&%s=8&%s=%s" title="%s">%s</a>'
					, self::admin_menu_slug
					, self::action_param
					, self::post_type_param
					, $post_type
					, __('View Sample Templates for this content type', CCTM_TXTDOMAIN )
					, __('View Sample Templates', CCTM_TXTDOMAIN)
				);
	}
	//! Pages	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Activating a post type will cause it to show up in the WP menus and its custom 
	fields will be managed.
	------------------------------------------------------------------------------*/
	private static function _page_activate_post_type($post_type)
	{
		// Validate post type
		if (!self::_is_existing_post_type($post_type) )
		{
			self::_page_display_error();
			return;
		}

		// get current values from database (if any)
		$data = get_option( self::db_key, array() );
		$data[$post_type]['is_active'] = 1;
		update_option( self::db_key, $data );
		$msg = '
				<div class="updated">
					<p>'
					. sprintf( __('The %s post_type has been activated.', CCTM_TXTDOMAIN), '<em>'.$post_type.'</em>')
					. '</p>
				</div>';
		self::set_flash($msg);
		// Often, PHP scripts use the header() function to refresh a page, but
		// WP has already claimed those, so we use a JavaScript refresh instead.
		// Refreshing the page ensures that active post types are added to menus.	
		$msg = '
			<script type="text/javascript">
				window.location.replace("?page='.self::admin_menu_slug.'");
			</script>';
		print $msg;	
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Create a new post type
	------------------------------------------------------------------------------*/
	private static function _page_create_new_post_type()
	{
		self::_set_post_type_form_definition();
		
		// Variables for our template
		$page_header 	= __('Create Custom Content Type', CCTM_TXTDOMAIN);
		$fields			= '';
		$action_name 	= 'custom_content_type_mgr_create_new_content_type';
		$nonce_name 	= 'custom_content_type_mgr_create_new_content_type_nonce';
		$submit 		= __('Create New Content Type', CCTM_TXTDOMAIN);
		$msg 			= ''; 	
		
		$def = self::$post_type_form_definition;
		
		// Save data if it was properly submitted
		if ( !empty($_POST) && check_admin_referer($action_name,$nonce_name) )
		{
			$sanitized_vals = self::_sanitize_post_type_def($_POST);
			$error_msg = self::_post_type_name_has_errors($sanitized_vals['post_type'], true);

			if ( empty($error_msg) )
			{				
				self::_save_post_type_settings($sanitized_vals);
				$msg = '
				<div class="updated">
					<p>'
					. sprintf( __('The content type %s has been created', CCTM_TXTDOMAIN), '<em>'.$sanitized_vals['post_type'].'</em>')
					. '</p>
				</div>';
				self::set_flash($msg);
				self::_page_show_all_post_types();
				return;
			}
			else
			{
				// This is for repopulating the form
				foreach ( $def as $node_id => $d )
				{
					$d['value'] = self::_get_value($sanitized_vals, $d['name']);
				}
					
				$msg = "<div class='error'>$error_msg</div>";
			}
		}
		
		// 
		foreach ($def as $pt => &$d)
		{
			$d['raw_name'] = $pt;
		}				
		$fields = FormGenerator::generate($def,'css-friendly');	
	

		$mgr_tpl_file = CCTM_PATH.'/tpls/settings/edit_post_type.tpl';
		if ( file_exists($mgr_tpl_file) ) 
		{ 
			$tpl = file_get_contents($mgr_tpl_file);
			FormGenerator::$placeholders['icons'] = self::_get_icons();
			FormGenerator::$placeholders['CCTM_URL'] = CCTM_URL;
			$fields = FormGenerator::parse($tpl, FormGenerator::$placeholders);
		}
		include('pages/basic_form.php');
	}
	
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Deactivate a post type. This will remove custom post types from the WP menus;
	deactivation stops custom fields from being standardized in built-in and custom 
	post types
	------------------------------------------------------------------------------*/
	private static function _page_deactivate_post_type($post_type)
	{
		// Validate post type
		if (!self::_is_existing_post_type($post_type) )
		{
			self::_page_display_error();
			return;
		}
		// Variables for our template
		$style			= '';
		$page_header 	= sprintf( __('Deactivate Content Type %s', CCTM_TXTDOMAIN), $post_type );
		$fields			= '';
		$action_name 	= 'custom_content_type_mgr_deactivate_content_type';
		$nonce_name 	= 'custom_content_type_mgr_deactivate_content_type_nonce';
		$submit 		= __('Deactivate', CCTM_TXTDOMAIN);
				
		// If properly submitted, Proceed with deleting the post type
		if ( !empty($_POST) && check_admin_referer($action_name,$nonce_name) )
		{
			// get current values from database
			$data = get_option( self::db_key, array() );
			$data[$post_type]['is_active'] = 0;
			update_option( self::db_key, $data );
			
			$msg = '<div class="updated"><p>'
				. sprintf( __('The %s content type has been deactivated.', CCTM_TXTDOMAIN), $post_type )
				. '</p></div>';
			self::set_flash($msg);
			
			// A JavaScript refresh ensures that inactive post types are removed from the menus.	
			$msg = '
			<script type="text/javascript">
				window.location.replace("?page='.self::admin_menu_slug.'");
			</script>';
			print $msg;
			return;
		}
		
		$msg = '<div class="error">
			<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
			<p>'
			. sprintf( __('You are about to deactivate the %s post type.', CCTM_TXTDOMAIN ), "<strong>$post_type</strong>")
			.'</p>';		
		
		// If it's a custom post type, we include some additional info.
		if ( !in_array($post_type, self::$built_in_post_types) )
		{
			$msg .= '<p>'
			. sprintf( __('Deactivation does not delete anything, but it does make %s posts unavailable to the outside world. %s will be removed from the administration menus and you will no longer be able to edit them using the WordPress manager.', CCTM_TXTDOMAIN), "<strong>$post_type</strong>", "<strong>$post_type</strong>" )
			.'</p>';
		}
		
		$post_cnt_obj = wp_count_posts($post_type);
		$msg .= '<p>'
			. sprintf( __('This would affect %1$s published %2$s posts.'
			,CCTM_TXTDOMAIN), '<strong>'.$post_cnt_obj->publish.'</strong>'
				 , "<strong>$post_type</strong>")
				.'</p>';
		$msg .= '<p>'.__('Are you sure you want to do this?',CCTM_TXTDOMAIN).'</p>
			</div>';		

		include('pages/basic_form.php');
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	This is only a valid page for custom post types.
	------------------------------------------------------------------------------*/
	private static function _page_delete_post_type($post_type)
	{
		// We can't delete built-in post types
		if (!self::_is_existing_post_type($post_type, false ) )
		{
			self::_page_display_error();
			return;
		}

		// Variables for our template
		$style			= '';
		$page_header = sprintf( __('Delete Content Type: %s', CCTM_TXTDOMAIN), $post_type );
		$fields			= '';
		$action_name = 'custom_content_type_mgr_delete_content_type';
		$nonce_name = 'custom_content_type_mgr_delete_content_type_nonce';
		$submit 		= __('Delete',CCTM_TXTDOMAIN);
		
		// If properly submitted, Proceed with deleting the post type
		if ( !empty($_POST) && check_admin_referer($action_name,$nonce_name) )
		{
			// get current values from database
			$data = get_option( self::db_key, array() );
			unset($data[$post_type]); // <-- Delete this node of the data structure
			update_option( self::db_key, $data );
			$msg = '<div class="updated"><p>'
				.sprintf( __('The post type %s has been deleted', CCTM_TXTDOMAIN), "<em>$post_type</em>")
				. '</p></div>';
			self::set_flash($msg);
			self::_page_show_all_post_types();
			return;
		}
		
		$msg = '<div class="error">
			<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
			<p>'
			. sprintf( __('You are about to delete the %s post type. This will remove all of its settings from the database, but this will NOT delete any rows from the wp_posts table. However, without a custom post type defined for those rows, they will be essentially invisible to WordPress.', CCTM_TXTDOMAIN), "<em>$post_type</em>" )
			.'</p>'
			. '<p>'.__('Are you sure you want to do this?',CCTM_TXTDOMAIN).'</p></div>';

		include('pages/basic_form.php');
		
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Returned on errors. Future: accept an argument identifying an error
	------------------------------------------------------------------------------*/
	private static function _page_display_error()
	{	
		$msg = '<p>'. __('Invalid post type.', CCTM_TXTDOMAIN) 
			. '</p><a class="button" href="?page='
			.self::admin_menu_slug.'">'. __('Back', CCTM_TXTDOMAIN). '</a>';
		wp_die( $msg );
	}


	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Edit an existing post type. Changing the unique post-type identifier (i.e. name)
	is not allowed. 
	------------------------------------------------------------------------------*/
	private static function _page_edit_post_type($post_type)
	{
		// We can't edit built-in post types
		if (!self::_is_existing_post_type($post_type, false ) )
		{
			self::_page_display_error();
			return;
		}

		self::_set_post_type_form_definition($post_type);

		// Variables for our template (TODO: register this instead of this cheap inline trick)
		$style			= '';
		$page_header 	= __('Edit Content Type: ') . $post_type;
		$fields			= '';
		$action_name = 'custom_content_type_mgr_edit_content_type';
		$nonce_name = 'custom_content_type_mgr_edit_content_type_nonce';
		$submit 		= __('Save',CCTM_TXTDOMAIN);
		$msg 			= ''; 	// Any validation errors
	
		$def = self::$post_type_form_definition;
		
		// Save data if it was properly submitted
		if ( !empty($_POST) && check_admin_referer($action_name,$nonce_name) )
		{
			$sanitized_vals = self::_sanitize_post_type_def($_POST);
			//print_r($sanitized_vals); exit;
			$error_msg = self::_post_type_name_has_errors($sanitized_vals['post_type']);

			if ( empty($error_msg) )
			{				
				self::_save_post_type_settings($sanitized_vals);
				
				$msg .= '<div class="updated"><p>'
					. sprintf( __('Settings for %s have been updated.', CCTM_TXTDOMAIN )
						, '<em>'.$sanitized_vals['post_type'].'</em>')
					.'</p></div>';
				self::set_flash($msg);
				
				$msg = '
					<script type="text/javascript">
						window.location.replace("?page='.self::admin_menu_slug.'");
					</script>';
				print $msg;
				return;
			}
			else
			{
				// This is for repopulating the form
				$def = self::_populate_form_def_from_data($def, $sanitized_vals);
				$msg = "<div class='error'>$error_msg</div>";
			}
		}

		// get current values from database
		$data = get_option( self::db_key, array() );
		
		// 
		foreach ($def as $pt => &$d)
		{
			$d['raw_name'] = $pt;
		}
		
		// Populate the form $def with values from the database
		$def = self::_populate_form_def_from_data($def, $data[$post_type]);
		$fields = FormGenerator::generate($def,'css-friendly');		
		
		$mgr_tpl_file = CCTM_PATH.'/tpls/settings/edit_post_type.tpl';
		if ( file_exists($mgr_tpl_file) ) 
		{ 
			$tpl = file_get_contents($mgr_tpl_file);
			FormGenerator::$placeholders['icons'] = self::_get_icons();
			FormGenerator::$placeholders['CCTM_URL'] = CCTM_URL;
			// print_r(FormGenerator::$placeholders);
			$fields = FormGenerator::parse($tpl, FormGenerator::$placeholders);
		}
		include('pages/basic_form.php');
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Manage custom fields for any post type, built-in or custom.
	------------------------------------------------------------------------------*/
	private static function _page_manage_custom_fields($post_type)
	{
		// Validate post type
		if (!self::_is_existing_post_type($post_type) )
		{
			self::_page_display_error();
			return;
		}

		$action_name = 'custom_content_type_mgr_manage_custom_fields';
		$nonce_name = 'custom_content_type_mgr_manage_custom_fields_nonce';
		$msg = ''; 			// Any validation errors
		$def_cnt = '';	// # of custom field definitions
		// The set of fields that makes up a custom field definition, but stripped of newlines
		// and with some modifications so it can be used inside a javascript variable
		$new_field_def_js = ''; 
		// Existing fields
		$fields = '';
		
		$data = get_option( self::db_key, array() );
		
		// Validate/Save data if it was properly submitted
		if ( !empty($_POST) && check_admin_referer($action_name,$nonce_name) )
		{
			$error_msg = array(); // used as a flag
			if (!isset($_POST['custom_fields']))
			{
				$data[$post_type]['custom_fields'] = array(); // all custom fields were deleted
			}
			else
			{
				$data[$post_type]['custom_fields'] = $_POST['custom_fields'];
				foreach ( $data[$post_type]['custom_fields'] as &$cf )
				{
					if ( preg_match('/[^a-z_0-9]/i', $cf['name']))
					{
						$error_msg[] = sprintf(
							__('%s contains invalid characters.',CCTM_TXTDOMAIN)
							, '<strong>'.$cf['name'].'</strong>');
						$cf['name'] = preg_replace('/[^a-z_]/','',$cf['name']);
					}
					if ( strlen($cf['name']) > 20 )
					{
						$cf['name'] = substr($cf['name'], 0 , 20);
						$error_msg[] = sprintf(
							__('%s is too long.',CCTM_TXTDOMAIN)
							, '<strong>'.$cf['name'].'</strong>');
					}
					if ( in_array($cf['name'], self::$reserved_field_names ) )
					{
						$error_msg[] = sprintf(
							__('%s is a reserved name.',CCTM_TXTDOMAIN)
							, '<strong>'.$cf['name'].'</strong>');						
					}
				}
			}
			if ($error_msg)
			{
				foreach ( $error_msg as &$e )
				{
					$e = '<li>'.$e.'</li>';
				}
				
				$msg = sprintf('<div class="error">
					<h3>%1$s</h3>
					%2$s %3$s %4$s
					<ul style="margin-left:30px">
						%5$s
					</ul>
					</div>'
					, __('There were errors in the names of your custom fields.', CCTM_TXTDOMAIN)
					, __('Names must not exceed 20 characters in length.', CCTM_TXTDOMAIN)
					, __('Names may contain the letters, numbers, and underscores only.', CCTM_TXTDOMAIN)
					, __('You cannot name your field using any reserved name.', CCTM_TXTDOMAIN)
					, implode("\n", $error_msg)
				);
			}
			else
			{
				update_option( self::db_key, $data );
				$msg = sprintf('<div class="updated"><p>%s</p></div>'
						, sprintf(__('Custom fields for %s have been updated', CCTM_TXTDOMAIN)
							, '<em>'.$post_type.'</em>'
						)
					);
				self::set_flash($msg);
				self::_page_show_all_post_types();
				return;
			}
		}	
	
		// We want to extract a $def for only THIS content_type's custom_fields
		$def = array();
		if ( isset($data[$post_type]['custom_fields']) )
		{
			$def = $data[$post_type]['custom_fields'];
		}
		// count # of custom field definitions --> replaces [+def_i+]
		$def_cnt = count($def);
		
		// We don't need the exact number of form elements, we just need an integer
		// that is sufficiently high so that the ids of Javascript-created elements
		// do not conflict with the ids of PHP-created elements.
		$element_cnt = count($def, COUNT_RECURSIVE);

		if (!$def_cnt)
		{
			$x = sprintf( __('The %s post type does not have any custom fields yet.', CCTM_TXTDOMAIN)
				, "<em>$post_type</em>" );
			$y = __('Click the button above to add a custom field.', CCTM_TXTDOMAIN );
			$msg .= sprintf('<div class="updated"><p>%s %s</p></div>', $x, $y);
		}

		$fields = self::_get_html_field_defs($def);

		// Gets a form definition ready for use inside of a JS variable
		$new_field_def_js = self::_get_javascript_field_defs();
//		print $new_field_def_js; exit;
		include('pages/manage_custom_fields.php');
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	Show what a single page for this custom post-type might look like.  This is 
	me throwing a bone to template editors and creators.
	
	I'm using a tpl and my parse() function because I have to print out sample PHP
	code and it's too much of a pain in the ass to include PHP without it executing.
	------------------------------------------------------------------------------*/
	private static function _page_sample_template($post_type)
	{
		// Validate post type
		if (!self::_is_existing_post_type($post_type) )
		{
			self::_page_display_error();
			return;
		}

		$current_theme_name = get_current_theme(); 
		$current_theme_path = get_stylesheet_directory(); 
		
		$hash = array();
		$data = get_option( self::db_key, array() );
		$tpl = file_get_contents( CCTM_PATH.'/tpls/samples/single_post.tpl');
		$tpl = htmlentities($tpl);

		$single_page_msg = sprintf( __('WordPress supports a custom theme file for each registered post-type (content-type). Copy the text below into a file named <strong>%s</strong> and save it into your active theme.', CCTM_TXTDOMAIN)
			, 'single-'.$post_type.'.php'
		);
		$single_page_msg .= sprintf( __('You are currently using the %1$s theme. Save the file into the %2$s directory.',CCTM_TXTDOMAIN)
			, '<strong>'.$current_theme_name.'</strong>'
			, '<strong>'.$current_theme_path.'</strong>'
		);
		
		$data = get_option( self::db_key, array() );
		$def = array();
		if ( isset($data[$post_type]['custom_fields']) )
		{
			$def = $data[$post_type]['custom_fields'];
		}
		//print_r($data[$post_type]); exit;
		// built-in content types don't verbosely display what they display
		/* Array
(
    [product] => Array
        (
            [supports] => Array
                (
                    [0] => title
                    [1] => editor
                    [2] => author
                    [3] => thumbnail
                    [4] => excerpt
                    [5] => trackbacks
                    [6] => custom-fields
                )
*/
//		print_r($data); exit;
		// Check the TYPE of custom field to handle image and relation custom fields.
		// title, author, thumbnail, excerpt
		$custom_fields_str = '';
		$builtin_fields_str = '';
		$comments_str = '';
		// Built-in Fields
		if ( is_array($data[$post_type]['supports']) )
		{
			if ( in_array('title', $data[$post_type]['supports']) )
			{
				$builtin_fields_str .= "\n\t<h1><?php the_title(); ?></h1>\n";
			}
			if ( in_array('editor', $data[$post_type]['supports']) )
			{
				$builtin_fields_str .= "\n\t\t<?php the_content(); ?>\n";
			}
			if ( in_array('author', $data[$post_type]['supports']) )
			{
				$builtin_fields_str .= "\n\t\t<?php the_author(); ?>\n";
			}
			if ( in_array('thumbnail', $data[$post_type]['supports']) )
			{
				$builtin_fields_str .= "\n\t\t<?php the_post_thumbnail(); ?>\n";
			}
			if ( in_array('excerpt', $data[$post_type]['supports']) )
			{
				$builtin_fields_str .= "\n\t\t<?php the_excerpt(); ?>\n";
			}
			if ( in_array('comments', $data[$post_type]['supports']) )
			{
				$comments_str .= "\n\t\t<?php comments_template(); ?>\n";
			}
		}

		// Custom fields
		foreach ( $def as $d )
		{
			switch ($d['type'])
			{
				case 'media':
					$custom_fields_str .= "\t\t<?php /* http://codex.wordpress.org/Function_Reference/wp_get_attachment_image */ ?>\n";
					$custom_fields_str .= sprintf("\t\t<strong>%s:</strong> <?php print wp_get_attachment_image( get_custom_field('%s'), 'full'); ?><br />\n", $d['label'], $d['name']);
				case 'text':
				default:
				$custom_fields_str .= sprintf("\t\t<strong>%s:</strong> <?php print_custom_field('%s'); ?><br />\n", $d['label'], $d['name']);
			}
		}
		// Populate placeholders
		$hash['post_type'] = $post_type;
		$hash['built_in_fields'] = $builtin_fields_str;
		$hash['custom_fields'] = $custom_fields_str;
		$hash['comments'] = $comments_str;
		
		$single_page_sample_code = StandardizedCustomFields::parse($tpl, $hash);
		
		// Manager Page Sample CSS
		$manager_page_css_msg = '';
		$manager_page_sample_css = '';
		$manager_page_css_msg .= sprintf( __('You can customize the forms in the manager by adding the following CSS declarations to your in your theme\'s %s file.', CCTM_TXTDOMAIN)
			, '<strong>editor-style.css</strong>' );
		$manager_page_css_msg .= sprintf( __('You can override the style definitions in %s', CCTM_TXTDOMAIN)
			, '<strong>'.CCTM_PATH.'/css/posts.css</strong>');

		// or in your theme's editor-style.css file.
		// FormGenerator::element_wrapper_id_prefix
		foreach ( $def as $d )
		{
			$manager_page_sample_css .= sprintf("/* The div that wraps the %s field */\n#%s%s {\n\n}\n\n/* Style the input for the %s field */\n#%s%s {\n\n}\n\n"
			, $d['name']
			, FormGenerator::element_wrapper_id_prefix 
			, $d['name']
			, $d['name']
			, StandardizedCustomFields::field_name_prefix
			, $d['name']
			);
		}
		
		// Manager Page HTML examples;
		// post-new.php?post_type=%s
		$manager_page_html_msg = '';
		$manager_page_sample_html = '';
		/* 
		<div class="formgenerator_element_wrapper" id="custom_field_id_sample_img">
			<span class="formgenerator_label formgenerator_media_label" id="formgenerator_label_custom_content_sample_img">Sample Image (sample_img)</span>
			<input type="hidden" id="custom_content_sample_img" name="custom_content_sample_img" />
		</div>
		
		A <div> wraps each custom field. Its class is "formgenerator_element_wrapper" and its id is the field name prefixed by "custom_field_id_"
		Labels for each field are wrapped with their own <span>. Each <span> uses 2 classes: a general one for all generated labels, and another specific to the field type (text, checkbox, etc).		
		*/
		$manager_page_html_msg .= __('You can create custom manager templates for the users who will be creating new content.', CCTM_TXTDOMAIN) . ' ';
		$manager_page_html_msg .= sprintf( __('Create a file named %s in the %s directory.', CCTM_TXTDOMAIN)
			, '<strong>'.$post_type.'.tpl</strong>'
			, '<strong>'.CCTM_PATH.'/tpls/manager/</strong>'
		);
		foreach ( $def as $d )
		{
			unset($d['default_value']); // this should not be publicly available.
			$manager_page_sample_html .= sprintf( "<!-- Sample HTML for %s field-->\n", $d['name']);
			$manager_page_sample_html .= "<!-- [+".$d['name']."+] will generate field in its entirety -->\n";
			$manager_page_sample_html .= "<!-- Individual placeholders follow: -->\n";
			foreach ($d as $k => $v)
			{
				$manager_page_sample_html .= '[+'.$d['name'].'.'.$k.'+]'. "\n";	
			}
			$manager_page_sample_html .= "\n";
			
//! FUTURE: TODO: Give more complete examples.
/*
			switch ( $d['type'] ) 
			{
				case 'checkbox':
					$output_this_field .= self::_get_checkbox_element($field_def);
					break;
				case 'dropdown':
					$output_this_field .= self::_get_dropdown_element($field_def);
					break;
				case 'media':
					$output_this_field .= self::_get_media_element($field_def);
					break;
				case 'readonly':
					$output_this_field .= self::_get_readonly_element($field_def);
					break;
				case 'relation':
					$output_this_field .= self::_get_relation_element($field_def);
					break;
				case 'textarea':
					$output_this_field .= self::_get_textarea_element($field_def);
					break;
				case 'wysiwyg':
					$output_this_field .= self::_get_wysiwyg_element($field_def);
					break;
				case 'text':
				default: 
					$output_this_field .= self::_get_text_element($field_def);
					break;
			}
*/
			
		}
		
		if ( empty($def) )
		{
			$manager_page_sample_css = sprintf( '/* %s %s */'
				, __('There are no custom fields defined this post type.', CCTM_TXTDOMAIN) 
				, "($post_type)"
			);
			$manager_page_sample_html = sprintf( '<!-- %s %s -->'
				, __('There are no custom fields defined this post type.', CCTM_TXTDOMAIN) 
				, "($post_type)"
			);
		}

		
		
		include('pages/sample_template.php');
	}
	
	/*------------------------------------------------------------------------------
	Manager Page -- called by page_main_controller()
	List all post types (default page)
	------------------------------------------------------------------------------*/
	private static function _page_show_all_post_types()
	{	
		$msg = self::get_flash();

		$data = get_option( self::db_key, array() );
		$customized_post_types =  array_keys($data);
		$displayable_types = array_merge(self::$built_in_post_types , $customized_post_types);
		$displayable_types = array_unique($displayable_types);
		
		$row_data = '';
		$tpl = file_get_contents(CCTM_PATH.'/tpls/settings/post_type_tr.tpl');
		foreach ( $displayable_types as $post_type )
		{
			$hash = array(); // populated for the tpl
			$hash['post_type'] = $post_type;
			
			// Get our links
			$deactivate				= self::_link_deactivate($post_type);
			$edit_link 				= self::_link_edit($post_type);
			$manage_custom_fields 	= self::_link_manage_custom_fields($post_type);
			$view_templates 		= self::_link_view_sample_templates($post_type);
			 
			 
			$hash['edit_manage_view_links'] = $edit_link . ' | '. $manage_custom_fields . ' | ' . $view_templates;
			
			if ( isset($data[$post_type]['is_active']) && !empty($data[$post_type]['is_active']) )
			{
				$hash['class'] = 'active';
				$hash['activate_deactivate_delete_links'] = '<span class="deactivate">'.$deactivate.'</span>';	
				$is_active = true;
			}
			else
			{
				$hash['class'] = 'inactive';
				$hash['activate_deactivate_delete_links'] = '<span class="activate">'
					. self::_link_activate($post_type) . ' | </span>'
					. '<span class="delete">'. self::_link_delete($post_type).'</span>';
				$is_active = false;
			}

			// Built-in post types use a canned description and override a few other behaviors
			if ( in_array($post_type, self::$built_in_post_types) ) 
			{
				$hash['description'] 	= __('Built-in post type.', CCTM_TXTDOMAIN);
				$hash['edit_manage_view_links'] = $manage_custom_fields . ' | ' . $view_templates;
				if (!$is_active)
				{
					$hash['activate_deactivate_delete_links'] = '<span class="activate">'
						. self::_link_activate($post_type) . '</span>';
				}
				
				$hash['activate_deactivate_delete_links'] = '';
			}
			// Whereas users define the description for custom post types
			else
			{
				$hash['description'] 	= self::_get_value($data[$post_type],'description');
			}
			
			// Images
			$hash['icon'] = '';
			switch ($post_type)
			{
				case 'post':
					$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/default/post.png' . '" width="15" height="15"/>';
					break;
				case 'page':
					$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/default/page.png' . '" width="14" height="16"/>';
					break;
				default:
				//print_r($data[$post_type]); exit;
					if ( !empty($data[$post_type]['menu_icon']) && !$data[$post_type]['use_default_menu_icon'] )
					{
						$hash['icon'] = '<img src="'. $data[$post_type]['menu_icon'] . '" />';
					}
					break;
			}
			$row_data .= FormGenerator::parse($tpl, $hash);
		}
		//! TODO
		//include('pages/sortable-list.php');
		include('pages/default.php');
	}

	/*------------------------------------------------------------------------------
	Populate form definition with data that defines a post-type.  This data comes 
	either from the database or from the $_POST array.  The $pt_data 
	(i.e. post-type data) should contain only information about 
	a single post_type; do not pass this function the entire contents of the 
	get_option().
	
	This whole function is necessary because the form generator definition needs
	to know where to find values for its fields -- the $def is an empty template,
	so we need to populate it with values by splicing the $def together with the 
	$pt_data.  Some of this complication is due to the fact that we need to update
	the field names to acommodate arrays, e.g. 
		<input type="text" name="some[2][name]" />
	
	See http://codex.wordpress.org/Function_Reference/register_post_type
	
	INPUT: $def (mixed) form definition
		$pt_data (mixed) data describing a single post type
	OUTPUT: $def updated with values
	------------------------------------------------------------------------------*/
	private static function _populate_form_def_from_data($def, $pt_data)
	{
		$labels_array = array(
			'singular_label'	=> 'singular_name',
			'add_new_label'		=> 'add_new',
			'add_new_item_label' => 'add_new_item',
			'edit_item_label'	=> 'edit_item',
			'new_item_label'	=> 'new_item',
			'view_item_label'	=> 'view_item',
			'search_items_label'	=> 'search_items',			
			'not_found_label'	=> 'not_found',
			'not_found_in_trash_label'	=> 'not_found_in_trash',
			'parent_item_colon_label'	=> 'parent_item_colon',
			'menu_name_label'	=> 'menu_name'
		);

		//print_r($pt_data); exit;
		foreach ($def as $node_id => $tmp)
		{
			if ( $node_id == 'supports_title' )
			{			
				if ( !empty($pt_data['supports']) && in_array('title', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'title';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}
			}
			elseif ( $node_id == 'supports_editor' )
			{			
				if ( !empty($pt_data['supports']) && in_array('editor', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'editor';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}
			}
			elseif ( $node_id == 'supports_author' )
			{			
				if ( !empty($pt_data['supports']) && in_array('author', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'author';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'supports_excerpt' )
			{			
				if ( !empty($pt_data['supports']) && in_array('excerpt', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'excerpt';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'supports_thumbnail' )
			{			
				if ( !empty($pt_data['supports']) && in_array('thumbnail', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'thumbnail';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'supports_trackbacks' )
			{			
				if ( !empty($pt_data['supports']) && in_array('trackbacks', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'trackbacks';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}		
			elseif ( $node_id == 'supports_custom-fields' )
			{			
				if ( !empty($pt_data['supports']) && in_array('custom-fields', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'custom-fields';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}			
			elseif ( $node_id == 'supports_comments' )
			{			
				if ( !empty($pt_data['supports']) && in_array('comments', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'comments';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'supports_revisions' )
			{			
				if ( !empty($pt_data['supports']) && in_array('revisions', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'revisions';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'supports_page-attributes' )
			{			
				if ( !empty($pt_data['supports']) && in_array('page-attributes', $pt_data['supports']) )
				{
					$def[$node_id]['value'] = 'page-attributes';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'rewrite_slug' )
			{			
				if ( !empty($pt_data['rewrite']['slug']) )
				{
					$def[$node_id]['value'] = $pt_data['rewrite']['slug'];
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'rewrite_with_front' )
			{			
				if ( !empty($pt_data['rewrite']['with_front']) )
				{
					$def[$node_id]['value'] = $pt_data['rewrite']['with_front'];
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}

			elseif ( $node_id == 'taxonomy_categories' )
			{	
				//print $node_id; exit;
				//print_r($pt_data['taxonomies']); exit;
				if ( !empty($pt_data['taxonomies']) && is_array($pt_data['taxonomies']) && in_array('category', $pt_data['taxonomies']) )
				{
					$def[$node_id]['value'] = 'category';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			elseif ( $node_id == 'taxonomy_tags' )
			{	
				//print $node_id; exit;
				//print_r($pt_data['taxonomies']); exit;
				if ( !empty($pt_data['taxonomies']) && is_array($pt_data['taxonomies']) && in_array('post_tag', $pt_data['taxonomies']) )
				{
					$def[$node_id]['value'] = 'post_tag';
				}
				else
				{
					$def[$node_id]['value'] = '';
				}				
			}
			
			// Labels: Handles all arguments to the $labels_array
			elseif ( array_key_exists($node_id, $labels_array) )
			{
				$v = $labels_array[$node_id];
				if ( !empty($pt_data['labels'][$v]) )
				{
					$def[$node_id]['value'] = $pt_data['labels'][$v];
				}
				else
				{
					$def[$node_id]['value'] = '';
				}
			}
			
			else
			{
				$field_name = $def[$node_id]['name'];
				$def[$node_id]['value'] = self::_get_value($pt_data,$field_name);			
			}
		}
		
		return $def;
			
	}
	
	/*------------------------------------------------------------------------------
	SYNOPSIS: Check for errors: ensure that $post_type is a valid post_type name.
	INPUT: 
		$post_type (str) name of the post type
		$new (boolean) whether or not this is validating a new post_type or an updated one
			(the only way that an update would fail would be if someone somehow POST'ed 
			against this form)
	OUTPUT: null if there are no errors, otherwise return a string describing an error.
	------------------------------------------------------------------------------*/
	private static function _post_type_name_has_errors($post_type, $new=false)
	{
		$errors = null;
		
		$taxonomy_names_array = get_taxonomies('','names');

		if ( empty($post_type) )
		{
			return __('Name is required.', CCTM_TXTDOMAIN);
		}

		foreach ( self::$reserved_prefixes as $rp )
		{
			if ( preg_match('/^'.preg_quote($rp).'.*/', $post_type) )
			{
				return sprintf( __('The post type name cannot begin with %s because that is a reserved prefix.', CCTM_TXTDOMAIN)
					, $rp);
			}		
		}

		
		// Is reserved name?
		if ( in_array($post_type, self::$reserved_post_types) )
		{
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is a reserved name.', CCTM_TXTDOMAIN )
				, '<strong>'.$post_type.'</strong>' );
			return $msg;
		}
		// Make sure the post-type name does not conflict with any registered taxonomies
		elseif ( in_array( $post_type, $taxonomy_names_array) )
		{
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is already in use as a registered taxonomy name.', CCTM_TXTDOMAIN)
				, $post_type );
		}
		// If this is a new post_type or if the $post_type name has been changed, 
		// ensure that it is not going to overwrite an existing post type name.
		else
		{
			$data = get_option( self::db_key, array() );
			if ( $new && in_array($post_type, array_keys($data) ) )
			{
				return __('That name is already in use.');
			}
		}

		return; // no errors
	}
	
		
	/*------------------------------------------------------------------------------
	Every form element when creating a new post type must be filtered here.
	INPUT: unsanitized $_POST data.
	OUTPUT: filtered data.  Only white-listed values are passed thru to output.
	
	Problems with:
		hierarchical
		rewrite_with_front
	
	This is janky... sorta doesn't work how it's supposed when combined with _save_post_type_settings().
	------------------------------------------------------------------------------*/
	private static function _sanitize_post_type_def($raw)
	{
		//print_r($raw); exit;
		$sanitized = array();

		// This will be empty if none of the "supports" items are checked.
		if (!empty($raw['supports']) )
		{
			$sanitized['supports'] = $raw['supports'];
		}
		else
		{
			$sanitized['supports'] = array();
		}
		
		if (!empty($raw['taxonomies']) )
		{
			$sanitized['taxonomies'] = $raw['taxonomies'];
		}
		else
		{
			// do this so this will take precedence when you merge the existing array with the new one in the _save_post_type_settings() function.
			$sanitized['taxonomies'] = array(); 
		}
		// You gotta unset these if you want the arrays to passed unmolested.
		unset($raw['supports']); 
		unset($raw['taxonomies']); 
		
 		// Temporary thing...
 		unset($sanitized['rewrite_slug']);
 		unset($sanitized['rewrite_with_front']);
 		
 
 		
 		// The main event
		// We grab everything, then override specific $keys as needed. 
		foreach ($raw as $key => $value )
		{
			if ( !preg_match('/^_.*/', $key) )
			{
				$sanitized[$key] = self::_get_value($raw, $key);
			}
		}
		
		// Specific overrides below:
		// post_type is the only required field
		$sanitized['post_type'] = self::_get_value($raw,'post_type');
		$sanitized['post_type'] = strtolower($sanitized['post_type']);
		$sanitized['post_type'] = preg_replace('/[^a-z|_]/', '_', $sanitized['post_type']);
		$sanitized['post_type'] = substr($sanitized['post_type'], 0, 20);

		// Our form passes integers and strings, but WP req's literal booleans,
		// so we do some type-casting here to ensure literal booleans.	
		$sanitized['show_ui'] 				= (bool) self::_get_value($raw,'show_ui');
		$sanitized['public'] 				= (bool) self::_get_value($raw,'public');
		$sanitized['show_in_nav_menus'] 	= (bool) self::_get_value($raw,'show_in_nav_menus');
		$sanitized['can_export'] 			= (bool) self::_get_value($raw,'can_export');
		$sanitized['use_default_menu_icon'] = (bool) self::_get_value($raw,'use_default_menu_icon');
		$sanitized['hierarchical'] 			= (bool) self::_get_value($raw,'hierarchical'); 
				
		// *facepalm*... Special handling req'd here for menu_position because 0 
		// is handled differently than a literal null.
		if ( (int) self::_get_value($raw,'menu_position') )
		{
			$sanitized['menu_position']	= (int) self::_get_value($raw,'menu_position',null);
		}
		else
		{
			$sanitized['menu_position']	= null;
		}
		
		// menu_icon... the user will lose any custom Menu Icon URL if they save with this checked!
		// TODO: let this value persist.
		if( $sanitized['use_default_menu_icon'] )
		{
			unset($sanitized['menu_icon']);	// === null;
		}
		
		if (empty($sanitized['query_var']))
		{
			$sanitized['query_var'] = false;
		}
		
		// Rewrites. TODO: make this work like the built-in post-type permalinks
		switch ($sanitized['permalink_action'])
		{
			case '/%postname%/':
				$sanitized['rewrite'] = true;
				break;
			case 'Custom':
				$sanitized['rewrite']['slug'] = $raw['rewrite_slug'];
				$sanitized['rewrite']['with_front'] = (bool) $raw['rewrite_with_front'];
				break;
			case 'Off':
			default:
				$sanitized['rewrite'] = false;
		}
		//print_r($sanitized); exit;
		return $sanitized;
	}

	/*------------------------------------------------------------------------------
	INPUT: $def (mixed) associative array describing a single post-type.
	OUTPUT: none; this saves a serialized data structure (arrays of arrays) to the db
	------------------------------------------------------------------------------*/
	private static function _save_post_type_settings($def)
	{
		
		$key = $def['post_type'];
		$all_post_types = get_option( self::db_key, array() );
		// Update existing settings if this post-type has already been added
		if ( isset($all_post_types[$key]) )
		{
			$all_post_types[$key] = array_merge($all_post_types[$key], $def);	
		}
		// OR, create a new node in the data structure for our new post-type
		else
		{
			$all_post_types[$key] = $def;
		}
		if ($all_post_types[$key]['use_default_menu_icon'])
		{
			unset($all_post_types[$key]['menu_icon']);
		}
		//print_r($def); exit;
		//print_r($_POST); exit;
		//print_r($all_post_types[$key]); exit;
		
		update_option( self::db_key, $all_post_types );
	}

	/*------------------------------------------------------------------------------
	This defines the form required to define a custom field. Note that names imply 
	arrays, e.g. name="custom_fields[3][label]".
	This is intentional: since all custom field definitions are stored as a serialized
	array in the wp_options table, we have to treat all defs as a kind of recordset
	(i.e. an array of similar hashes).
	 
	[+def_i+] gets used by Javascript for on-the-fly adding of form fields (where
	def_i is a Javascript variable indicating the definition number (or i for integer).
	------------------------------------------------------------------------------*/
	private static function _set_custom_field_def_template()
	{
		$def['label']['name']			= 'custom_fields[[+def_i+]][label]';
		$def['label']['label']			= __('Label', CCTM_TXTDOMAIN);
		$def['label']['value']			= '';
		$def['label']['extra']			= '';			
		$def['label']['description']	= '';
		$def['label']['type']			= 'text';
		$def['label']['sort_param']		= 1;

		$def['name']['name']			= 'custom_fields[[+def_i+]][name]';
		$def['name']['label']			= __('Name', CCTM_TXTDOMAIN);
		$def['name']['value']			= '';
		$def['name']['extra']			= '';			
		$def['name']['description']		= __('The name identifies the option_name in the wp_postmeta database table. You will use this name in your template functions to identify this custom field.', CCTM_TXTDOMAIN);
		$def['name']['type']			= 'text';
		$def['name']['sort_param']		= 2;

		$def['description']['name']			= 'custom_fields[[+def_i+]][description]';
		$def['description']['label']		= __('Description',CCTM_TXTDOMAIN);
		$def['description']['value']		= '';
		$def['description']['extra']		= '';
		$def['description']['description']	= '';
		$def['description']['type']			= 'textarea';
		$def['description']['sort_param']	= 3;

		$def['type']['name']		= 'custom_fields[[+def_i+]][type]';
		$def['type']['label']		= __('Input Type', CCTM_TXTDOMAIN);
		$def['type']['value']		= 'text';
		$def['type']['extra']		= ' onchange="javascript:addRemoveDropdown(this.parentNode.id,this.value, [+def_i+])"';
		$def['type']['description']	= '';
		$def['type']['type']		= 'dropdown';
		$def['type']['options']		= array('checkbox','dropdown','image','media','relation','text','textarea','wysiwyg');
		$def['type']['sort_param']	= 4;

		$def['default_value']['name']			= 'custom_fields[[+def_i+]][default_value]';
		$def['default_value']['label']			= __('Default Value', CCTM_TXTDOMAIN);
		$def['default_value']['value']			= '';
		$def['default_value']['extra']			= '';
		$def['default_value']['description']		= __('The default value will appear in form fields when a post is first created. For checkboxes, use a default value of "1" if you want it to be checked by default.', CCTM_TXTDOMAIN);
		$def['default_value']['type']			= 'text';
		$def['default_value']['sort_param']		= 5;


		$def['sort_param']['name']			= 'custom_fields[[+def_i+]][sort_param]';
		$def['sort_param']['label']			= __('Sort Order',CCTM_TXTDOMAIN);
		$def['sort_param']['value']			= '';
		$def['sort_param']['extra']			= ' size="2" maxlength="4"';
		$def['sort_param']['description']	= __('This controls where this field will appear on the page. Fields with smaller numbers will appear higher on the page.',CCTM_TXTDOMAIN);
		$def['sort_param']['type']			= 'text';
		$def['sort_param']['sort_param']	= 6;


		self::$custom_field_def_template = $def;
	}

	/*------------------------------------------------------------------------------
	Used when creating or editing Post Types	
	I had to put this here in a function rather than in a config file so I could
	take advantage of the WP translation functions __()
	------------------------------------------------------------------------------*/
	private static function _set_post_type_form_definition($post_type_label='sample_post_type')
	{
		$def =	array();
		include('form_defs/post_type.php');	
		self::$post_type_form_definition = $def;
	}


	/*------------------------------------------------------------------------------
	"Transformation" here refers to the reflexive mapping that is required to create
	a form definition that will generate a form that allows users to define a definition.
	In other words, given a form definition, calculate the definition the allows you
	to edit that definition.
	
	The custom_fields array consists of form element definitions that are used when
	editing or creating a new post.  When we want to edit that definition, 
	we have to create new form elements that allow us to edit each part of the 
	original definition, e.g. we need a text element to allow us to edit 
	the "label", we need a textarea element to allow us to edit the "description",
	etc.

	INPUT: $field_def (mixed) a single custom field definition.  Something like:
	
		Array
		(
            [label] => Rating
            [name] => rating
            [description] => MPAA rating
            [type] => dropdown
            [options] => Array
                (
                    [0] => PG
                    [1] => PG-13
                    [2] => R
                )

            [sort_param] => 		
		)
		
	OUTPUT: a modified version of the $custom_field_def_template, with values updated 
	based on the incoming $field_def.  The 'options' are handled in a special way: 
	they are moved to the 'special' key -- this causes the FormGenerator to generate 
	text fields for each one so the user can edit the options for their dropdown.
	------------------------------------------------------------------------------*/
	private static function _transform_data_structure_for_editing($field_def)
	{
		// Used to collect all 5 translated field definitions for this $field_def
		$translated_defs = array();
		
		// Copying over all elments from the self::$custom_field_def_template
		foreach ( $field_def as $attr => $val )
		{	
			// Is this $attr an editable item for which we must generate a form element?
			if (isset(self::$custom_field_def_template[$attr]) )
			{
				foreach (self::$custom_field_def_template[$attr] as $key => $v )
				{
					$translated_defs[$attr][$key] = self::$custom_field_def_template[$attr][$key];
				}					
			}
			// Special handling: 'options' really belong to 'type', e.g. spec. opts for a dropdown
			elseif ( $attr == 'options' && !empty($val) )
			{
				$translated_defs['type']['special'] = $val;
			}
		}
		
		// Populate the new form definitions with their values from the original
		foreach ( $translated_defs as $field => &$def )
		{
			if ( isset($field_def[$field]))
			{
				$def['value'] = $field_def[$field];
			}
			else
			{
				$def['value'] = '';
			}
			// Associate the group of new elements back to this definition.
			$def['def_i'] = self::$def_i; 
		}
		return $translated_defs;
	}	


	
	//! Public Functions
	/*------------------------------------------------------------------------------
	Load CSS and JS for admin folks in the manager.  Note that we have to verbosely 
	ensure that thickbox css and js are loaded: normally they are tied to the 
	"main content" area of the content type, so thickbox would otherwise fail
	if your custom post_type doesn't use the main content type.
	Errors: TO-DO. 
	------------------------------------------------------------------------------*/
	public static function admin_init()
	{
		
		
    	load_plugin_textdomain( CCTM_TXTDOMAIN, '', CCTM_PATH );
	
		// Set our form defs in this, our makeshift constructor.
		//self::_set_post_type_form_definition();
		self::_set_custom_field_def_template();
		
		// TODO: $E = new WP_Error();
		wp_register_style('CCTM_css'
			, CCTM_URL . '/css/manager.css');
		wp_enqueue_style('CCTM_css');
		// Hand-holding: If your custom post-types omit the main content block, 
		// then thickbox will not be queued.
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_style( 'jquery-ui-tabs', CCTM_URL . '/css/custom-theme/jquery-ui-1.8.10.custom.css');
		wp_enqueue_script( 'jquery-ui-tabs');
	}
	
	/*------------------------------------------------------------------------------
	Adds a link to the settings directly from the plugins page.  This filter is 
	called for each plugin, so we need to make sure we only alter the links that
	are displayed for THIS plugin.
	
	INPUT (determined by WordPress):
		$links is a hash of links to display in the format of name => translation e.g.
			array('deactivate' => 'Deactivate')
		$file is the path to plugin's main file (the one with the info header), 
			relative to the plugins directory, e.g. 'custom-content-type-manager/index.php'
	OUTPUT: $links array.
	------------------------------------------------------------------------------*/
	public static function add_plugin_settings_link($links, $file)
	{
		if ( $file == basename(self::get_basepath()) . '/index.php' ) 
		{
			$settings_link = sprintf('<a href="%s">%s</a>'
				, admin_url( 'options-general.php?page='.self::admin_menu_slug )
				, __('Settings')
			);
			array_unshift( $links, $settings_link );
		}

		return $links;
	}
	
	//------------------------------------------------------------------------------
	// Defines the diretory for this plugin.
	public static function get_basepath(){
		return dirname(dirname(__FILE__));
	}
	 
	//------------------------------------------------------------------------------
	/**
	* Get the flash message.
	*/
	public static function get_flash()
	{
		$output = '';
		if ( isset($_SESSION[self::db_key]) )
		{
			$output = $_SESSION[self::db_key];
		}
		unset( $_SESSION[self::db_key] );
		return $output;
	}
	/*------------------------------------------------------------------------------
	Create custom post-type menu
	------------------------------------------------------------------------------*/
	public static function create_admin_menu()
	 {
		add_menu_page(
			'Custom Content Types',					// page title
			'Custom Content Types',	 				// menu title
			'manage_options', 						// capability
			self::admin_menu_slug, 					// menu-slug (should be unique)
			'CCTM::page_main_controller',			// callback function
			CCTM_URL .'/images/gear.png',			// Icon
			71
		);
	}
	
	/*------------------------------------------------------------------------------
	Checks whether or not a given post-type is active with its custom fields standardized..
	INPUT: $content_type (str) name of a post type.
	OUTPUT: true|false
	------------------------------------------------------------------------------*/
	public static function is_active_post_type($content_type)
	{
		$data = get_option( self::db_key );
		if ( isset($data[$content_type]['is_active']) && $data[$content_type]['is_active'] == 1 )
		{
			return true;
		}		
		else
		{
			return false;
		}
	}

		
	/*------------------------------------------------------------------------------
	This is the function called when someone clicks on the settings page.  
	The job of a controller is to process requests and route them.
	------------------------------------------------------------------------------*/
	public static function page_main_controller() 
	{
		if (!current_user_can('manage_options'))  
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		$action 		= (int) self::_get_value($_GET,self::action_param,0);
		$post_type 		= self::_get_value($_GET,self::post_type_param);
		
		switch($action)
		{
			case 1: // create new custom post type
				self::_page_create_new_post_type();
				break;
			case 2: // update existing custom post type. Override form def.
				self::$post_type_form_definition['post_type']['type'] = 'readonly';
				self::$post_type_form_definition['post_type']['description'] = __('The name of the post-type cannot be changed. The name may show up in your URLs, e.g. ?movie=star-wars. This will also make a new theme file available, starting with prefix named "single-", e.g. <strong>single-movie.php</strong>.',CCTM_TXTDOMAIN);
				self::_page_edit_post_type($post_type);
				break;
			case 3: // delete existing custom post type
				self::_page_delete_post_type($post_type);
				break;
			case 4: // Manage Custom Fields for existing post type	
				self::_page_manage_custom_fields($post_type);
				break;
			case 5: // TODO: Manage Taxonomies for existing post type
				break;
			case 6: // Activate post type
				self::_page_activate_post_type($post_type);
				break;
			case 7: // Deactivate post type
				self::_page_deactivate_post_type($post_type);
				break;
			case 8: // Show an example of custom field template
				self::_page_sample_template($post_type);
				break;
			default: // Default: List all post types	
				self::_page_show_all_post_types();
		}
	}	
	
	/*------------------------------------------------------------------------------
	Print errors if they were thrown by the tests. Currently this is triggered as 
	an admin notice so as not to disrupt front-end user access, but if there's an
	error, you should fix it! The plugin may behave erratically!
	INPUT: none... ideally I'd pass this a value, but the WP interface doesn't make
		this easy, so instead I just read the class variable: CCTMtests::$errors
	OUTPUT: none directly.  But errors are printed if present.
	------------------------------------------------------------------------------*/
	public static function print_notices()
	{
		if ( !empty(CCTMtests::$errors) )
		{
			$error_items = '';
			foreach ( CCTMtests::$errors as $e )
			{
				$error_items .= "<li>$e</li>";
			}
			$msg = sprintf( __('The %s plugin encountered errors! It cannot load!', CCTM_TXTDOMAIN)
				, CCTM::name);
			printf('<div id="custom-post-type-manager-warning" class="error">
				<p>
					<strong>%1$s</strong>
					<ul style="margin-left:30px;">
						%2$s
					</ul>
				</p>
				</div>'
				, $msg
				, $error_items);
		}
	}
	
	
	/*------------------------------------------------------------------------------
	Register custom post-types, one by one. Data is stored in the wp_options table
	in a structure that matches exactly what the register_post_type() function
	expectes as arguments.
	
	See:
	http://codex.wordpress.org/Function_Reference/register_post_type
	
	See wp-includes/posts.php for examples of how WP registers the default post types
	
	$def = Array
	(
	    'supports' => Array
	        (
	            'title',
	            'editor'
	        ),
	
	    'post_type' => 'book',
	    'singular_label' => 'Book',
	    'label' => 'Books',
	    'description' => 'What I&#039;m reading',
	    'show_ui' => 1,
	    'capability_type' => 'post',
	    'public' => 1,
	    'menu_position' => '10',
	    'menu_icon' => '', 
	    'custom_content_type_mgr_create_new_content_type_nonce' => 'd385da6ba3',
	    'Submit' => 'Create New Content Type',
	    'show_in_nav_menus' => '', 
	    'can_export' => '', 
	    'is_active' => 1,
	);

	FUTURE:
		register_taxonomy( $post_type,
			$cpt_post_types,
			array( 'hierarchical' => get_disp_boolean($cpt_tax_type["hierarchical"]),
			'label' => $cpt_label,
			'show_ui' => get_disp_boolean($cpt_tax_type["show_ui"]),
			'query_var' => get_disp_boolean($cpt_tax_type["query_var"]),
			'rewrite' => array('slug' => $cpt_rewrite_slug),
			'singular_label' => $cpt_singular_label,
			'labels' => $cpt_labels
		) );
	------------------------------------------------------------------------------*/
	public static function register_custom_post_types() 
	{	

		$data = get_option( self::db_key, array() );
		foreach ($data as $post_type => $def) 
		{
			if ( isset($def['is_active']) 
				&& !empty($def['is_active']) 
				&& !in_array($post_type, self::$built_in_post_types)) 
			{	
	#			print_r($def); exit;
				register_post_type( $post_type, $def );
				// TODO: make global setting that asks whether or not the user wants us to do this automatically
				//if ( is_array($def['supports']) && in_array('thumbnail', $def['supports']) )
				//{
					/* This generates a warning:
					Warning: in_array() [function.in-array]: Wrong datatype for second argument in /Users/everett2/Sites/pretasurf/html/blog/wp-includes/theme.php on line 1671 */
					// add_theme_support( 'post-thumbnails', $post_type );
				//}
			}
		}
	
	}
	
	
	//------------------------------------------------------------------------------
	/**
	* Sets a flash message that's viewable only for the next page view (for the current user)
	*/
	public static function set_flash($msg)
	{
		$_SESSION[ self::db_key ] = $msg;
	}

}
/*EOF*/