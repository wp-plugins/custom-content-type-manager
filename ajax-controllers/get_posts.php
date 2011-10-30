<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
/*------------------------------------------------------------------------------
This controller displays a selection of posts for the user to select.
------------------------------------------------------------------------------*/

// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($raw_fieldname)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</p>';
}
$fieldname = preg_replace('/^'. CCTMFormElement::css_id_prefix . '/', '', $raw_fieldname);

$field_data = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($field_data)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
}

//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

// Template Variables
$d = array();
$d['page_title'] = 'Select Post';
$d['menu'] = '<span onclick="javascript:thickbox_upload_image(\''.$raw_fieldname.'\');">Upload</span>';
$d['content'] =  '';

$args = CCTM::get_value($field_data, 'search_parameters', array());
$Q = new GetPostsQuery($args);
$results = $Q->get_posts();


$d['search_form'] = '<p>Search form here...</p>';

$d['content'] .= '<ul>';
foreach ($results as $r){
	$r['field_id'] = $raw_fieldname;
	$d['content'] .= CCTM::load_view('li_post_selector.php', $r);
}
$d['content'] .= '</ul>';

print CCTM::load_view('templates/thickbox.php', $d);

/*EOF*/