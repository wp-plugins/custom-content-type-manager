<?php
/*------------------------------------------------------------------------------
Settings Page
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Settings', CCTM_TXTDOMAIN);
$data['menu'] 		='';
$data['msg']		= self::get_flash();
$data['action_name']  = 'custom_content_type_mgr_settings';
$data['nonce_name']  = 'custom_content_type_mgr_settings';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);

// Add links to any custom field settings here
$data['content'] = ''; 

// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	self::$data['settings']['delete_posts'] 			= (int) CCTM::get_value($_POST, 'delete_posts', 0);	
	self::$data['settings']['delete_custom_fields'] 	= (int) CCTM::get_value($_POST, 'delete_custom_fields', 0);
	self::$data['settings']['add_custom_fields'] 		= (int) CCTM::get_value($_POST, 'add_custom_fields', 0);
	self::$data['settings']['update_custom_fields'] 	= (int) CCTM::get_value($_POST, 'update_custom_fields', 0);
	self::$data['settings']['show_custom_fields_menu']	= (int) CCTM::get_value($_POST, 'show_custom_fields_menu', 0);
	self::$data['settings']['show_settings_menu'] 		= (int) CCTM::get_value($_POST, 'show_settings_menu', 0);
	update_option( self::db_key, self::$data );

	$data['msg'] = '<div class="updated"><p>'
		. __('Settings have been updated.', CCTM_TXTDOMAIN )
		.'</p></div>';
	self::set_flash($data['msg']);
	print '<script type="text/javascript">window.location.replace("?page=cctm_settings");</script>';
	return;
}
// print "<pre>"; print_r(self::$data['settings']); print "</pre>"; // exit; 
//! Defaults 
$data['settings'] = array(
	'delete_posts' => 0
	, 'delete_custom_fields' => 0
	, 'add_custom_fields' => 0
	, 'update_custom_fields' => 0
 	, 'show_custom_fields_menu' => 1
 	, 'show_settings_menu' => 1
 	
);

// this only works for checkboxes...
foreach ( $data['settings'] as $k => $v) {
	if (isset(self::$data['settings'][$k]) && self::$data['settings'][$k]) {
		$data['settings'][$k] = ' checked="checked"';
	}
}


$data['content'] .= CCTM::load_view('settings.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/