<?php
/*------------------------------------------------------------------------------
CCTM = Custom Content Type Manager

This is the main class for the Custom Content Type Manager plugin.
It holds its functions hooked to WP events and utilty functions.

Homepage:
http://code.google.com/p/wordpress-custom-content-type-manager/

It is largely static classes

This class handles the creation and management of custom post-types (also
referred to as 'content-types'). 
------------------------------------------------------------------------------*/
class CCTM {
	// Name of this plugin and version data.
	// See http://php.net/manual/en/function.version-compare.php:
	// any string not found in this list < dev < alpha =a < beta = b < RC = rc < # < pl = p
	const name   = 'Custom Content Type Manager';
	const version = '0.9.4';
	const version_meta = 'dev'; // dev, rc (release candidate), pl (public release)
	
	
	// Required versions (referenced in the CCTMtest class).
	const wp_req_ver  = '3.0.1';
	const php_req_ver  = '5.2.6';
	const mysql_req_ver = '4.1.2';
	
	/**
	 * The following constants identify the option_name in the wp_options table
	 * where this plugin stores various data.
	 */	 
	const db_key  = 'cctm_data';

	// These parameters identify where in the $_GET array we can find the values
	// and how URLs are constructed, e.g. some-admin-page.php?a=123&pt=xyz
	const action_param    = 'a';
	const post_type_param   = 'pt';

	// Each class that extends the CCTMFormElement class must prefix this to its class name.
	const FormElement_classname_prefix = 'CCTM_';

	// used to control the uploading of the .cctm.json files
	const max_def_file_size = 524288; // in bytes
	
	// Directory relative to wp-content/uploads/ where we can store def files
	// Omit the trailing slash.
	const base_storage_dir = 'cctm';
	
	// Directory relative to wp-content/uploads/{self::base_storage_dir} used to store 
	// the .cctm.json definition files. Omit the trailing slash.
	const def_dir = 'defs';

	// Default permissions for dirs/files created in the base_storage_dir.
	// These cannot be more permissive thant the system's settings: the system
	// will automatically shave them down. E.g. if the system has a global setting
	// of 0755, a local setting here of 0770 gets bumped down to 0750.
	const new_dir_perms = 0755;
	const new_file_perms = 0644;

	// Used to filter inputs (e.g. descriptions)
	public static $allowed_html_tags = '<a><strong><em><code><style>';
		
	// Data object stored in the wp_options table representing all primary data
	// for post_types and custom fields
	public static $data = array();
	
	// integer iterator used to uniquely identify groups of field definitions for
	// CSS and $_POST variables
	public static $def_i = 0;

	// This is the definition shown when a user first creates a post_type
	public static $default_post_type_def = array
		(
		    'supports' => array('title', 'editor'),
		    'taxonomies' => array(),
		    'post_type' => '',
		    'labels' => array
		        (
		            'menu_name' => '',
		            'singular_name' => '',
		            'add_new' => '',
		            'add_new_item' => '',
		            'edit_item' => '',
		            'new_item' => '',
		            'view_item' => '',
		            'search_items' => '',
		            'not_found' => '',
		            'not_found_in_trash' => '',
		            'parent_item_colon' => '',
		        ),
		    'description' => '',
		    'show_ui' => 1,
		    'public' => 1,
		    'menu_icon' => '',
		    'label' => '',
		    'menu_position' => '',
		    'rewrite_with_front' => 1,
		    'permalink_action' => 'Off',
		    'rewrite_slug' => '',
		    'query_var' => '',
		    'capability_type' => 'post',
		    'show_in_nav_menus' => 1,
		    'can_export' => 1,
		    'use_default_menu_icon' => 1,
		    'hierarchical' => 0,
		    'rewrite' => '',
		    'has_archive' => 0
		);


	// Where are the icons for custom images stored?
	// TODO: let the users select their own dir in their own directory
	public static $custom_field_icons_dir;

	// Built-in post-types that can have custom fields, but cannot be deleted.
	public static $built_in_post_types = array('post', 'page');

	// Names that are off-limits for custom post types b/c they're already used by WP
	public static $reserved_post_types = array('post', 'page', 'attachment', 'revision'
		, 'nav_menu', 'nav_menu_item');

	// Custom field names are not allowed to use the same names as any column in wp_posts
	public static $reserved_field_names = array('ID', 'post_author', 'post_date', 'post_date_gmt',
		'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status',
		'post_password', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt',
		'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type',
		'comment_count');

	// Future-proofing: post-type names cannot begin with 'wp_'
	// See: http://codex.wordpress.org/Custom_Post_Types
	// FUTURE: List any other reserved prefixes here (if any)
	public static $reserved_prefixes = array('wp_');

	/**
	 * Warnings are stored as a simple array of text strings, e.g. 'You spilled your coffee!'
	 * Whether or not they are displayed is determined by checking against the self::$data['warnings']
	 * array: the text of the warning is hashed and this is used as a key to identify each warning.
	 */
	public static $warnings = array();
	
	public static $errors; // used to store validation errors


	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * This formats any errors registered in the class $errors array. The errors 
	 * take this format: self::$errors['field_name'] = 'Description of error';
	 * 
	 * @return	string	(empty string if no errors)
	 */
	private static function _format_errors() {
		$error_str = '';
		if ( empty ( self::$errors ) ) {
			return '';
		}
		
		foreach ( self::$errors as $e ) {
			$error_str .= '<li>'.$e.'</li>
			';	
		}

		return sprintf('<div class="error">
			<p><strong>%1$s</strong></p>
			<ul style="margin-left:30px">
				%2$s
			</ul>
			</div>'
			, __('Please correct the following errors:', CCTM_TXTDOMAIN)
			, $error_str
		);
	}

	//------------------------------------------------------------------------------
	/**
	 * This allows us to dynamically change the field classes in our forms.
	 * Normally, the output is only 'cctm_text', but if there is an error 
	 * in self::$errors[$fieldname], then the class becomes 
	 * 'cctm_text cctm_error'.
	 */
	private static function _get_class($fieldname, $fieldtype='text') {
		if ( isset(self::$errors[$fieldname]) ) {
			return "cctm_$fieldtype cctm_error";
		}
		else {
			return "cctm_$fieldtype";
		}
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @return string representing all img tags for all post-type icons
	 */
	private static function _get_post_type_icons() {

		$icons = array();
		if ($handle = opendir(CCTM_PATH.'/images/icons')) {
			while (false !== ($file = readdir($handle))) {
				if ( !preg_match('/^\./', $file) ) {
					$icons[] = $file;
				}
			}
			closedir($handle);
		}

		$output = '';
		$tpl = CCTM_PATH.'/tpls/settings/icon.tpl';
		$tpl = file_get_contents($tpl);

		foreach ( $icons as $img ) {
			$output .= self::parse($tpl, array('title'=> $img, 'src'=> CCTM_URL.'/images/icons/'.$img) );
		}

		return $output;
	}

	//------------------------------------------------------------------------------
	/**
	 * SYNOPSIS: checks the custom content data array to see $post_type exists as one 
	 * of CCTM's defined post types (it doesn't check against post types defined 
	 *	elsewhwere).
	 *	
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DataStructures
	 *
	 * Built-in post types 'page' and 'post' are considered valid (i.e. existing) by
	 * default, even if they haven't been explicitly defined for use by this plugin
	 * so long as the 2nd argument, $search_built_ins, is not overridden to false.
	 * We do this because sometimes we need to consider posts and pages, and other times
	 * not.
	 *
	 * @param string $post_type	the lowercase database slug identifying a post type.
	 * @param boolean $search_built_ins (optional) whether or not to search inside the
			$built_in_post_types array.
	 * @return boolean indicating whether this is a valid post-type
	 */
	private static function _is_existing_post_type($post_type, $search_built_ins=true) {
	
		// If there is no existing data, check against the built-ins
		if ( empty(self::$data['post_type_defs']) && $search_built_ins ) {
			return in_array($post_type, self::$built_in_post_types);
		}
		// If there's no existing $data and we omit the built-ins...
		elseif ( empty(self::$data['post_type_defs']) && !$search_built_ins ) {
			return false;
		}
		// Check to see if we've stored this $post_type before
		elseif ( array_key_exists($post_type, self::$data['post_type_defs']) ) {
			return true;
		}
		// Check the built-ins
		elseif ( $search_built_ins && in_array($post_type, self::$built_in_post_types) ) {
			return true;
		}
		else {
			return false;
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Check for errors: ensure that $post_type is a valid post_type name.
	 *
	 * @param mixed 	$data describes a post type (this will be input to the register_post_type() function
	 * @param boolean 	$new  (optional) whether or not the post_type is new (default=false)
	 * @return mixed 	returns null if there are no errors, otherwise returns a string describing an error.
	 */
	private static function _post_type_name_has_errors($data, $new=false) {
	
		$errors = null;

		$taxonomy_names_array = get_taxonomies('', 'names');

		if ( empty($data['post_type']) ) {
			return __('Name is required.', CCTM_TXTDOMAIN);
		}
		if ( empty($data['labels']['menu_name'])) // remember: the location in the $_POST array is different from the name of the option in the form-def.
			{
			return __('Menu Name is required.', CCTM_TXTDOMAIN);
		}

		foreach ( self::$reserved_prefixes as $rp ) {
			if ( preg_match('/^'.preg_quote($rp).'.*/', $data['post_type']) ) {
				return sprintf( __('The post type name cannot begin with %s because that is a reserved prefix.', CCTM_TXTDOMAIN)
					, $rp);
			}
		}

		$registered_post_types = get_post_types();
		$cctm_post_types = array_keys(self::$data['post_type_defs']);
		$other_post_types = array_diff($registered_post_types, $cctm_post_types);
		$other_post_types = array_diff($other_post_types, self::$reserved_post_types);
		
		// Is reserved name?
		if ( in_array($data['post_type'], self::$reserved_post_types) ) {
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is a reserved name.', CCTM_TXTDOMAIN )
				, '<strong>'.$post_type.'</strong>' );
			return $msg;
		}
		// Make sure the post-type name does not conflict with any registered taxonomies
		elseif ( in_array( $data['post_type'], $taxonomy_names_array) ) {
			$msg = __('Please choose another name.', CCTM_TXTDOMAIN );
			$msg .= ' ';
			$msg .= sprintf( __('%s is already in use as a registered taxonomy name.', CCTM_TXTDOMAIN)
				, $post_type );
			return $msg;
		}
		// If this is a new post_type or if the $post_type name has been changed,
		// ensure that it is not going to overwrite an existing post type name.
		elseif ( $new && is_array(self::$data['post_type_defs']) && in_array($data['post_type'], $cctm_post_types ) ) {
			return sprintf( __('The name %s is already in use.', CCTM_TXTDOMAIN), htmlentities($data['post_type']) );
		}
		// Is the name taken by an existing post type registered by some other plugin?
		elseif (in_array($data['post_type'], $other_post_types) ) {
			return sprintf( __('The name %s has been registered by some other plugin.', CCTM_TXTDOMAIN), htmlentities($data['post_type']) );
		}
		// Make sure there's not an unsuspecting theme file named single-my_post_type.php
/*
		$dir = get_stylesheet_directory();
		if ( file_exists($dir . '/single-'.$data['post_type'].'.php')) {
			return sprintf( __('There is a template file named single-%s.php in your theme directory (%s).', CCTM_TXTDOMAIN)
				, htmlentities($data['post_type']) 
				, get_stylesheet_directory());
		}
*/
		
		return; // no errors
	}

	//------------------------------------------------------------------------------
	/**
	 * Sanitize posted data for a clean export.  This just ensures that the user 
	 * has entered some info about what they are about to export.
	 *
	 * @param	mixed	$raw = $_POST data
	 * @return	mixed	sanitized post data
	 */
	private static function _sanitize_export_params($raw) {
		$sanitized = array();
		// title
		if ( empty($raw['title'])) {
			self::$errors['title'] = __('Title is required.', CCTM_TXTDOMAIN);
		}
		elseif ( preg_match('/[^a-z\s\-_0-9]/i', $raw['title']) ) {
			self::$errors['title'] = __('Only basic text characters are allowed.', CCTM_TXTDOMAIN);
		}
		elseif ( strlen($raw['title'] > 64) ) {
			self::$errors['title'] = __('The title cannot exceed 64 characters.', CCTM_TXTDOMAIN);
		}
		
		// author
		if ( empty($raw['author'])) {
			self::$errors['author'] = __('Author is required.', CCTM_TXTDOMAIN);
		}
		elseif ( preg_match('/[^a-z\s\-_0-9]/i', $raw['author']) ) {
			self::$errors['author'] = __('Only basic text characters are allowed.', CCTM_TXTDOMAIN);
		}
		elseif ( strlen($raw['author'] > 64) ) {
			self::$errors['author'] = __('The author name cannot exceed 32 characters.', CCTM_TXTDOMAIN);
		}
		
		if ( empty($raw['url'])) {
			$raw['url'] = site_url(); // defaults to this site
		}
		elseif ( !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $raw['url']) ) {
			self::$errors['url'] = __('The URL must be in a standard format, e.g. http://yoursite.com.', CCTM_TXTDOMAIN);
		}
		elseif ( strlen($raw['url'] > 255) ) {
			self::$errors['url'] = __('The URL cannot exceed 255 characters.', CCTM_TXTDOMAIN);
		}
		
		if ( empty($raw['description'])) {
			self::$errors['description'] = __('Description is required.', CCTM_TXTDOMAIN);
		}
		elseif ( strlen($raw['description'] > 1024) ) {
			self::$errors['description'] = __('The description cannot exceed 1024 characters.', CCTM_TXTDOMAIN);
		}

		$sanitized['title'] = htmlentities( substr( preg_replace('/[^a-z\s\-_0-9]/i', '', trim($raw['title']) ), 0, 64) );
		$sanitized['author'] = htmlentities( substr( preg_replace('/[^a-z\s\-_0-9]/i', '', trim($raw['author']) ), 0, 64) );
		$sanitized['url'] = htmlentities( substr( trim($raw['url']), 0, 255) );
		$sanitized['description'] = htmlentities( substr( strip_tags( trim($raw['description']) ), 0, 1024) );
		
		return $sanitized;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Every form element when creating a new post type must be filtered here.
	 * 
	 * Problems with:
	 * 	hierarchical
	 * 	rewrite_with_front
	 * 
	 * This is janky... sorta doesn't work how it's supposed when combined with _save_post_type_settings().
	 *
	 *
	 * @param mixed $raw unsanitized $_POST data
	 * @return mixed filtered $_POST data (only white-listed are passed thru to output)
	 */
	private static function _sanitize_post_type_def($raw) {

		unset($raw['custom_content_type_mgr_create_new_content_type_nonce']);
		unset($raw['custom_content_type_mgr_edit_content_type_nonce']);
		
		
		$raw = CCTM::striptags_deep(($raw));

		// WP always adds slashes: see http://kovshenin.com/archives/wordpress-and-magic-quotes/
		$raw = CCTM::stripslashes_deep(($raw));
		
		$sanitized = array();
		// Handle unchecked checkboxes
		if ( empty($raw['cctm_hierarchical_custom'])) {
			$sanitized['cctm_hierarchical_custom'] = '';
		}
		if ( empty($raw['cctm_hierarchical_includes_drafts'])) {
			$sanitized['cctm_hierarchical_includes_drafts'] = '';
		}
		if ( empty($raw['cctm_hierarchical_post_types'])) {
			$sanitized['cctm_hierarchical_post_types'] = array();
		}
		
		// This will be empty if no "supports" items are checked.
		if (!empty($raw['supports']) ) {
			$sanitized['supports'] = $raw['supports'];
			unset($raw['supports']);
		}
		else {
			$sanitized['supports'] = array();
		}

		if (!empty($raw['taxonomies']) ) {
			$sanitized['taxonomies'] = $raw['taxonomies'];
		}
		else {
			// do this so this will take precedence when you merge the existing array with the new one in the _save_post_type_settings() function.
			$sanitized['taxonomies'] = array();
		}
		// You gotta unset these if you want the arrays to passed unmolested.
		unset($raw['taxonomies']);

		// Temporary thing...
		unset($sanitized['rewrite_slug']);
//		unset($sanitized['rewrite_with_front']);

		// The main event
		// We grab everything except stuff that begins with '_', then override specific $keys as needed.
		foreach ($raw as $key => $value ) {
			if ( !preg_match('/^_.*/', $key) ) {
				$sanitized[$key] = self::get_value($raw, $key);
			}
		}

		// Specific overrides below:
		// post_type is the only required field
		$sanitized['post_type'] = self::get_value($raw, 'post_type');
		$sanitized['post_type'] = strtolower($sanitized['post_type']);
		$sanitized['post_type'] = preg_replace('/[^a-z0-9|_]/', '_', $sanitized['post_type']);
		$sanitized['post_type'] = substr($sanitized['post_type'], 0, 20);

		// Our form passes integers and strings, but WP req's literal booleans,
		// so we do some type-casting here to ensure literal booleans.
		$sanitized['rewrite_with_front']     = (bool) self::get_value($raw, 'rewrite_with_front');
		$sanitized['show_ui']     = (bool) self::get_value($raw, 'show_ui');
		$sanitized['public']     = (bool) self::get_value($raw, 'public');
		$sanitized['show_in_nav_menus']  = (bool) self::get_value($raw, 'show_in_nav_menus');
		$sanitized['can_export']    = (bool) self::get_value($raw, 'can_export');
		$sanitized['use_default_menu_icon'] = (bool) self::get_value($raw, 'use_default_menu_icon');
		$sanitized['hierarchical']    = (bool) self::get_value($raw, 'hierarchical');

		if ( empty($sanitized['has_archive']) ) {
			$sanitized['has_archive'] = false;
		}
		else {
			$sanitized['has_archive'] = true;
		}
		
		// *facepalm*... Special handling req'd here for menu_position because 0
		// is handled differently than a literal null.
		if ( (int) self::get_value($raw, 'menu_position') ) {
			$sanitized['menu_position'] = (int) self::get_value($raw, 'menu_position', null);
		}
		else {
			$sanitized['menu_position'] = null;
		}

		// menu_icon... the user will lose any custom Menu Icon URL if they save with this checked!
		// TODO: let this value persist.
		if ( $sanitized['use_default_menu_icon'] ) {
			unset($sanitized['menu_icon']); // === null;
		}

		if (empty($sanitized['query_var'])) {
			$sanitized['query_var'] = false;
		}

		// Cleaning up the labels
		if ( empty($sanitized['label']) ) {
			$sanitized['label'] = $sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['singular_name']) ) {
			$sanitized['labels']['singular_name'] = $sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['add_new']) ) {
			$sanitized['labels']['add_new'] = __('Add New');
		}
		if ( empty($sanitized['labels']['add_new_item']) ) {
			$sanitized['labels']['add_new_item'] = __('Add New') . ' ' .$sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['edit_item']) ) {
			$sanitized['labels']['edit_item'] = __('Edit'). ' ' .$sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['new_item']) ) {
			$sanitized['labels']['new_item'] = __('New'). ' ' .$sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['view_item']) ) {
			$sanitized['labels']['view_item'] = __('View'). ' ' .$sanitized['post_type'];
		}
		if ( empty($sanitized['labels']['search_items']) ) {
			$sanitized['labels']['search_items'] = __('Search'). ' ' .$sanitized['labels']['menu_name'];
		}
		if ( empty($sanitized['labels']['not_found']) ) {
			$sanitized['labels']['not_found'] = sprintf( __('No %s found', CCTM_TXTDOMAIN), strtolower($raw['labels']['menu_name']) );
		}		
		if ( empty($sanitized['labels']['not_found_in_trash']) ) {
			$sanitized['labels']['not_found_in_trash'] = sprintf( __('No %s found in trash', CCTM_TXTDOMAIN), strtolower($raw['labels']['menu_name']) );
		}
		if ( empty($sanitized['labels']['parent_item_colon']) ) {
			$sanitized['labels']['parent_item_colon'] = __('Parent Page');
		}


		// Rewrites. TODO: make this work like the built-in post-type permalinks
		switch ($sanitized['permalink_action']) {
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

		return $sanitized;
	}


	//------------------------------------------------------------------------------
	/**
	 * this saves a serialized data structure (arrays of arrays) to the db
	 *
	 * @param mixed $def associative array definition describing a single post-type.
	 * @return 
	 */
	private static function _save_post_type_settings($def) {

		$key = $def['post_type'];
		// Update existing settings if this post-type has already been added
		if ( isset(self::$data['post_type_defs'][$key]) ) {
			self::$data['post_type_defs'][$key] = array_merge(self::$data['post_type_defs'][$key], $def);
		}
		// OR, create a new node in the data structure for our new post-type
		else {
			self::$data['post_type_defs'][$key] = $def;
		}
		if (self::$data['post_type_defs'][$key]['use_default_menu_icon']) {
			unset(self::$data['post_type_defs'][$key]['menu_icon']);
		}

		update_option( self::db_key, self::$data );
	}

	//------------------------------------------------------------------------------
	/**
	 * Used when creating or editing Post Types
	 * I had to put this here in a function rather than in a config file so I could
	 * take advantage of the WP translation functions __()
	 * @param string $post_type_label (optional)
	 */
	private static function _set_post_type_form_definition($post_type_label='sample_post_type') {
		$def = array();
		include 'form_defs/post_type.php';
		self::$post_type_form_definition = $def;
	}


	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	 * Load CSS and JS for admin folks in the manager.  Note that we have to verbosely
	 * ensure that thickbox's css and js are loaded: normally they are tied to the
	 * "editor" area of the content type, so thickbox would otherwise fail
	 * if your custom post_type doesn't use the main editor.
	 * See http://codex.wordpress.org/Function_Reference/wp_enqueue_script for a list
	 * of default scripts bundled with WordPress
	 */
	public static function admin_init() {

		load_plugin_textdomain( CCTM_TXTDOMAIN, false, CCTM_PATH.'/lang/' );
		
		wp_register_style('CCTM_css'
			, CCTM_URL . '/css/manager.css');
		wp_enqueue_style('CCTM_css');
		// Hand-holding: If your custom post-type omits the main content block,
		// then thickbox will not be queued and your image, reference, selectors will fail.
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );

		wp_enqueue_style( 'jquery-ui-tabs', CCTM_URL . '/css/smoothness/jquery-ui-1.8.11.custom.css');
		wp_enqueue_script( 'jquery-ui-tabs');
		wp_enqueue_script( 'jquery-ui-sortable');

		// Allow each custom field to load up any necessary CSS/JS
		$available_custom_field_types = CCTM::get_available_custom_field_types();
		foreach ( $available_custom_field_types as $field_type ) {
			$element_file = CCTM_PATH.'/includes/elements/'.$field_type.'.php';
			if ( file_exists($element_file) )
			{
				include_once($element_file);
				if ( class_exists(CCTM::FormElement_classname_prefix.$field_type) )
				{
					$field_type_name = CCTM::FormElement_classname_prefix.$field_type;
					$FieldObj = new $field_type_name();
					$FieldObj->admin_init();
				}
			}
		}

		wp_enqueue_script( 'cctm_manager', CCTM_URL . '/js/manager.js' );
	}


	//------------------------------------------------------------------------------
	/**
	 * Adds a link to the settings directly from the plugins page.  This filter is
	 * called for each plugin, so we need to make sure we only alter the links that
	 * are displayed for THIS plugin.
	 * 
	 * INPUTS (determined by WordPress):
	 * @param	array	$links is a hash of links to display in the format of name => translation e.g.
	 * 		array('deactivate' => 'Deactivate')
	 * @param	string	$file is the path to plugin's main file (the one with the info header),
			relative to the plugins directory, e.g. 'custom-content-type-manager/index.php'
	 * @return array $links 
	 */
	public static function add_plugin_settings_link($links, $file) {
		if ( $file == basename(self::get_basepath()) . '/index.php' ) {
			$settings_link = sprintf('<a href="%s">%s</a>'
				, admin_url( 'options-general.php?page=cctm' )
				, __('Settings')
			);
			array_unshift( $links, $settings_link );
		}

		return $links;
	}
		
	//------------------------------------------------------------------------------
	/**
	 * Solves the problem with encodings.  On many servers, the following won't work:
	 *
	 * 		print 'ę'; // prints Ä™
	 *
	 * But this solves it: 
	 *
	 * 		print charset_decode_utf_8('ę');
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=88
	 * Solution from Squirrelmail, see http://pa2.php.net/manual/en/function.utf8-decode.php
	 */
	public static function charset_decode_utf_8($string) { 
		/* Only do the slow convert if there are 8-bit characters */ 
		/* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */ 
		if (! ereg("[\200-\237]", $string) and ! ereg("[\241-\377]", $string)) {
			return $string;
		}
		
		// decode three byte unicode characters 
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e","'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",$string); 
		
		// decode two byte unicode characters 
		$string = preg_replace("/([\300-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $string); 
		
		return $string; 
	}

	//------------------------------------------------------------------------------
	/**
	 * WordPress lacks an "onUpdate" event, so this is a home-rolled way I can run
	 * a specific bit of code when a new version of the plugin is installed. The way
	 * this works is the names of all files inside of the updates/ folder are loaded
	 * into an array, e.g. 0.9.4, 0.9.5.  When the first new page request comes through
	 * WP, the database is in the old state, whereas the code is new, so the database
	 * will say e.g. that the plugin version is 0.1 and the code will say the plugin version
	 * is 0.2.  All the available updates are included and their contents are executed 
	 * in order.  This ensures that all update code is run sequentially.
	 *
	 * Any version prior to 0.9.4 is considered "version 0" by this process.
	 *
	 */
	public static function check_for_updates() {
		// check to see if it's a new install and not an update
		/*
if ( empty(self::$data) ) {
			self::$data['cctm_installation_timestamp'] = time();
			update_option( self::db_key, self::$data );
			return;
		}
*/
//		print self::get_stored_version();
//		print self::get_current_version();

		// if it's not a new install, we check for updates
		if ( version_compare( self::get_stored_version(), self::get_current_version(), '<' ) ) 
		{	
			// set the flag
			define('CCMT_UPDATE_MODE', 1);
			// Load up available updates in order (scandir will sort the results automatically)
			$updates = scandir(CCTM_PATH.'/updates');
			foreach ($updates as $file) {
				// Skip the gunk
				if ($file === '.' || $file === '..') continue;
				if (is_dir(CCTM_PATH.'/updates/'.$file)) continue;
				if (substr($file, 0, 1) == '.')	continue;
				// skip non-php files
				if (pathinfo(CCTM_PATH.'/updates/'.$file, PATHINFO_EXTENSION) != 'php') continue;

				// We don't want to re-run older updates
				$this_update_ver = substr($file,0,-4);	
				if ( version_compare( self::get_stored_version(), $this_update_ver, '<' ) ) 
				{
					// Run the update by including the file
					include(CCTM_PATH.'/updates/'.$file);
					// timestamp the update
					self::$data['cctm_update_timestamp'] = time(); // req's new data structure
					// store the new version after the update
					self::$data['cctm_version'] = $this_update_ver; // req's new data structure
					update_option( self::db_key, self::$data );
				}
			}
		}
		
		// If this is empty, then it is a first install, so we timestamp it
		// and prep the data structure
		if (empty(CCTM::$data)) {
			CCTM::$data['cctm_installation_timestamp'] = time();
			CCTM::$data['cctm_version'] = CCTM::get_current_version();
			CCTM::$data['export_info'] = array(
				'title' 		=> 'CCTM Site',
				'author' 		=> get_option('admin_email',''),
				'url' 			=> get_option('siteurl','http://wpcctm.com/'),
				'description'	=> __('This site was created in part using the Custom Content Type Manager', CCTM_TXTDOMAIN),
			);		
			update_option(CCTM::db_key, CCTM::$data);
		}		
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Create custom post-type menu
	 * To avoid having the 1st submenu page be a duplicate of the parent item,
	 * make the menu_slug equal to the parent menu_slug; however then all incoming 
	 * links are then identified by the same $_GET param. For some reason, this 
	 * causes all my admin pages to print twice.
	 *
	 * See http://codex.wordpress.org/Administration_Menus	 
	 */
	public static function create_admin_menu() {

		// Main menu item
		add_menu_page(
			__('Manage Custom Content Types', CCTM_TXTDOMAIN),  // page title
			__('Content Types', CCTM_TXTDOMAIN),     		// menu title
			'manage_options',       							// capability
			'cctm',												// menu-slug (should be unique)
			'CCTM::page_main_controller',   					// callback function
			CCTM_URL .'/images/gear.png',   					// Icon
			71													// menu position
		);

		add_submenu_page( 
			'cctm', 									// parent slug (menu-slug from add_menu_page call)
			__('CCTM Custom Fields', CCTM_TXTDOMAIN), 	// page title
			__('Custom Fields', CCTM_TXTDOMAIN), 		// menu title
			'manage_options', 							// capability
			'cctm_fields', 								// menu_slug: cf = custom fields
			'CCTM::page_main_controller' 				// callback function
		);
		
		add_submenu_page( 
			'cctm', 								// parent slug (menu-slug from add_menu_page call)
			__('CCTM Settings', CCTM_TXTDOMAIN), 	// page title
			__('Settings', CCTM_TXTDOMAIN), 		// menu title
			'manage_options', 						// capability
			'cctm_settings', 						// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);
		
		add_submenu_page( 
			'cctm', 								// parent slug (menu-slug from add_menu_page call)
			__('CCTM Themes', CCTM_TXTDOMAIN), 		// page title
			__('Themes', CCTM_TXTDOMAIN), 			// menu title
			'manage_options', 						// capability
			'cctm_themes',  						// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);
		
		add_submenu_page( 
			'cctm', 								// parent slug (menu-slug from add_menu_page call)
			__('CCTM Tools', CCTM_TXTDOMAIN), 		// page title
			__('Tools', CCTM_TXTDOMAIN), 			// menu title
			'manage_options', 						// capability
			'cctm_tools', 							// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);
				
		add_submenu_page( 
			'cctm', 								// parent slug (menu-slug from add_menu_page call)
			__('CCTM Information', CCTM_TXTDOMAIN), // page title
			__('Info', CCTM_TXTDOMAIN), 			// menu title
			'manage_options', 						// capability
			'cctm_info', 							// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);

/*
		add_submenu_page( 
			'cctm',				 					// parent slug (menu-slug from add_menu_page call)
			__('CCTM Store', CCTM_TXTDOMAIN), 		// page title
			__('Store', CCTM_TXTDOMAIN), 			// menu title
			'manage_options', 						// capability
			'cctm_store', 							// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);

		add_submenu_page( 
			'cctm',				 					// parent slug (menu-slug from add_menu_page call)
			__('CCTM Help', CCTM_TXTDOMAIN), 		// page title
			__('Help', CCTM_TXTDOMAIN), 			// menu title
			'manage_options', 						// capability
			'cctm_help',					 		// menu_slug
			'CCTM::page_main_controller' 			// callback function
		);

	add_submenu_page(
		'themes.php'
		, _x('Editor', 'theme editor')
		, _x('Editor', 'theme editor')
		, 'edit_themes'
		, 'theme-editor.php');
	add_submenu_page( 
		$ptype_obj->show_in_menu, 
		$ptype_obj->labels->name, 
		$ptype_obj->labels->all_items, 
		$ptype_obj->cap->edit_posts
		, "edit.php?post_type=$ptype" );

*/
		// Add Custom Fields links
		$active_post_types = self::get_active_post_types();
		foreach ($active_post_types as $post_type) {
			$parent_slug = 'edit.php?post_type='.$post_type;
			if ($post_type == 'post'){
				$parent_slug = 'edit.php';
			}
			add_submenu_page( 
				$parent_slug
				, __('Custom Fields', CCTM_TXTDOMAIN)
				, __('Custom Fields', CCTM_TXTDOMAIN)
				, 'manage_options'
				, 'cctm&a=list_pt_associations&pt='.$post_type
				, 'CCTM::page_main_controller'
			);
		}

		// Add Settings links
		foreach ($active_post_types as $post_type) {
			$parent_slug = 'edit.php?post_type='.$post_type;
			if ( in_array($post_type, self::$reserved_post_types) ){
				continue;
			}
			add_submenu_page( 
				$parent_slug
				, __('Settings', CCTM_TXTDOMAIN)
				, __('Settings', CCTM_TXTDOMAIN)
				, 'manage_options'
				, 'cctm&a=edit_post_type&pt='.$post_type
				, 'CCTM::page_main_controller'
			);
		}

	}
	

	//------------------------------------------------------------------------------
	/**
	 * Handles creation of any directories this plugin writes to the webserver 
	 * file system.
	 * @return 	boolean true if everything is Ok, false if there were errors
	 */
	public static function create_verify_storage_directories() {
		
		$upload_dir = wp_upload_dir();
		$dir = $upload_dir['basedir'] .'/'.self::base_storage_dir . '/' . self::def_dir;

		if ( file_exists($dir) && is_dir($dir) ) {
			return true;
		}
		
		if ( !mkdir ( $dir, self::new_dir_perms, true) ) {
			self::$errors['mkdir'] = '<p>Failed to create the CCTM base storage directory: <code>'.$dir.'</code></p>
				<p><a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Permissions" target="_blank">Click here</a> for more information about correcting permissions errors on your server.</p>';
			return false;
		}
		return true;
	}


	//------------------------------------------------------------------------------
	/**
	 * Returns an array of active post_types (i.e. ones that will a have their fields
	 * standardized.
	 * 
	 * @return array
	 */
	public static function get_active_post_types() {
		$active_post_types = array();
		if ( isset(self::$data['post_type_defs']) && is_array(self::$data['post_type_defs'])) {
			foreach (self::$data['post_type_defs'] as $post_type => $def) {
				if ( isset($def['is_active']) && $def['is_active'] == 1 ) {
					$active_post_types[] = $post_type;
				}
				
			}
		}

		return $active_post_types;
	}

	//------------------------------------------------------------------------------
	/**
	 * Custom manipulation of the WHERE clause used by the wp_get_archives() function.
	 * WP deliberately omits custom post types from archive results.
	 *
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=13
	 */		
	public static function get_archives_where_filter( $where , $r ) {
	
		// Get only public, custom post types
		$args = array( 'public' => true, '_builtin' => false ); 		
		$public_post_types = get_post_types( $args );
		
		// Only posts get archives... not pages.
		$search_me_post_types = array('post');
		
		// check which have 'has_archive' enabled.
		foreach (self::$data as $post_type => $def) {
			if ( isset($def['has_archive']) && $def['has_archive'] && in_array($post_type, $public_post_types)) {
					$search_me_post_types[] = $post_type;
			} 
		}
		
		$post_types = "'" . implode( "' , '" , $search_me_post_types ) . "'";
		
		return str_replace( "post_type = 'post'" , "post_type IN ( $post_types )" , $where );
	}

	//------------------------------------------------------------------------------
	/**
	 * TODO: lookup others @ wpcctm.com
	 */
	public static function get_available_custom_field_types() {
		return array(
			'checkbox',
			'colorselector',
			'date',
			'dropdown',
			'image',
			'media',
			'multiselect',
			'relation',
			'text',
			'textarea',
			'wysiwyg', 
		);
	}

	//------------------------------------------------------------------------------
	/**
	 *  Defines the diretory for this plugin.
	 *
	 * @return string
	 */
	public static function get_basepath() {
		return dirname(dirname(__FILE__));
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Gets the plugin version from this class.
	 *
	 * @return	string
	 */
	public static function get_current_version() {
		return self::version .'-'. self::version_meta;
	}

	//------------------------------------------------------------------------------
	/**
	 * Returns a path with trailing slash.
	 *
	 * @return string
	 */
	public static function get_custom_icons_src_dir() {
		self::$custom_field_icons_dir = CCTM_URL.'/images/custom-fields/';
		return self::$custom_field_icons_dir;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Get the flash message (i.e. a message that persists for the current user only
	 * for the next page view). See "Flashdata" here:
	 * http://codeigniter.com/user_guide/libraries/sessions.html
	 *
	 * @return message
	 */
	public static function get_flash() {
		$output = ''; 
		$key = self::get_identifier();
		if (isset(self::$data['flash'][$key])) {
			$output = self::$data['flash'][$key];
			unset( self::$data['flash'][$key] );
			update_option(self::db_key, self::$data);
			return html_entity_decode($output);
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Used to identify the current user for flash messages and screen locks
	 */
	public static function get_identifier() {
		global $current_user;
		if (!isset($current_user->ID) || empty($current_user->ID)) {
			return 0;
		}
		else {
			return $current_user->ID;
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Designed to safely retrieve scalar elements out of a hash. Don't use this
	 * if you have a more deeply nested object (e.g. an array of arrays).
	 *
	 * @param array $hash an associative array, e.g. array('animal' => 'Cat');
	 * @param string $key the key to search for in that array, e.g. 'animal'
	 * @param mixed $default (optional) : value to return if the value is not set. Default=''
	 * @return mixed
	 */
	public static function get_value($hash, $key, $default='') {
		if ( !isset($hash[$key]) ) {
			return $default;
		}
		else {
			if ( is_array($hash[$key]) ) {
				return $hash[$key];
			}
			// Warning: stripslashes was added to avoid some weird behavior
			else {
				return esc_html(stripslashes($hash[$key]));
			}
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * return all post-type definitions
	 * @return	array
	 */
	public static function get_post_type_defs() {
		if ( isset(self::$data['post_type_defs']) && is_array(self::$data['post_type_defs'])) {
			return self::$data['post_type_defs'];
		}
		else {
			return array();
		}
	}
		
	//------------------------------------------------------------------------------
	/**
	 * Gets the plugin version (used to check if updates are available). This checks
	 * the database to see what the database thinks is the current version. Right 
	 * after an update, the database will think the version is older than what 
	 * the CCTM class will show as the current version.
	 *
	 * @return	string
	 */
	public static function get_stored_version() {
		if ( isset(self::$data['cctm_version']) ) {
			return self::$data['cctm_version'];
		}
		else {
			return '0';
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public static function highlight_cctm_compatible_themes($stuff) {
		$stuff[] = 'CCTM compatible!'; 
		return $stuff;
		print $stuff; exit;
	}
		
	//------------------------------------------------------------------------------
	/**
	* Includes the class file for the field type specified by $field_type
	*/
	public static function include_form_element_class($field_type) {
		if (empty($field_type) ) {
			$msg = __('Field type is empty.', CCTM_TXTDOMAIN);
			die($msg);
		}
		
		$element_file = CCTM_PATH.'/includes/elements/'.$field_type.'.php';
		if ( !file_exists($element_file)) {
			// ERROR!
			$msg = sprintf( __('File not found for %s element: %s', CCTM_TXTDOMAIN) 
				, $field_type
				, $element_file
			);
			die ($msg); //! TODO: print admin notice
		}
		//
		else {
			//! TODO: try/catch block
			include_once($element_file);
			if ( !class_exists(self::FormElement_classname_prefix.$field_type) ) {
				$msg = sprintf( __('Incorrect class name in %s file. Expected class name: %s', CCTM_TXTDOMAIN)
					, $element_file
					, self::FormElement_classname_prefix.$field_type
				);
				die( $msg );
			}
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Used when generating forms. Any non-empty non-zero incoming value will cause
	 * the function to return checked="checked"
	 * @param	mixed	normally a string, but if an array, the 2nd param must be set
	 * @param	string	value to look for inside the $input array. 
	 * @return	string	either '' or 'checked="checked"'
	 */
	public static function is_checked($input, $find_in_array='') {
		if ( is_array($input) ) {
			if ( in_array($find_in_array, $input) ) {
				return 'checked="checked"';			
			}		
		}
		else
		{
			if (!empty($input) && $input!=0) {
				return 'checked="checked"';
			}
		}
		return ''; // default
	}

	//------------------------------------------------------------------------------
	/**
	 * If $option_value == $field_value, then this returns 'selected="selected"'
	 * @param	string	$option_value: the value of the <option> being tested
	 * @param	string	$field_value: the current value of the field
	 * @return	string
	 */
	public static function is_selected($option_value, $field_value) {
		if ( $option_value == $field_value ) {
			return 'selected="selected"';
		}
		return '';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Using something like the following:
	 *	if (!@fclose(@fopen($src, 'r'))) {
	 *		$src = CCTM_URL.'/images/custom-fields/default.png';
	 *	}
	 * caused segfaults in some server configurations (see issue 60):
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=60
	 * So in order to check whether an image path is broken or not, we translate the 
	 * $src URL into a local path so we can use humble file_exists() instead.
	 *
	 * This must also be able to handle when WP is installed in a sub directory.
	 *
	 * @param	string	$src a path to an image ON THIS SERVER, e.g. '/wp-content/uploads/img.jpg'
	 *					or 'http://mysite.com/some/img.jpg'
	 * @return	boolean	true if the img is valid, false if the img link is broken
	 */
	public static function is_valid_img($src) {
	
		$info = parse_url($src);
		
		// Bail on malformed URLs
		if (!$info) {
			return false;
		}		
		// Is this image hosted on another server? (currently that's not allowed)
		if ( isset($info['scheme']) ) {
			$this_site_info = parse_url( get_site_url() );
			if ( $this_site_info['scheme'] != $info['scheme'] 
				|| $this_site_info['host'] != $info['host'] 
				|| $this_site_info['port'] != $info['port']) {
				
				return false;
			}
		}
		
		// Gives us something like "/home/user/public_html/blog"
		$ABSPATH_no_trailing_slash = preg_replace('#/$#','', ABSPATH);
		
		// This will tell us whether WP is installed in a subdirectory
		$wp_info = parse_url(site_url());

		// This works when WP is installed @ the root of the site
		if ( !isset($wp_info['path']) ) {
			$path = $ABSPATH_no_trailing_slash . $info['path'];
		}
		// But if WP is installed in a sub dir...
		else {
			$path_to_site_root = preg_replace('#'.preg_quote($wp_info['path']).'$#'
				,''
				, $ABSPATH_no_trailing_slash);
			$path = $path_to_site_root . $info['path'];
		}

		if ( file_exists($path) ) {
			return true;
		}
		else {
			return false;
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Load CCTM data from database.
	 */
	public static function load_data() {
		self::$data = get_option( CCTM::db_key, array() );
	} 
	
	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 * @param	string	filename (relative to the views/ directory)
	 * @param	array	associative array of data
	 * @return	string	the parsed contents of that file
	 */
	public static function load_view($filename, $data=array() ) {
	    if (is_file(CCTM_PATH . '/views/'.$filename)) {
	        ob_start();
	        include CCTM_PATH . '/views/'.$filename;
	        return ob_get_clean();
	    }
	    die('View file does not exist: ' .$filename);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Since WP doesn't seem to support sorting of custom post types, we have to 
	 * forcibly tell it to sort by the menu order. Perhaps this should kick in
	 * only if a post_type's def has the "Attributes" box checked?
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=142
	 */
	public static function order_posts($orderBy) {
        global $wpdb;
		$orderBy = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
        return($orderBy);
    }


	//------------------------------------------------------------------------------
	/**
	* This admin menu page handles exporting of the CCTM definition data. 
	*/
	public static function page_export() {

		$settings = get_option(CCTM::db_key_settings, array() );
		$settings['export_info'] = self::get_value($settings, 'export_info', array() );
		$action_name  = 'custom_content_type_mgr_export_def';
		$nonce_name  = 'custom_content_type_mgr_export_def_nonce';
		$msg = '';
					
		// Save if submitted...
		if ( !empty($_POST) && check_admin_referer($action_name, $nonce_name) ) {
			// A little cleanup before we sanitize
			unset($_POST[ $nonce_name ]);
			unset($_POST['_wp_http_referer']);

			$posted_data = self::_sanitize_export_params($_POST);
			$settings['export_info'] = $posted_data; // prep for saving.

			// Any errors?
			if ( !empty(self::$errors) ) {
				$msg = self::_format_errors();
			}
			// Save;
			else {
				$title = 'cctm_def';
				if ( !empty($posted_data['title']) ) {
					$title = $posted_data['title'];
					$title = strtolower($title);
					$title = preg_replace('/\s+/', '_', $title); 
					$title = preg_replace('/[^a-z_]/', '', $title); 
				}
				$nonce = wp_create_nonce('cctm_download_definition');
				$msg = sprintf('<div class="updated"><p>%s</p></div>'
				, sprintf(__('Your Custom Content Type definition %s should begin downloading shortly. If the download does not begin, %s', CCTM_TXTDOMAIN)
				, '<storng>'.$title.'.cctm.json</strong>'
				, '<a href="'.CCTM_URL.'/download.php?_wpnonce='.$nonce.'">click here</a>'));

				// Save the options: anything that's in the form is considered a valid "info" key.
				update_option( self::db_key_settings, $settings );

				// Fire off a request to download the file:
				$msg .= sprintf('
					<script type="text/javascript">
						jQuery(document).ready(function() {
							window.location.replace("%s?_wpnonce=%s");
						});
					</script>'
					, CCTM_URL.'/download.php'
					, $nonce );
			}
		}
	
		include(CCTM_PATH.'/includes/pages/export.php');
	}

	//------------------------------------------------------------------------------
	/**
	* Ugh... the structure here sucks... tiered validation is messy
	*/
	public static function page_import() {
		require_once('ImportExport.php');

		$settings = get_option(CCTM::db_key_settings, array() );
		$settings['export_info'] = self::get_value($settings, 'export_info', array() );
		$action_name  = 'custom_content_type_mgr_import_def';
		$nonce_name  = 'custom_content_type_mgr_import_def_nonce';
		$msg = self::get_flash();
		
		
		// Save if submitted... this is tricky because validation comes in tiers here.
		if ( !empty($_POST) && check_admin_referer($action_name, $nonce_name) ) {
			// A little cleanup before we sanitize
			unset($_POST[ $nonce_name ]);
			unset($_POST['_wp_http_referer']);

			// Start Checking stuff....
			// Big no-no #1: no file 
			if ( empty($_FILES) || empty($_FILES['cctm_settings_file']['tmp_name'])) {
				self::$errors['cctm_settings_file'] = sprintf( 
					__('No file selected', CCTM_TXTDOMAIN)
					, CCTM::max_def_file_size 
				); 
				$msg = self::_format_errors();
				include_once(CCTM_PATH.'/includes/pages/import.php');
				return;
			}
			// Big no-no #2: file is too  big
			if ($_FILES['cctm_settings_file']['size'] > CCTM::max_def_file_size ) {
				self::$errors['cctm_settings_file'] = sprintf( 
					__('The definition filesize must not exceed %s bytes.', CCTM_TXTDOMAIN)
					, CCTM::max_def_file_size 
				); 
				$msg = self::_format_errors();
				include_once(CCTM_PATH.'/includes/pages/import.php');
				return;
			}
			
			// Big no-no #3: bad data structure
			$raw_file_contents = file_get_contents($_FILES['cctm_settings_file']['tmp_name']);
			$data = json_decode( $raw_file_contents, true);

			// Let's check that this thing is legit
			if ( !ImportExport::is_valid_upload_structure($data) ) {
				self::$errors['format'] = __('The uploaded file is not in the correct format.', CCTM_TXTDOMAIN);
				$msg = self::_format_errors();
				include_once(CCTM_PATH.'/includes/pages/import.php');
				return;
			}
			
			// create_verify_storage_directories will set errors, and we add another error here
			// to let the user know that we can't interface with the library dir 
			$basename = basename($_FILES['cctm_settings_file']['name']);
			// Sometimes you can get filenames that look lie "your_def.cctm (1).json"
			if ( !ImportExport::is_valid_basename($basename) ) {
				// grab anything left of the first period, then re-create the .cctm.json extension
				list($basename) = explode('.', $basename);
				$basename .= ImportExport::extension;
			}
			$upload_dir = wp_upload_dir();
			$dir = $upload_dir['basedir'] .'/'.self::base_storage_dir . '/' . self::def_dir;
	
			if ( !self::create_verify_storage_directories() ) {
				self::$errors['library'] = __('We could not upload the definition file to your library.', CCTM_TXTDOMAIN);	
			} 
			elseif ( !move_uploaded_file($_FILES['cctm_settings_file']['tmp_name'], $dir.'/'.$basename )) {
				self::$errors['library'] = __('We could not upload the definition file to your library.', CCTM_TXTDOMAIN);	
			}
		
			// Any errors?  At this point, they aren't deal-breakers.
			if ( !empty(self::$errors) ) {
				$msg = self::_format_errors();
			}

			// Save
			$settings = get_option(self::db_key_settings, array() );
			$settings['candidate'] = $data;
			update_option(self::db_key_settings, $settings);			

		}

		include_once(CCTM_PATH.'/includes/pages/import.php');
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This is the grand poobah of functions for the admin pages: it routes requests 
	 * to specific functions.
	 * This is the function called when someone clicks on the settings page.
	 * The job of a controller is to process requests and route them.
	 *
	 */
	public static function page_main_controller() {
		// TODO: this should be specific to the request
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		// Grab any possible parameters that might get passed around in the URL
		$action		= self::get_value($_GET, self::action_param);
		$post_type	= self::get_value($_GET, self::post_type_param);
		$file 		= self::get_value($_GET, 'file');
		$field_type	= self::get_value($_GET, 'type');
		$field_name = self::get_value($_GET, 'field');
		
		
		
		// Default Actions for each main menu item (see create_admin_menu)
		if (empty($action)) {
			$page = self::get_value($_GET, 'page', 'cctm');
			switch ($page) {
				case 'cctm': // main: custom content types
					$action = 'list_post_types';
					break;
				case 'cctm_fields': // custom-fields
					$action = 'list_custom_fields';
					break;
				case 'cctm_settings':	// settings
					$action = 'settings';
					break;
				case 'cctm_themes': // themes
					$action = 'themes';
					break;
				case 'cctm_tools':	// tools
					$action = 'tools';
					break;
				case 'cctm_info':	// info
					$action = 'info';
					break;			
			}
		}
		
		// Validation on the controller name to prevent mischief:
		if ( preg_match('/[^a-z_\-]/i', $action) ) {
			include CCTM_PATH.'/controllers/404.php';
			return;
		}
		
		$requested_page = CCTM_PATH.'/controllers/'.$action.'.php'; 
		
		if ( file_exists($requested_page) ) {
			include($requested_page);
		}
		else {
			include CCTM_PATH.'/controllers/404.php';
		}
		return;
	}

	/*------------------------------------------------------------------------------
	SYNOPSIS: a simple parsing function for basic templating.
	INPUT:
		$tpl (str): a string containing [+placeholders+]
		$hash (array): an associative array('key' => 'value');
	OUTPUT
		string; placeholders corresponding to the keys of the hash will be replaced
		with the values and the string will be returned.
	------------------------------------------------------------------------------*/
	public static function parse($tpl, $hash) 
	{
	
	    foreach ($hash as $key => $value) 
	    {
	    	if ( !is_array($value) )
	    	{
	        	$tpl = str_replace('[+'.$key.'+]', $value, $tpl);
	        }
	    }
	    
	    // Remove any unparsed [+placeholders+]
	    $tpl = preg_replace('/\[\+(.*?)\+\]/', '', $tpl);
	    
	    return $tpl;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Print errors if they were thrown by the tests. Currently this is triggered as
	 * an admin notice so as not to disrupt front-end user access, but if there's an
	 * error, you should fix it! The plugin may behave erratically!
	 * INPUT: none... ideally I'd pass this a value, but the WP interface doesn't make
	 * 	this easy, so instead I just read the class variable: CCTMtests::$errors
	 * 	
	 * @return	none  But errors are printed if present.
	 */
	public static function print_notices() {
		if ( !empty(CCTMtests::$errors) ) {
			$error_items = '';
			foreach ( CCTMtests::$errors as $e ) {
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

	//------------------------------------------------------------------------------
	/**
	 * Print warnings if there are any that haven't been dismissed
	 */
	public static function print_warnings() {
		
		$warning_items = '';
		
		// Check for warnings
		if ( !empty(self::$data['warnings']) ) {
//			print '<pre>'. print_r(self::$data['warnings']) . '</pre>'; exit;
			$clear_warnings_url = sprintf(
				'<a href="?page=cctm&a=clear_warnings&_wpnonce=%s" title="%s" class="button">%s</a>'
				, wp_create_nonce('cctm_clear_warnings')
				, __('Dismiss all warnings', CCTM_TXTDOMAIN)
				, __('Dismiss Warnings', CCTM_TXTDOMAIN)
			);
			$warning_items = '';
			foreach ( self::$data['warnings'] as $warning => $viewed ) {
				if ($viewed == 0) {
					$warning_items .= "<li>$warning</li>";
				}
			}
		}
		
		if ($warning_items) {
			$msg = __('The Custom Content Type Manager encountered the following warnings:', CCTM_TXTDOMAIN);
			printf('<div id="custom-post-type-manager-warning" class="error">
				<p>
					<strong>%s</strong>
					<ul style="margin-left:30px;">
						%s
					</ul>
				</p>
				<p>%s</p>
				</div>'
				, $msg
				, $warning_items
				, $clear_warnings_url
			);		
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * Register custom post-types, one by one. Data is stored in the wp_options table
	 * in a structure that matches exactly what the register_post_type() function
	 * expectes as arguments.
	 *
	 * See: http://codex.wordpress.org/Function_Reference/register_post_type
	 * See wp-includes/posts.php for examples of how WP registers the default post types
	 *
	 *	$def = Array
	 *	(
	 *	    'supports' => Array
	 *	        (
	 *	            'title',
	 *	            'editor'
	 *	        ),
	 *
	 *	    'post_type' => 'book',
	 *	    'singular_label' => 'Book',
	 *	    'label' => 'Books',
	 *	    'description' => 'What I&#039;m reading',
	 *	    'show_ui' => 1,
	 *	    'capability_type' => 'post',
	 *	    'public' => 1,
	 *	    'menu_position' => '10',
	 *	    'menu_icon' => '',
	 *	    'custom_content_type_mgr_create_new_content_type_nonce' => 'd385da6ba3',
	 *	    'Submit' => 'Create New Content Type',
	 *	    'show_in_nav_menus' => '',
	 *	    'can_export' => '',
	 *	    'is_active' => 1,
	 *	);

	FUTURE??:
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
	*/
	public static function register_custom_post_types() {
	
		$post_type_defs = self::get_post_type_defs();
		foreach ($post_type_defs as $post_type => $def) {
			if ( isset($def['is_active'])
				&& !empty($def['is_active'])
				&& !in_array($post_type, self::$built_in_post_types)) {
				register_post_type( $post_type, $def );
			}
		}
		// Added per issue 50
		// http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=50
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	//------------------------------------------------------------------------------
	/**
	 * Warnings are like errors, but they can be dismissed.
	 * So if the warning hasn't been logged already and dismissed,
	 * it gets its own place in the data structure.
	 *
	 * @param	string	Text of the warning
	 * @return	none
	 */
	public static function register_warning($str) {
		if (!isset(self::$data['warnings'][$str])) {
			self::$data['warnings'][$str] = 0; // 0 = not read.
			update_option(self::db_key, self::$data);			
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This filters the basic page lookup so URLs like http://mysite.com/archives/date/2010/11
	 * will return custom post types.
	 * See issue 13 for full archive suport:
	 * http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=13
	 * and http://bajada.net/2010/08/31/custom-post-types-in-the-loop-using-request-instead-of-pre_get_posts
	 */
	public static function request_filter( $query ) {

		// This is a troublesome little query... we need to monkey with it so WP will play nice with
		// custom post types, but if you breathe on it wrong, chaos ensues. See the following issues:
		// 	http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=108
		// 	http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=111
		// 	http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=112
		if ( empty($query) 
			|| isset($query['pagename']) 
			|| isset($query['preview']) 
			|| isset($query['feed']) 
			|| isset($query['page_id'])
			|| !empty($query['post_type']) ) {
			
			return $query;
		}

		// Get only public, custom post types
		$args = array( 'public' => true, '_builtin' => false ); 		
		$public_post_types = get_post_types( $args );


		// Categories can apply to posts and pages
		$search_me_post_types = array('post','page');		
		if ( isset($query['category_name']) ) {
			foreach ($public_post_types as $pt => $tmp) {
				$search_me_post_types[] = $pt;
			}
			$query['post_type'] = $search_me_post_types;
			return $query;
		}
		
		// Only posts get archives, not pages, so our first archivable post-type is "post"...
		$search_me_post_types = array('post');		
		
		// check which have 'has_archive' enabled.
		foreach (self::$data as $post_type => $def) {
			if ( isset($def['has_archive']) && $def['has_archive'] && in_array($post_type, $public_post_types)) {
					$search_me_post_types[] = $post_type;
			} 
		}

		$query['post_type'] = $search_me_post_types;
		
		return $query;
	}

	//------------------------------------------------------------------------------
	/**
	 * Ensures that the front-end search form can find posts. 
	 * See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=143
	 */
	public static function search_filter($query) {
		if ($query->is_search or $query->is_feed) {
			if ( !isset($_GET['post_type']) && empty($_GET['post_type'])) {
				$post_types = get_post_types( array('public'=>true) );
				$query->set('post_type', $post_types);
			}
		}
		return $query;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Sets a flash message that's viewable only for the next page view (for the current user)
	 * $_SESSION doesn't work b/c WP doesn't natively support them = lots of confused users.
	 * setcookie() won't work b/c WP has already sent header info.
	 * So instead, we store this stuff in the database. Sigh.
	 * 
	 * @param string $msg text or html message
	 */
	public static function set_flash($msg) {
		self::$data['flash'][ self::get_identifier() ] = $msg;
		update_option(self::db_key, self::$data);
	}


	//------------------------------------------------------------------------------
	/**
	 * Used by php usort to sort custom field defs by their sort_param attribute
	 *
	 * @param string $field
	 * @param string $sortfunc
	 * @return array
	 */
	public static function sort_custom_fields($field, $sortfunc) {
		return create_function('$var1, $var2', 'return '.$sortfunc.'($var1["'.$field.'"], $var2["'.$field.'"]);');
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Recursively removes all quotes from $_POSTED data if magic quotes are on
	 * http://algorytmy.pl/doc/php/function.stripslashes.php
	 *
	 * @param	array	possibly nested 
	 * @return	array	clensed of slashes
	 */
	public static function stripslashes_deep($value)
	{
		if ( is_array($value) ) {
			$value = array_map( 'CCTM::'. __FUNCTION__, $value);    
		}
		else {
			$value = stripslashes($value);
		}
	   return $value;
	}

	//------------------------------------------------------------------------------
	/**
	 * Recursively strips tags from all inputs, including nested ones.
	 *
	 * @param	array	usually the $_POST array or a copy of it
	 * @return	array	the input array, with tags stripped out of each value.
	 */
	public static function striptags_deep($value)
	{
		if ( is_array($value) ) {
			$value = array_map('CCTM::'. __FUNCTION__, $value);    
		}
		else {
			$value = strip_tags($value, self::$allowed_html_tags);
		}
	   return $value;
	}

}

/*EOF CCTM.php*/