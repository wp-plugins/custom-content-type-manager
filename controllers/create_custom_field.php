<?php
/*------------------------------------------------------------------------------
Edit a custom field of the type specified by $field_type.  

$field_type
------------------------------------------------------------------------------*/

	
$field_data = array(); // Data object we will save

self::include_form_element_class($field_type); // This will die on errors

$field_type_name = self::FormElement_classname_prefix.$field_type;
$FieldObj = new $field_type_name(); // Instantiate the field element


// Page variables
$data = array();
$data['page_title'] = sprintf(__('Create Custom Field: %s', CCTM_TXTDOMAIN), $FieldObj->get_name() );
$data['msg'] = '';
$data['menu'] = sprintf('<a href="?page=cctm_fields&a=list_custom_field_types" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['action_name']  = 'custom_content_type_mgr_create_new_custom_field';
$data['nonce_name']  = 'custom_content_type_mgr_create_new_custom_field_nonce';


// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	// A little cleanup before we handoff to save_definition_filter
	unset($_POST[ $data['nonce_name'] ]);
	unset($_POST['_wp_http_referer']);

	// Validate and sanitize any submitted data
	$field_data 		= $FieldObj->save_definition_filter($_POST, $post_type);
	$field_data['type'] = $field_type; // same effect as adding a hidden field
	
	$field_data['sort_param'] = 0; // default: up top
	
	$FieldObj->props 	= $field_data;  // This is how we repopulate data in the create forms

	// Any errors?
	if ( !empty($FieldObj->errors) ) {
		$msg = $FieldObj->format_errors();
	}
	// Save;
	else {
		$field_name = $field_data['name']; 
		self::$data['custom_field_defs'][$field_name] = $field_data;
		update_option( self::db_key, self::$data );
		unset($_POST);
		$success_msg = sprintf('<div class="updated"><p>%s</p></div>'
			, sprintf(__('A %s custom field has been created.', CCTM_TXTDOMAIN)
			, '<em>'.$FieldObj->get_name().'</em>'));
		self::set_flash($success_msg);
		include(CCTM_PATH.'/controllers/list_custom_fields.php');
		return;
	}

}

$data['fields'] = $FieldObj->get_create_field_definition();

$data['icon'] = sprintf('<img src="%s" class="cctm-field-icon" id="cctm-field-icon-%s"/>'
				, $FieldObj->get_icon(), $field_type);
$data['url'] = $FieldObj->get_url();
$data['name'] = $FieldObj->get_name();
$data['description'] = htmlentities($FieldObj->get_description());
				

$data['content'] = CCTM::load_view('custom_field.php', $data);
print CCTM::load_view('templates/default.php', $data);


/*EOF*/