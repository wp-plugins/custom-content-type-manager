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

$field_types = CCTM::get_available_custom_field_types();
foreach ( $field_types as $ft ) {
	$element_file = CCTM_PATH.'/includes/elements/'.$ft.'.php';
	// TODO: search alternate location
	if ( file_exists($element_file) )
	{
		include_once($element_file);
		if ( class_exists(CCTM::FormElement_classname_prefix.$ft) )
		{
			$d = array();
			$field_type_name = CCTM::FormElement_classname_prefix.$ft;
			$FieldObj = new $field_type_name();
			
			$d['name'] 			= $FieldObj->get_name();
			$d['icon'] 			= $FieldObj->get_icon();
			$d['description']	= $FieldObj->get_description();
			$d['url'] 			= $FieldObj->get_url();
			$d['type'] 			= $ft;
			$data['fields'] .= CCTM::load_view('tr_custom_field_type.php',$d);
		}
	}
}

$data['content'] = CCTM::load_view('custom_field_types.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/