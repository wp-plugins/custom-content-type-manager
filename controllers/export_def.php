<?php
/*------------------------------------------------------------------------------
Export a content type definition to a .json file
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('Export Definition', CCTM_TXTDOMAIN);
$data['menu'] 		='';
$data['msg']		= '';
$data['action_name']  = 'custom_content_type_mgr_export';
$data['nonce_name']  = 'custom_content_type_mgr_export';
$data['submit']   = __('Save', CCTM_TXTDOMAIN);
$data['content'] = '';


// If properly submitted, Proceed with saving settings and exporting def.
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	$data['msg'] = 'Updating...';

}

// Populate the values
$data['title'] = CCTM::get_value(self::$data['export_info'], 'title');
$data['author'] = CCTM::get_value(self::$data['export_info'], 'author');
$data['url'] = CCTM::get_value(self::$data['export_info'], 'url');
$data['description'] = CCTM::get_value(self::$data['export_info'], 'description');
$data['template_url'] = CCTM::get_value(self::$data['export_info'], 'template_url');

$data['content'] = CCTM::load_view('export.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/