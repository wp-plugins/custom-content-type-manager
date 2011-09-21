<?php
/*------------------------------------------------------------------------------
Tools Page: displays available tools
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Tools', CCTM_TXTDOMAIN);
$data['menu'] 		='';
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_theme';
$data['nonce_name']  = 'custom_content_type_mgr_theme';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);



// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$data['msg'] = 'Updating...';

}

$data['content'] = '';

$data['content'] = CCTM::load_view('tools.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/