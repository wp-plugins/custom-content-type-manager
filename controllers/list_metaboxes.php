<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
//------------------------------------------------------------------------------
/**
 * Show all available types of Custom Fields
 *
 */
$data=array();
$data['page_title'] = __('List Metaboxes', CCTM_TXTDOMAIN);
$data['help'] = 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Metaboxes';
$data['msg'] = self::get_flash();
$data['menu'] = sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm_fields&a=create_metabox" class="button">%s</a>', __('Create Metabox', CCTM_TXTDOMAIN) );

$data['content'] = '';
$data['fields'] = '';

$data['content'] .= CCTM::load_view('list_metaboxes.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/