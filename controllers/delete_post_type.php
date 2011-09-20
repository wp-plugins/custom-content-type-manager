<?php
/*------------------------------------------------------------------------------
* Confirm Delete/Deletes a custom post type definition. 
* @param string $post_type
------------------------------------------------------------------------------*/

$data 				= array();
$data['page_title']	= sprintf( __('Delete Content Type: %s', CCTM_TXTDOMAIN), $post_type );
$data['menu'] 		= ''; 
$data['msg']		= CCTM::get_flash();
$data['action_name'] = 'custom_content_type_mgr_delete_content_type';
$data['nonce_name'] = 'custom_content_type_mgr_delete_content_type_nonce';
$data['submit']   = __('Delete', CCTM_TXTDOMAIN);
$data['fields']   = '';

// We can't delete built-in post types
if (!self::_is_existing_post_type($post_type, false ) ) {
	include(CCTM_PATH.'/controllers/error.php');
	return;
}

// If properly submitted, Proceed with deleting the post type
if ( !empty($_POST) && check_admin_referer($data['action_name'], $data['nonce_name']) ) {
	unset(self::$data['post_type_defs'][$post_type]); // <-- Delete this node of the data structure
	update_option( self::db_key, self::$data );
	$msg = '<div class="updated"><p>'
		.sprintf( __('The post type %s has been deleted', CCTM_TXTDOMAIN), "<em>$post_type</em>")
		. '</p></div>';
	self::set_flash($msg);
	include( CCTM_PATH . '/controllers/list_post_types.php');
//	print CCTM::load_view('list_post_types.php', $data);
	return;
}

$data['content'] = '<div class="error">
	<img src="'.CCTM_URL.'/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
	<p>'
	. sprintf( __('You are about to delete the %s post type. This will remove all of its settings from the database, but this will NOT delete any rows from the wp_posts table. However, without a custom post type defined for those rows, they will be essentially invisible to WordPress.', CCTM_TXTDOMAIN), "<em>$post_type</em>" )
	.'</p>'
	. '<p>'.__('Are you sure you want to do this?', CCTM_TXTDOMAIN).'
	<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeletePostType" title="Deleting a content type" target="_blank">
	<img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" />
	</a>
	</p></div>';
		
$data['content'] = CCTM::load_view('basic_form.php', $data);
print CCTM::load_view('templates/default.php', $data);

/*EOF*/