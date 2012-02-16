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
	 * Create only form elements.
	 */
	public function form($instance) {
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$args_str = ''; // formatted args for the user to look at so they remember what they searched for.
		
		if (!isset($instance['parameters'])) {
			$instance['parameters'] = ''; 	// default value
		}
		if (!isset($instance['formatting_string'])) {
			$instance['formatting_string'] = '<li><a href="[+permalink+]">[+post_title+]</a></li>'; 	// default value
		}

		$args = array();
		$search_parameters_str = $instance['parameters'];
		parse_str($search_parameters_str, $args);
		$Q = new GetPostsQuery($args);
		$args_str = $Q->get_args();

		
		print '<p>'.__('List posts according to flexible search criteria.', CCTM_TXTDOMAIN)
			. '<a href="http://code.google.com/p/wordpress-custom-content-type-manager/"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a></p>
			<h4>'.__('Search Criteria', CCTM_TXTDOMAIN).'</h4>
			
			<span class="button" onclick="javascript:widget_summarize_posts(\''.$this->get_field_id('parameters') . '\');">'.__('Define Search', CCTM_TXTDOMAIN).'</span>
			
			<!-- also target for Ajax writes -->
			<div id="existing_'.$this->get_field_id('parameters').'">'.
			$args_str
			.'</div>
			<input type="hidden" name="'.$this->get_field_name('parameters').'" id="'.$this->get_field_id('parameters').'" value="'.$instance['parameters'].'" />
			

			<div id="target_'.$this->get_field_id('selector').'"></div>
			
			<label class="cctm_label" for="'.$this->get_field_id('formatting_string').'">'.__('Formatting String', CCTM_TXTDOMAIN).'</label>
			<textarea name="'.$this->get_field_name('formatting_string').'" id="'.$this->get_field_id('formatting_string').'" rows="3" cols="30">'.$instance['formatting_string'].'</textarea>
			';
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Process the $args to something GetPostsQuery. 
	 */
	function widget($args, $instance) {
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$args = array();
		$search_parameters_str = $instance['parameters'];
		parse_str($search_parameters_str, $args);
		
		$Q = new GetPostsQuery();
		
		$results = $Q->get_posts($args);
		
		$output = '<ul>';
		foreach ($results as $r) {
			$output .= CCTM::parse($instance['formatting_string'], $r);
		}
		$output .= '</ul>';
		
		print $output;
	}

	
	//! Static
	public static function register_this_widget() {
		register_widget(__CLASS__);
	}

}

/*EOF*/