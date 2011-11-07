<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');
/*------------------------------------------------------------------------------
This controller retrieves a search form
------------------------------------------------------------------------------*/
$fieldtype = CCTM::get_value($_POST, 'fieldtype');
if (empty($fieldtype)) {
	print '<p>'.sprintf(__('Invalid field type: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldtype).'</em>') .'</p>';
}
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

$Form = new GetPostsForm();

$search_by = array();
$search_by[] = 'post_type';
$search_by[] = 'taxonomy';
$search_by[] = 'taxonomy_term';
$search_by[] = 'search_term';
$search_by[] = 'search_columns';

print $Form->generate($search_by);

/*EOF*/