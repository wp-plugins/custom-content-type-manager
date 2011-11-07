<?php
/**
 * A class for programmatically creating posts with all their custom fields via 
 * a unified API.  It is similar to wp_insert_post() but with several important
 * differences:
 *	1. It does not call any actions or filters when it is executed, so for better
 *		or for worse, there is no way for 3rd parties to intervene with this.
 *	2. It automatically creates/updates/deletes all custom fields in the postmeta
 *		table without the need to have use the update_post_meta() and related functions.
 *	3. It does not check for user permissions. If you're running around in the PHP
 *		code, you have rull run of the database. 
 *
 * @pacakge SummarizePosts
 */
class SP_Post {

	private static $wp_posts_columns = array(
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content',
		'post_title',
		'post_excerpt',
		'post_status',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping',
		'pinged',
		'post_modified',
		'post_modified_gmt',
		'post_content_filtered',
		'post_parent',
		'guid',
		'menu_order',
		'post_type',
		'post_mime_type',
		'comment_count'
	);

	public $errors = array();
	
	public $props = array();
	
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __construct($props=array()) {
		
	}

	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __get($k){
		
	}

	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __isset($k) {
	
	}

	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __unset($k) {
	
	}
	
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function __set($k,$v){
		
	}
	
	//------------------------------------------------------------------------------
	//! Private Functions
	//------------------------------------------------------------------------------
	/**
	 * Tests whether a string is valid for use as a MySQL column name.  This isn't 
	 * 100% accurate, but the postmeta virtual columns can be more flexible.
	 * @param	string
	 * @return	boolean
	 */
	private function _is_valid_column_name($str) {
		if (preg_match('/[^a-zA-Z0-9\/\-\_]/', $str)) {
			return false;
		}
		else {
			return true;
		}
	}	
	
	//------------------------------------------------------------------------------
	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function delete() {
	
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Ties into GetPostsQuery, but offers a bit more flexibility.
	 *
	 * @param	mixed	$args	integer ID, or valid search params for GetPostsQuery
	 */
	public function get($args) {
	
	}
	
	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function insert($args) {
		// unset(ID)
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Intelligently switch between insert/update
	 *
	 * @return	boolean	true on success, false on failure
	 */
	public function save() {
		// filter data
		// check whether it's an update or insert operation.
		// update or insert the data
		
	}


	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function update() {
	
	}
}


/*EOF*/