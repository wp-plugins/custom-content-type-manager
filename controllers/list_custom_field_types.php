<?php
//------------------------------------------------------------------------------
/**
 * Show all available types of Custom Fields
 *
 */

$data=array();
$data['page_title'] = __('Add Field: Choose Type of Custom Field', CCTM_TXTDOMAIN);
$data['msg'] = self::get_flash();
$data['menu'] = sprintf('<a href="?page=cctm_fields&a=list_custom_fields" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) );
$data['fields'] = '';

$element_files = CCTM::get_available_custom_field_types();
foreach ( $element_files as $file ) {
	include_once($file);
	$field_type = basename($file);
	$field_type = preg_replace('/\.php$/', '', $field_type);

	if ( class_exists(CCTM::FormElement_classname_prefix.$field_type) )
	{
		$d = array();
		$field_type_name = CCTM::FormElement_classname_prefix.$field_type;
		$FieldObj = new $field_type_name();
		
		$d['name'] 			= $FieldObj->get_name();
		$d['icon'] 			= $FieldObj->get_icon();
		$d['description']	= $FieldObj->get_description();
		$d['url'] 			= $FieldObj->get_url();
		$d['type'] 			= $field_type;
		$data['fields'] .= CCTM::load_view('tr_custom_field_type.php',$d);
	}
}

$data['content'] = CCTM::load_view('custom_field_types.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/