<?php
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
$d = array(); // <-- Template Variables
/*------------------------------------------------------------------------------
This controller grabs one or many post by the $_POST['post_id'], formats them
and returns them to the browser as the "preview".

Should there be limits on what gets posted to this form because it does cough up 
post contents.  Is the Ajax nonce enough?

Note that the default tpls used here are the _relation*.tpl's:
	_relation.tpl for single posts
	_relation_multi.tpl for fields where "is repeatable" has been selected
------------------------------------------------------------------------------*/
// print '<pre>'.print_r($_POST, true) . '</pre>';return;
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

// Will be either the single or the multi, depending.
$tpl = '';

// Might be an array
$post_ids = CCTM::get_value($_POST,'post_id');
if (empty($post_ids)) {
	// print '<p>'.__('Post ID required.', CCTM_TXTDOMAIN).'</p>';
	return;	
}
// Multi
elseif (is_array($post_ids)) {
	// name should go to name[]
	$tpl = CCTM::load_tpl(
		array('fields/elements/'.$def['name'].'.tpl'
			, 'fields/elements/_'.$def['type'].'_multi.tpl'
			, 'fields/elements/_relation_multi.tpl'
		)
	);
}
// Single Post
else {
	$tpl = CCTM::load_tpl(
		array('fields/elements/'.$def['name'].'.tpl'
			, 'fields/elements/_'.$def['type'].'.tpl'
			, 'fields/elements/_relation.tpl'
		)
	);
}
//print $tpl; return;
// Just in case...
if (empty($tpl)) {
	print '<p>'.__('Formatting template not found!', CCTM_TXTDOMAIN).'</p>';
	return;	

}

//------------------------------------------------------------------------------
// Begin!
//------------------------------------------------------------------------------
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
//require_once(CCTM_PATH.'/includes/GetPostsForm.php');


$Q = new GetPostsQuery(); 
//$args['include'] = $post_ids;
//$results = $Q->get_posts($args);
$Q->include = $post_ids;

$results = $Q->get_posts();
//print '<pre>'. $Q->debug() . '</pre>';

//print '<pre>'.print_r($results, true).'</pre>';

// Mostly just stuff from the full object record (the post and *all* custom fields),
// but we add a couple things in here for formatting purposes.
foreach($results as $r) {

	$r = $Q->append_extra_data($r['ID']);
		
	$post_type = $r['post_type'];


	$r['id'] = $fieldname;
	$r['name'] = $fieldname;	
	$r['id_prefix'] = CCTM_FormElement::css_id_prefix;
	$r['name_prefix'] = CCTM_FormElement::post_name_prefix;

/*
	// Some translated labels and stuff
	$r['preview'] = __('Preview', CCTM_TXTDOMAIN);
	$r['remove'] = __('Remove', CCTM_TXTDOMAIN);
	$r['cctm_url'] = CCTM_URL;
	$r['id'] = $fieldname;
	$r['name'] = $fieldname;
	$r['id_prefix'] = CCTM_FormElement::css_id_prefix;
	$r['name_prefix'] = CCTM_FormElement::post_name_prefix;
	
	add_image_size('tiny_thumb', 30, 30);
	// Special handling for media attachments (i.e. photos) and for 
	// custom post-types where the custom icon has been set.
	if ($post_type == 'attachment') {
		$r['preview_url'] = $r['guid'];

		list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'tiny_thumb', true);
		$r['src_tiny_thumb'] = $src;
		$r['img_tiny_thumb'] = sprintf('<img class="mini-thumbnail" src="%s" height="30" width="30" alt="%s" />'
			, $src, $r['preview']);

		$r['img_thumbnail'] = wp_get_attachment_image( $r['ID'], 'thumbnail', true );
		list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'thumbnail', true);
		$r['src_thumbnail'] = $src;
	}
	// Other post-types
	else
	{
		$r['preview_url'] = $r['guid'].'&preview=true';
		
		if (isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
				&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0) {
			$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
			$r['src_tiny_thumb'] = CCTM_URL . '/images/icons/32x32/'. $baseimg;
			$r['img_tiny_thumb'] = sprintf('<img class="mini-thumbnail" src="%s" height="30" width="30" alt="%s" />'
				, $r['src_tiny_thumb'], $r['preview']);
			$r['img_thumbnail'] = sprintf('<img class="mini-thumbnail" src="%s" height="32" width="32" alt="%s" />'
				, $r['src_tiny_thumb'], $r['preview']);
			$r['src_thumbnail'] = $r['src_tiny_thumb'];
			
		}
		else {
			list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'tiny_thumb', true);
			$r['src_tiny_thumb'] = $src;
			$r['img_tiny_thumb'] = sprintf('<img class="mini-thumbnail" src="%s" height="30" width="30" alt="" />'
				, $src);
	
			$r['img_thumbnail'] = wp_get_attachment_image( $r['ID'], 'thumbnail', true );
			list($src, $w, $h) = wp_get_attachment_image_src( $r['ID'], 'thumbnail', true);
			$r['src_thumbnail'] = $src;
		}
	}
*/
	
	//print '<pre>'.print_r($r, true) . '</pre>'; return;
	print CCTM::parse($tpl, $r);
}

/*EOF*/