<?php
/**
 * This widget is designed to allow users to display content from a single post in a widget.
 * Functionality inspired by the Custom Post Widget plugin: 
 * 	http://wordpress.org/extend/plugins/custom-post-widget/
 */
class CCTM_Post_Widget extends WP_Widget {

	public $name;
	public $description;
	public $control_options = array(
		'title' => 'Post'
	);
	
	public function __construct() {
		$this->name = __('Post Content', CCTM_TXTDOMAIN);
		$this->description = __("Show a post's content inside of a widget.", CCTM_TXTDOMAIN);
		$widget_options = array(
			'classname' => __CLASS__,
			'description' => $this->description,
		);
		
		parent::__construct(__CLASS__, $this->name, $widget_options, $this->control_options);

		wp_enqueue_script('thickbox');
		wp_register_script('cctm_post_widget', CCTM_URL.'/js/post_widget.js', array('jquery', 'media-upload', 'thickbox'));
		wp_enqueue_script('cctm_post_widget');	}

	//------------------------------------------------------------------------------
	/**
	 * Create only form elements.
	 */
	public function form($instance) {
		
		require_once(CCTM_PATH.'/includes/GetPostsQuery.php');

		$formatted_post = ''; // Formatted post
		
		if (!isset($instance['title'])) {
			$instance['title'] = ''; 	// default value
		}
		if (!isset($instance['post_id'])) {
			$instance['post_id'] = ''; 	// default value
		}
		else {
			$Q = new GetPostsQuery();
			$post = $Q->get_post($instance['post_id']);
		}
		if (!isset($instance['formatting_string'])) {
			$instance['formatting_string'] = '[+post_content+]'; 	// default value
		}
		if (!isset($instance['post_type'])) {
			$instance['post_type'] = 'post'; 	// default value
		}
		
		$post_types = get_post_types(array('public'=>1));
		
		$post_type_options = '';

		foreach ($post_types as $k => $v) {
			$is_selected = '';
			if ($k == $instance['post_type']) {
				$is_selected = ' selected="selected"';	
			}
			$post_type_options .= sprintf('<option value="%s" %s>%s</option>', $k, $is_selected, $v);
		}

		
		print '<p>'.$this->description
			. '<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Post_Widget"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a></p>
			<label class="cctm_label" for="'.$this->get_field_id('post_type').'">Post Type</label>

			<select name="'.$this->get_field_name('post_type').'" id="'.$this->get_field_id('post_type').'">
				'.$post_type_options.'
			</select>
			<span class="button" onclick="javascript:select_post(\''.$this->get_field_id('target_id').'\',\''.$this->get_field_name('target_id').'\',\''.$this->get_field_id('post_type').'\');">'. __('Choose Post', CCTM_TXTDOMAIN).'</span>

			<br/><br/>
			<strong>Selected Post</strong><br/>
			<!-- also target for Ajax writes -->
			<div id="'.$this->get_field_id('target_id').'"></div>
			<!-- Thickbox ID -->
			<div id="target_'.$this->get_field_id('target_id').'"></div>
			<br/><br/>
			
			<input type="checkbox" value="1"/> <label class="">Override Post Title</label>
			<label class="cctm_label" for="'.$this->get_field_id('title').'">'.__('Title', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.$instance['title'].'" />
			
			
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

		$post_id = (int) $instance['post_id'];
		
		$Q = new GetPostsQuery();
		
		$post = $Q->get_post($post_id);
		
		$output = $args['before_widget']
			.$args['before_title'].$instance['title'].$args['after_title']
			.'<ul>';
		foreach ($results as $r) {
			$output .= CCTM::parse($instance['formatting_string'], $r);
		}
		$output .= '</ul>'
		
		. $args['after_widget'];
		
		print $output;
	}

	
	//! Static
	public static function register_this_widget() {
		register_widget(__CLASS__);
	}

}

/*EOF*/