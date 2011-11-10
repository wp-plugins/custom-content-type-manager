<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');
/*------------------------------------------------------------------------------
This controller displays a selection of posts for the user to select.
------------------------------------------------------------------------------*/

// Template Variables Initialization
$d = array(); 
$d['search_parameters'] = '';
$d['fieldname'] 		= '';
$d['menu']				= '';
$d['search_form']		= '';
$d['content']			= '';
$d['page_number']		= '0'; 
$d['orderby'] 			= 'ID';
$d['order'] 			= 'ASC';

$results_per_page = 12;

// Generate a search form
// we do this AFTER the get_posts() function so the form can access the GetPostsQuery->args/defaults
$Form = new GetPostsForm();


//print '<pre>'.print_r($_POST, true) . '</pre>';return;

//! Validation
// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($raw_fieldname)) {
	print '<pre>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</pre>';
	return;
}
// More Template Variables
$d['fieldname'] = $raw_fieldname;

$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $raw_fieldname);

$def = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($def)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
	return;
}


// This gets subsequent search data that gets passed when the user refines the search.
$args = array();
if (isset($_POST['search_parameters'])) {
	// $d['content'] .= '<pre>'. print_r($_POST['search_parameters'], true).'</pre>';
	parse_str($_POST['search_parameters'], $args);
	// Pass the "view" parameters to the view
	$d['page_number'] = CCTM::get_value($args, 'page_number', 0);
	$d['orderby'] = CCTM::get_value($args, 'orderby', 'ID');
	$d['order'] = CCTM::get_value($args, 'order', 'ASC');
	// Unsest these, otherwise the query will try to search them as custom field values.
	unset($args['page_number']);
	unset($args['fieldname']);
	//print '<pre>'; print_r($args); print '</pre>';
}

// Set search boundaries (i.e. the parameters used when nothing is specified)
$defaults = array();

switch ($def['type']) {
	case 'image':
		$defaults['post_type'] = 'attachment';
		$defaults['post_mime_type'] = 'image';
		$defaults['post_status'] = array('publish','inherit');
		$defaults['orderby'] = 'ID';
		$defaults['order'] = 'DESC';
		break;
		
	case 'media':
		$defaults['post_type'] = 'attachment';
		$defaults['post_mime_type'] = 'application';
		$defaults['post_status'] = array('publish','inherit');
		$defaults['orderby'] = 'ID';
		$defaults['order'] = 'DESC';
		break;
		
	default:
		//$defaults['post_type'] = array_keys(get_post_types());
		$defaults['post_status'] = array('publish','inherit');
		//$defaults['omit_post_type'] = array('revision','nav_menu_item');
		$defaults['orderby'] = 'ID';
		$defaults['order'] = 'DESC';
}
$defaults['limit'] = $results_per_page;
$defaults['paginate'] = 1;

//print '<pre>'.print_r($defaults,true).'</pre>'; return;

// optionally get pages to exclude
if (isset($_POST['exclude'])) {
	$defaults['exclude'] = $_POST['exclude'];
}

$search_parameters_str = ''; // <-- read custom search parameters, if defined.
if (isset($def['search_parameters'])) {
	$search_parameters_str = $def['search_parameters'];
}
$additional_defaults = array();
parse_str($search_parameters_str, $additional_defaults);
//print '<pre>'.print_r($additional_defaults,true).'</pre>';
foreach($additional_defaults as $k => $v) {
	if (!empty($v)) {
		$defaults[$k] = $v;
		// $Form->Q->set_defaults(array($k,$v));
		// $args[$k] = $v; // <-- for the "narrow results" search form
	}
}


//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------


$Q = new GetPostsQuery(); 
$Q->set_defaults($defaults);
//print '<pre>'; print_r($refined_args); print '</pre>';
//$d['menu'] = '<span class="linklike" onclick="javascript:thickbox_upload_image(\''.$raw_fieldname.'\');">Upload</span>';

$page_number = CCTM::get_value($args, 'page_number', 0);
$args['offset'] = 0; // assume 0
// Calculate offset based on page number
if (is_numeric($d['page_number']) && $d['page_number'] > 1) {
	$args['offset'] = ($d['page_number'] - 1) * $results_per_page;
}


// Get the results
$results = $Q->get_posts($args);
//$d['content'] .= '<pre>'. $Q->get_args(). '</pre>';


$search_form_tpl = CCTM::load_tpl(
	array('post_selector/search_forms/'.$fieldname.'.tpl'
		, 'post_selector/search_forms/_'.$def['type'].'.tpl'
		, 'post_selector/search_forms/_default.tpl'
	)
);

$Form->set_tpl($search_form_tpl);
$Form->set_name_prefix(''); // blank out the prefixes
$Form->set_id_prefix('');
$search_by = array('search_term','yearmonth','post_type'); 
$d['search_form'] = $Form->generate($search_by, $args);


$item_tpl = '';
$wrapper_tpl = '';

// Multi Field (contains an array of values.
if (isset($def['is_repeatable']) && $def['is_repeatable'] == 1) {

	$item_tpl = CCTM::load_tpl(
		array('post_selector/items/'.$fieldname.'.tpl'
			, 'post_selector/items/_'.$def['type'].'_multi.tpl'
			, 'post_selector/items/_default.tpl'
		)
	);
	$wrapper_tpl = CCTM::load_tpl(
		array('post_selector/wrappers/'.$fieldname.'.tpl'
			, 'post_selector/wrappers/_'.$def['type'].'_multi.tpl'
			, 'post_selector/wrappers/_default.tpl'
		)
	);
}
// Simple field (contains single value)
else {	
	$item_tpl = CCTM::load_tpl(
		array('post_selector/items/'.$fieldname.'.tpl'
			, 'post_selector/items/_'.$def['type'].'.tpl'
			, 'post_selector/items/_default.tpl'
		)
	);
	$wrapper_tpl = CCTM::load_tpl(
		array('post_selector/wrappers/'.$fieldname.'.tpl'
			, 'post_selector/wrappers/_'.$def['type'].'.tpl'
			, 'post_selector/wrappers/_default.tpl'
		)
	);
}


// Placeholders for the wrapper tpl
$hash = array();
$hash['post_title'] = __('Title', CCTM_TXTDOMAIN);
$hash['post_date'] = __('Date', CCTM_TXTDOMAIN);
$hash['post_status'] = __('Status', CCTM_TXTDOMAIN);
$hash['post_parent'] = __('Parent', CCTM_TXTDOMAIN);
$hash['post_type'] = __('Post Type', CCTM_TXTDOMAIN);
$hash['search'] = __('Filter', CCTM_TXTDOMAIN);

$hash['content'] = '';
// And the items
foreach ($results as $r){
	$r['name'] = $raw_fieldname;
	$r['preview'] = __('Preview', CCTM_TXTDOMAIN);	
	$r['field_id'] = $raw_fieldname;
	add_image_size('tiny_thumb', 30, 30);
	$post_type = $r['post_type'];
	if ($post_type == 'post') {
		$r['thumbnail_src'] = CCTM_URL . '/images/wp-post.png';
	}
	elseif ($post_type == 'page') {
		$r['thumbnail_src'] = CCTM_URL . '/images/wp-page.png';	
	}
	elseif (isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
				&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0) {
		$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
		$r['thumbnail_src'] = CCTM_URL . '/images/icons/32x32/'. $baseimg;
	}
	else {
		list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'tiny_thumb', true);
		$r['thumbnail_src'] = $src;
	}
	$hash['content'] .= CCTM::parse($item_tpl, $r);
}


$d['content'] .= CCTM::parse($wrapper_tpl,$hash);

$d['content'] .= '<div class="cctm_pagination_links">'.$Q->get_pagination_links().'</div>';

print CCTM::load_view('templates/thickbox.php', $d);

/*EOF*/