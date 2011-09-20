<?php
/*------------------------------------------------------------------------------
* Confirm Delete/Deletes a custom field definition
* @param string $field_name (unique name of field)
------------------------------------------------------------------------------*/

$data 				= array();
$data['page_title']	= sprintf( __('Delete Custom Field: %s', CCTM_TXTDOMAIN), $field_name );
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_delete_field';
$data['nonce_name'] = 'custom_content_type_mgr_delete_field_nonce';
$data['submit']   = __('Delete', CCTM_TXTDOMAIN);
$data['fields']   = '';

/*
print "<pre>";
print_r(self::$data['custom_field_defs']);
print "</pre>";
exit;
*/
// Make sure the field exists
if (!array_key_exists($field_name, self::$data['custom_field_defs'])) {
	$msg_id = 'invalid_field_name';
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	unset(self::$data['custom_field_defs'][$field_name]); 
	update_option( self::db_key, self::$data );
	$msg = '<div class="updated"><p>'
		.sprintf( __('The custom field %s has been deleted', CCTM_TXTDOMAIN), "<em>$field_name</em>")
		. '</p></div>';
	self::set_flash($msg);
	include( CCTM_PATH . '/controllers/list_custom_fields.php');
	return;
}

$data['content'] = '<div class="error">
	<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
	<p>'
	. sprintf( __('You are about to delete the %s custom field. This will remove all of its settings from the database, but this will NOT delete any data from the wp_postmeta table. However, without a definition for this field, it will be mostly invisible to WordPress.', CCTM_TXTDOMAIN), "<em>$field_name</em>" )
	.'</p>'
	. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
	<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeletePostType" title="Deleting a content type" target="_blank">
	<img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" />
	</a>
	</p></div>';
		
$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/