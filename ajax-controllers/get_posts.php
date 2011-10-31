<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
$d = array(); // <-- Template Variables
/*------------------------------------------------------------------------------
This controller displays a selection of posts for the user to select.
------------------------------------------------------------------------------*/

// Some Tests first to see if the request is valid...
$raw_fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($raw_fieldname)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($raw_fieldname).'</em>') .'</p>';
	return;
}
$fieldname = preg_replace('/^'. CCTM_FormElement::css_id_prefix . '/', '', $raw_fieldname);

$field_data = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($field_data)) {
	print '<p>'.sprintf(__('Invalid fieldname: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
	return;
}



//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

// This gets search data that gets passed when the user refines the search.
$refined_args = array();
if (isset($_POST['search_parameters'])) {
	parse_str($_POST['search_parameters'], $refined_args);
	//print "<pre>". print_r($refined_args, true) . '</pre>'; 
	//exit;
}


$results_per_page = 10;
$args = CCTM::get_value($field_data, 'search_parameters', array()); // <-- read custom search parameters, if defined.
$args = array_merge($args, $refined_args);
$page_number = (int) CCTM::get_value($_POST, 'page_number', 0);
$offset = 0;

// Template Variables
$d['menu'] = '<span class="linklike" onclick="javascript:thickbox_upload_image(\''.$raw_fieldname.'\');">Upload</span>';
$d['content'] =  sprintf('<input type="hidden" id="fieldname" value="%s" />', $raw_fieldname) 
	. sprintf('<input type="hidden" id="page_number" value="%s" />', $page_number);
$d['search_form'] = '';


// Generate a search form
$Form = new GetPostsForm();
/*
$tpl = '
		<form id="refine_search">
			<div id="[+search_term.id+]_wrapper" class="[+wrapper_class+]">
				<label for="[+id_prefix+][+search_term.id+]" class="[+label_class+]" id="[+search_term.id+]_label">[+search_term.label+]</label>
				<input class="[+input_class+] input_field" type="text" name="[+name_prefix+][+search_term.id+]" id="[+id_prefix+][+search_term.id+]" value="[+search_term.value+]" />
			</div>
			
			<div id="[+yearmonth.id+]_wrapper" class="[+wrapper_class+]">
				<label for="[+id_prefix+][+yearmonth.id+]" class="[+label_class+]" id="[+id+]_label">[+label+]</label>
				
				<select size="[+yearmonth.size+]" name="[+name_prefix+][+yearmonth.name+]" class="[+input_class+]" id="[+id_prefix+][+yearmonth.id+]">
					<option value=""></option>
					[+yearmonth.options+]
				</select>
			</div>

			<span class="button" onclick="javascript:refine_search(\'refine_search\');">[+search+]</span>			
		</form>';
*/

$tpl = '
		<form id="refine_search">
				<label for="[+id_prefix+][+search_term.id+]" class="[+label_class+]" id="[+search_term.id+]_label">[+search_term.label+]</label>
				<input class="[+input_class+] input_field" type="text" name="[+name_prefix+][+search_term.id+]" id="[+id_prefix+][+search_term.id+]" value="[+search_term.value+]" />

			
				<label for="[+id_prefix+][+yearmonth.id+]" class="[+label_class+]" id="[+id+]_label">[+yearmonth.label+]</label>
				
				<select size="[+yearmonth.size+]" name="[+name_prefix+][+yearmonth.name+]" class="[+input_class+]" id="[+id_prefix+][+yearmonth.id+]">
					<option value=""></option>
					[+yearmonth.options+]
				</select>

			[+order+]

			<span class="button" onclick="javascript:refine_search(\'refine_search\');">[+search+]</span>
			<span class="button" onclick="javascript:change_page(0);">Show All</span>
		</form>';
		
$Form->set_tpl($tpl);
$Form->set_name_prefix(''); // blank out the prefixes
$Form->set_id_prefix('');
$search_by = array('search_term','yearmonth','order'); 
$d['search_form'] = $Form->generate($search_by, $refined_args);

// Calculate offset based on page number
if (is_numeric($page_number) && $page_number > 1) {
	$offset = ($page_number - 1) * $results_per_page;
}

// Get the results
$Q = new GetPostsQuery($args);
$Q->paginate = true;
$Q->limit = $results_per_page;
$Q->offset = $offset;

$results = $Q->get_posts();


$d['content'] .= '<ul>';
foreach ($results as $r){
	$r['field_id'] = $raw_fieldname;
	add_image_size('tiny_thumb', 30, 30);
	list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'tiny_thumb', true);
	$r['thumbnail_src'] = $src;
	$d['content'] .= CCTM::load_view('li_post_selector.php', $r);
}
$d['content'] .= '</ul>';

$d['content'] .= '<div class="summarize-posts-pagination-links">'.$Q->get_pagination_links().'</div>';

print CCTM::load_view('templates/thickbox.php', $d);

/*EOF*/