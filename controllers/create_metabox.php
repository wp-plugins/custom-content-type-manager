<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Create a Metabox
------------------------------------------------------------------------------*/

// Page variables
$data = array();
$data['page_title'] = __('Create Metabox', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Metaboxes';
$data['msg'] = '';
$data['menu'] = sprintf('<a href="'.get_admin_url(false, 'admin.php').'?page=cctm_fields&a=list_metaboxes" title="%s" class="button">%s</a>', __('Cancel'), __('Cancel'));
$data['action_name']  = 'custom_content_type_mgr_create_metaboxes';
$data['nonce_name']  = 'custom_content_type_mgr_create_metaboxes_nonce';
// $data['change_field_type'] = '<br/>';

$field_data = array(); // Data object we will save


// Save if submitted...
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	print 'Saving...'; exit;
}

$data['fields'] = '';
$data['icon'] = sprintf('<img src="%s" class="cctm-field-icon" id="cctm-field-icon-%s"/>'
	, '', '');

$data['name'] = '';
$data['description'] = htmlspecialchars('');


$data['content'] = CCTM::load_view('metabox.php', $data);
print CCTM::load_view('templates/default.php', $data);


/*EOF*/