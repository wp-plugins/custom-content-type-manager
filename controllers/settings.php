<?php
/*------------------------------------------------------------------------------
Settings Page
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Settings', CCTM_TXTDOMAIN);
$data['menu'] 		='';
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_settings';
$data['nonce_name']  = 'custom_content_type_mgr_settings';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);



// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$data['msg'] = 'Updating...';

}

$data['content'] = 'Settings go here...';

$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/