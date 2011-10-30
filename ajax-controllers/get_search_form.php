<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
/*------------------------------------------------------------------------------
This controller retrieves a search form
------------------------------------------------------------------------------*/
$fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($fieldname)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
}
$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $fieldname);

$field_data = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($field_data)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
}
print '<pre>';
print_r($field_data);
print '</pre>';
?>