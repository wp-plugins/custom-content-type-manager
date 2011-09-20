<?php
/*------------------------------------------------------------------------------
This plugin standardizes the custom fields for specified content types, e.g.
post, page, and any other custom post-type you register via a plugin.
------------------------------------------------------------------------------*/
class StandardizedCustomFields 
{
	/*
	This prefix helps ensure unique keys in the $_POST array. It is used only to 
	identify the form elements; this prefix is *not* used as part of the meta_key
	when saving the field names to the database. If you want your fields to be 
	hidden from built-in WordPress functions, you can name them individually 
	using "_" as the first character.
	
	If you omit a prefix entirely, your custom field names must steer clear of
	the built-in post field names (e.g. 'content').
	*/
	const field_name_prefix = 'custom_content_'; 
	
	// Which types of content do we want to standardize?
	public static $content_types_array = array('post');
	
	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Get custom fields for this content type.
	 * @param string $post_type the name of the post_type, e.g. post, page.
	OUTPUT: array of associative arrays where each associative array describes 
		a custom field to be used for the $content_type specified.
	FUTURE: read these arrays from the database.
	*/
	private static function _get_custom_fields($post_type) {
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_fields']))
		{
			return CCTM::$data['post_type_defs'][$post_type]['custom_fields'];
		}
		else
		{
			return array();
		}
	}

	//------------------------------------------------------------------------------
	/**
	 * This determines if the user is editing an existing post.
	 *
	 * @return boolean
	 */
	private static function _is_existing_post()
	{
		if ( substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1) == 'post.php' )
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	//------------------------------------------------------------------------------
	/**
	 * This determines if the user is creating a new post.
	 *
	 * @return boolean
	 */
	 private static function _is_new_post()
	{
		if ( substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],'/')+1) == 'post-new.php' )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//! Public Functions	
	//------------------------------------------------------------------------------
	/**
	* Create the new Custom Fields meta box
	* TODO: allow customization of the name, instead of just 'Custom Fields', and also
	* of the wrapper div.
	*/
	public static function create_meta_box() {
		$content_types_array = CCTM::get_active_post_types();
		foreach ( $content_types_array as $content_type ) {
			add_meta_box( 'custom-content-type-mgr-custom-fields'
				, __('Custom Fields', CCTM_TXTDOMAIN )
				, 'StandardizedCustomFields::print_custom_fields'
				, $content_type
				, 'normal'
				, 'high'
				, $content_type 
			);
		}
	}

	/**
	 * WP only allows users to select PUBLISHED pages of the same post_type in their hierarchical
	 * menus.  And there are no filters for this whole thing save at the end to filter the generated 
	 * HTML before it is sent to the browser. Arrgh... this is grossly inefficient!!
	 * It's inefficient, but here we optionally pimp out the HTML to offer users sensible choices for
	 * hierarchical parents.
	 *
	 * @param	string	incoming html element for selecting a parent page, e.g.
	 *						<select name="parent_id" id="parent_id">
	 *					        <option value="">(no parent)</option>
	 *					        <option class="level-0" value="706">Post1</option>
	 *						</select>	
	 *
	 * See http://wordpress.org/support/topic/cannot-select-parent-when-creatingediting-a-page
	 
	 
		if( preg_match('/name="(parent_id|post_parent)"/', $output) && $post->post_type="articles" ) {
			$post_statuses = array('pending','publish');
			$post_exclude = is_numeric($_GET['post']) ? ' AND ID!='.$_GET['post']:'';
			$query = "SELECT * FROM ".$wpdb->posts." WHERE (post_type = 'page' AND (post_status='".implode("' OR post_status='",$post_statuses)."') AND $post_exclude ) ORDER BY menu_order, post_title ASC";
			$pages = $wpdb->get_results($query);
			$output = '';
			if ( ! empty($pages) ) {
				$output = "<select name=\"parent_id\" id=\"\">\n";
				$output .= "\t<option value=\"\">".__('(no parent)')."</option>\n";
				$output .= walk_page_dropdown_tree($pages, 0);
				$output .= "</select>\n";
			}
		}
	 
	 	CCTM::$data[$post_type]['cctm_hierarchical_post_types'] = array()
	 	CCTM::$data[$post_type]['cctm_hierarchical_post_status'] = array()
	 
	 */
	public static function customized_hierarchical_post_types( $html ) {
		global $wpdb, $post;
		$post_type = $post->post_type;
		
		// customize if selected
		if (isset(CCTM::$data[$post_type]['hierarchical'])
			&& CCTM::$data[$post_type]['hierarchical'] 
			&& CCTM::$data[$post_type]['cctm_hierarchical_custom']) {
			// filter by additional parameters
			if ( CCTM::$data[$post_type]['cctm_hierarchical_includes_drafts'] ) {
				$args['post_status'] = 'publish,draft,pending';	
			}
			else {
				$args['post_status'] = 'publish';
			}
			
			$args['post_type'] = CCTM::$data[$post_type]['cctm_hierarchical_post_types'];
			// We gotta ensure ALL posts are returned.
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=114
			$args['numberposts'] = -1;

			$posts = get_posts($args);

			$html = '<select name="parent_id" id="parent_id">
				<option value="">(no parent)</option>
			';
			foreach ( $posts as $p ) {
				$is_selected = '';
				if ( $p->ID == $post->post_parent ) {
					$is_selected = ' selected="selected"';	
				}
				$html .= sprintf('<option class="level-0" value="%s"%s>%s (%s)</option>', $p->ID, $is_selected, $p->post_title, $p->post_type);
			}
			$html .= '</select>';
		}
		return $html;
	}

	/*------------------------------------------------------------------------------
	Display the new Custom Fields meta box inside the WP manager.
	INPUT:
	@param object $post passed to this callback function by WP. 
	@param object $callback_args will always have a copy of this object passed (I'm not sure why),
		but in $callback_args['args'] will be the 7th parameter from the add_meta_box() function.
		We are using this argument to pass the content_type.
	
	@return null	this function should print form fields.
	------------------------------------------------------------------------------*/
	public static function print_custom_fields($post, $callback_args='') 
	{
	
		$post_type = $callback_args['args']; // the 7th arg from add_meta_box()
		$custom_fields = self::_get_custom_fields($post_type);
		$output = '';		
				

		// If no custom content fields are defined, or if this is a built-in post type that hasn't been activated...
		if ( empty($custom_fields) )
		{
			return;
		}
		
		foreach ( $custom_fields as $cf ) {
			if (!isset(CCTM::$data['custom_field_defs'][$cf])) {
				// throw error!!
				continue;
			}
			$def = CCTM::$data['custom_field_defs'][$cf];
			$output_this_field = '';
			CCTM::include_form_element_class($def['type']); // This will die on errors
			$field_type_name = CCTM::FormElement_classname_prefix.$def['type'];
			$FieldObj = new $field_type_name(); // Instantiate the field element
			
			if ( self::_is_new_post() ) {	
				$FieldObj->props = $def;
				$output_this_field = $FieldObj->get_create_field_instance();
			}
			else {
				$current_value = htmlspecialchars( get_post_meta( $post->ID, $def['name'], true ) );
				$FieldObj->props = $def;
				$output_this_field =  $FieldObj->get_edit_field_instance($current_value);
			}
						
			$output .= $output_this_field;
		}
		
		// Print the nonce: this offers security and it will help us know when we should do custom saving logic in the save_custom_fields function
		$output .= '<input type="hidden" name="_cctm_nonce" value="'. wp_create_nonce('cctm_create_update_post') . '" />';
		
		// Show the big icon: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=136
		if ( isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
			&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0 ) { 
			$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
			
			$output .= sprintf('
			<style>
				#icon-edit, #icon-post {
				  background-image:url(%s);
				  background-position: 0px 0px;
				}
			</style>'
			, CCTM_URL . '/images/icons/32x32/'. $baseimg);
		}
 		// Print the form
 		print '<div class="form-wrap">';		
	 	print $output;
	 	print '</div>';
 
	}


	/*------------------------------------------------------------------------------
	Remove the default Custom Fields meta box. Only affects the content types that
	have been activated.
	INPUTS: sent from WordPress
	------------------------------------------------------------------------------*/
	public static function remove_default_custom_fields( $type, $context, $post ) 
	{
		$content_types_array = CCTM::get_active_post_types();
		foreach ( array( 'normal', 'advanced', 'side' ) as $context ) {
			foreach ( $content_types_array as $content_type )
			{
				remove_meta_box( 'postcustom', $content_type, $context );
			}
		}
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Save the new Custom Fields values. If the content type is not active in the 
	 * CCTM plugin or its custom fields are not being standardized, then this function 
	 * effectively does nothing.
	 *
	 * WARNING: This function is also called when the wp_insert_post() is called, and
	 * we don't want to step on its toes. We want this to kick in ONLY when a post 
	 * is inserted via the WP manager. 
	 * see http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=52
	 * 
	 * @param	integer	$post_id id of the post these custom fields are associated with
	 * @param	object	$post  the post object
	 */
	public static function save_custom_fields( $post_id, $post ) 
	{
		// Bail if you're not in the admin editing a post
		if (!self::_is_existing_post() && !self::_is_new_post() ) {
			return;
		}
		
		// Bail if this post-type is not active in the CCTM
		if ( !isset(CCTM::$data['post_type_defs'][$post->post_type]['is_active']) 
			|| CCTM::$data['post_type_defs'][$post->post_type]['is_active'] == 0) {
			return;
		}
	
		// Bail if there are no custom fields defined in the CCTM
		if ( empty(CCTM::$data['post_type_defs'][$post->post_type]['custom_fields']) ) {
			return;
		}
		
		// See issue http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=80
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		}

		// Use this to ensure you save custom fields only when saving from the edit/create post page
		$nonce = CCTM::get_value($_POST, '_cctm_nonce');
		if (! wp_verify_nonce($nonce, 'cctm_create_update_post') ) {
			return;
		}

		if ( !empty($_POST) ) {			
			$custom_fields = self::_get_custom_fields($post->post_type);
			foreach ( $custom_fields as $field_name ) {
				$field_type = CCTM::$data['custom_field_defs'][$field_name]['type'];
				CCTM::include_form_element_class($field_type); // This will die on errors
	
				$field_type_name = CCTM::FormElement_classname_prefix.$field_type;
				$FieldObj = new $field_type_name(); // Instantiate the field element
				$FieldObj->props = CCTM::$data['custom_field_defs'][$field_name];
				$value = $FieldObj->save_post_filter($_POST, $field_name);
				update_post_meta( $post_id, $field_name, $value );
			}			
		}
	}


} // End Class



/*EOF*/