<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
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

//print '<pre>'.print_r($_POST, true) . '</pre>';return;

//! Validation
// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($raw_fieldname)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</p>';
	return;
}
$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $raw_fieldname);

$def = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($def)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
	return;
}


// This gets search data that gets passed when the user refines the search.
$refined_args = array();
if (isset($_POST['search_parameters'])) {
	parse_str($_POST['search_parameters'], $refined_args);
	// print '<pre>'; print_r($refined_args); print '</pre>';
}
if (!empty($_POST)) {
	unset($_POST['action']);
	unset($_POST['get_posts_nonce']);
	unset($_POST['fieldname']);
	$search_parameters = http_build_query($_POST);
	//print '<pre>'.print_r($x, true) . '</pre>';return;	
}


// optionally get pages to exclude
if (isset($_POST['exclude'])) {
	$refined_args['exclude'] = $_POST['exclude'];
}




//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

$Q = new GetPostsQuery(); 

//print '<pre>'; print_r($refined_args); print '</pre>';
// Unsest these, otherwise the query will try to search them as custom field values.
unset($refined_args['page_number']);
unset($refined_args['fieldname']);
$refined_args = $Q->sanitize_args($refined_args);

$results_per_page = 12;

// Default Search Parameters
$default_search_params = array();
$default_search_params['post_type'] = array_keys(get_post_types());
$default_search_params['post_status'] = array('publish','inherit');
$default_search_params['omit_post_type'] = array('revision','nav_menu_item');
$default_search_params['orderby'] = 'ID';
$default_search_params['order'] = 'DESC';
$default_search_params['paginate'] = 1;
$args = CCTM::get_value($def, 'search_parameters', $default_search_params); // <-- read custom search parameters, if defined.

//$args = array_merge($args, $refined_args);
foreach ($refined_args as $k => $v) {
	$args[$k] = $v;
}
//$args = array_merge($refined_args, $args);
//print '<pre>'; print_r(get_post_types()); print '</pre>'; exit;
$page_number = (int) CCTM::get_value($_POST, 'page_number', 0);
$offset = 0;

// Template Variables
$d['fieldname'] = $raw_fieldname;
//$d['page_number'] = $page_number;
//$d['orderby'] = CCTM::get_value($refined_args,'orderby');
//$d['order'] = CCTM::get_value($refined_args,'order');

$d['menu'] = '<span class="linklike" onclick="javascript:thickbox_upload_image(\''.$raw_fieldname.'\');">Upload</span>';


// Generate a search form
$Form = new GetPostsForm();

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
$d['search_form'] = $Form->generate($search_by, $refined_args);

// Calculate offset based on page number
if (is_numeric($page_number) && $page_number > 1) {
	$offset = ($page_number - 1) * $results_per_page;
}

// Get the results

/*
$Q->paginate = true;
$Q->orderby = CCTM::get_value($refined_args,'orderby');
$Q->order = CCTM::get_value($refined_args,'order');
$Q->limit = $results_per_page;
$Q->offset = $offset;
*/

$args['paginate'] = true;
$args['orderby'] = CCTM::get_value($refined_args,'orderby');
$args['order'] = CCTM::get_value($refined_args,'order');
$args['limit'] = $results_per_page;
$args['offset'] = $offset;


$results = $Q->get_posts($args);
//print '<pre>'. $Q->debug(). '</pre>';

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