<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('edit_posts')) exit('You do not have permission to do that.');

function proper_parse_str($str) {
  # result array
  $arr = array();

  # split on outer delimiter
  $pairs = explode('&', $str);

  # loop through each pair
  foreach ($pairs as $i) {
    # split into name and value
    list($name,$value) = explode('=', $i, 2);
    
    # if name already exists
    if( isset($arr[$name]) ) {
      # stick multiple values into an array
      if( is_array($arr[$name]) ) {
        $arr[$name][] = $value;
      }
      else {
        $arr[$name] = array($arr[$name], $value);
      }
    }
    # otherwise, simply stick it in a scalar
    else {
      $arr[$name] = $value;
    }
  }

  # return result array
  return $arr;
}
/*------------------------------------------------------------------------------
This controller retrieves a search form
It expects the fieldname (without the cctm_ prefix).
It also accepts the search_parameters (serialized data describing existing values)
------------------------------------------------------------------------------*/
$fieldname = CCTM::get_value($_POST, 'fieldname');
if (empty($fieldname)) {
	print '<p>'.sprintf(__('Invalid field name: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
}

$def = CCTM::get_value(CCTM::$data['custom_field_defs'], $fieldname);
if (empty($def)) {
	print '<p>'.sprintf(__('Invalid field name: %s', CCTM_TXTDOMAIN), '<em>'. htmlspecialchars($fieldname).'</em>') .'</p>';
	return;
}

$search_parameters_str = '';
if (isset($_POST['search_parameters'])) {
	$search_parameters_str = $_POST['search_parameters'];
}
//print '<pre>'.$search_parameters_str. '</pre>'; return;
$existing_values = array();
parse_str($search_parameters_str, $existing_values);

//print '<pre>'.print_r($existing_values, true) . '</pre>'; 
require_once(CCTM_PATH.'/includes/SummarizePosts.php');
require_once(CCTM_PATH.'/includes/GetPostsQuery.php');
require_once(CCTM_PATH.'/includes/GetPostsForm.php');

$Form = new GetPostsForm();

// How should we search?
$search_by = array();

$search_by[] = 'post_type';
$search_by[] = 'taxonomy';
$search_by[] = 'taxonomy_term';
$search_by[] = 'post_parent';
$search_by[] = 'meta_key';
$search_by[] = 'meta_value';
//$search_by[] = 'search_term';
//$search_by[] = 'search_columns';


$form_tpl = '
<style>
[+css+]
</style>
<p>This form will determine which posts will be selectable when users create or edit a post that uses this field.</p>
<form id="search_parameters_form" class="[+form_name+]">
	[+content+]
	<span class="button" onclick="javascript:save_search_parameters(\'search_parameters_form\');">Save</span>
	<span class="button" onclick="javascript:tb_remove();">Cancel</span>
</form>
';

$Form->set_name_prefix('');
$Form->set_id_prefix('');

$Form->set_tpl($form_tpl);
print $Form->generate($search_by, $existing_values);

/*EOF*/