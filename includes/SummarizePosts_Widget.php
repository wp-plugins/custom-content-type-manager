<?php
class SummarizePosts_Widget extends WP_Widget {

	public $name = 'Summarize Posts';
	public $description = 'List your posts via a dynamic and flexible search form.';
	public $control_options = array(
		'title' => 'Posts'
	);
	
	public function __construct() {
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);
		
		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);

		wp_enqueue_script('thickbox');
		wp_register_script('summarizeposts_widget', CCTM_URL.'/js/summarizeposts.js', array('jquery', 'media-upload', 'thickbox'));
		wp_enqueue_script('summarizeposts_widget');
	}

	//------------------------------------------------------------------------------
	/**
	 * 
	 */
	public function form($instance) {

		print '<span class="button" onclick="javascript:widget_summarize_posts(\''.$this->get_field_id('selector') . '\');">Define Search</span>
			<div id="target_'.$this->get_field_id('selector').'"></div>';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Process the $args to something GetPostsQuery. 
	 */
	function widget($args, $instance) {
		require_once(CCTM.'/includes/SummarizePosts.php');
		require_once(CCTM.'/includes/GetPostsQuery.php');
		$Q = new GetPostsQuery();
		$results = $Q->get_posts($args);
		foreach ($results as $r) {
			print_r($r);
		}
	}

	
	//! Static
	public static function register_this_widget() {
		register_widget(__CLASS__);
	}

}

/*EOF*/