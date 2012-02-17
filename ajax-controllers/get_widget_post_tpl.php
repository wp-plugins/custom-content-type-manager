<?php
/*------------------------------------------------------------------------------
Used to get a tpl for use in the Post Content widget

@param	integer	post_id
@param	string	css_id instance of the field (has to sync with JS)
------------------------------------------------------------------------------*/
if (!defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');

require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

$post_id = CCTM::get_value($_POST, 'post_id');
$target_id = CCTM::get_value($_POST, 'target_id');
$target_name = CCTM::get_value($_POST, 'target_name');

// Will be either the single or the multi, depending.
$tpl = '';

$tpl = CCTM::load_tpl('widgets/post_item.tpl');

// Just in case...
if (empty($tpl)) {
	print '<p>'.__('Formatting template not found!', CCTM_TXTDOMAIN).'</p>';
	return;	

}

$Q = new GetPostsQuery();
$post = $Q->get_post($post_id);

$post_type = $post['post_type'];

if ($post_type == 'post') {
	$post['post_icon'] = CCTM_URL . '/images/wp-post.png';
}
elseif ($post_type == 'page') {
	$post['post_icon'] = CCTM_URL . '/images/wp-page.png';
}
elseif ( isset(CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon']) 
	&& CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] == 0 ) { 
	$baseimg = basename(CCTM::$data['post_type_defs'][$post_type]['menu_icon']);
	// die($baseimg); 
	if ( file_exists(CCTM_PATH . '/images/icons/32x32/'. $baseimg) ) {
		$post['post_icon'] = CCTM_URL . '/images/icons/32x32/'. $baseimg;
	}
}
else {
		$post['post_icon'] = CCTM_URL . '/images/broken_image.png';
}

// http://cctm:8888/sub/wp-admin/post.php?post=1214&action=edit
// http://cctm:8888/sub/wp-admin/post.php?post=1415&action=edit
$post['edit_url'] = get_admin_url(false,'post.php')."?post=$post_id&action=edit";
$post['target_id'] = $target_id;
$post['target_name'] = $target_name;

print CCTM::parse($tpl, $post);

/*EOF*/