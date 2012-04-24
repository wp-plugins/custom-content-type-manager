<?php
/**
 * This handles custom columns when creating lists of posts/pages in the manager.
 * We use an object here so we can rely on a "dynamic funciton name" via __call() where
 * the function called corresponds to the post-type name. 
 *
 * WARNING: this requires that the post-type is named validly, i.e. in a name that would
 * be valid as a PHP function.
 * See 
 *	http://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
 *	http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
 *
 * manage_edit-${post_type}_columns 
 */
class CCTM_Columns {

	/**
	 * Sets the post-type, e.g. 'books'
	 */
	public $post_type; 
	
	/**
	 *
	 * @param	string	$post_type
	 * @param	array	$default_columns associative array (set by WP);
	 * @return	array	associative array of column ids and translated names.
	 */
	public function __call($post_type, $default_columns) {
		//die('xhere.');
		//return $default_columns;
		$custom_columns = array('cb' => '<input type="checkbox" />',); // the output
		$built_in_columns = array(
			//'cb' => '<input type="checkbox" />',
			'title' => __('Title'), // post_title
			'author' => __('Author'), // lookup on wp_users
			'comments' => __('Comments'),
			'date' => __('Date')
		);
		$raw_columns = array();
		if (isset(CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'])) {
			$raw_columns = CCTM::$data['post_type_defs'][$post_type]['cctm_custom_columns'];
		}
		// The $raw_columns contains a simple array, e.g. array('field1','wonky');
		// we need to create an associative array.
		// Look up what kind of column this is.
		foreach ($raw_columns as $c) {
			if (isset($built_in_columns[$c])) {
				$custom_columns[$c] = $built_in_columns[$c]; // already translated
			}
			// Custom Field
			elseif (isset(CCTM::$data['custom_field_defs'][$c])) {
				$custom_columns[$c] = __(CCTM::$data['custom_field_defs'][$c]['label']);
			}
			// Taxonomy
			elseif (false) {
			
			}
		}
		return $custom_columns;
		// die('here'. $post_type);	
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Populate the custom data for a given column.  This function should actually
	 * *print* data, not just return it.
	 * Oddly, WP doesn't even send the column this way unless it is something custom.
	 *
	 */
	public function populate_custom_column_data($column) {

/*
		if ('ID' == $column) echo $post->ID;
		elseif ('agent' == $column) echo 'agent-name';
		elseif ('price' == $column) echo 'money';
		elseif ('status' == $column) echo 'status';
*/

		print_custom_field($column);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Custom joining on postmeta table for sorting on custom columns
	 */
	public function posts_join($join) {
	
		global $wpdb;
	
		// We don't want searches
		if(is_search() ) {
			return $join;
		}
		
		$post_type = CCTM::get_value($_GET, 'post_type');
		if (empty($post_type)) {
			return $join;
		}
		if (isset(CCTM::$data['post_type_defs'][$post_type]['custom_orderby']) && !empty(CCTM::$data['post_type_defs'][$post_type]['custom_orderby'])) {
			$column = CCTM::$data['post_type_defs'][$post_type]['custom_orderby'];
			// Req'd to sort on custom column
			if (!in_array($column, CCTM::$reserved_field_names)) {
				$join .= $wpdb->prepare(" LEFT JOIN {$wpdb->postmeta} ON  {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = %s", $column);
			}
		}
			
		return $join;	
	}
	
	public function posts_where($where) {
	
	}

	public function posts_groupby($groupby) {
	
	}
}
/*EOF*/