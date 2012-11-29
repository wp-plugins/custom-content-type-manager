<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Take a numerical post id and converts it to a full anchor tag.
 */

class CCTM_to_link extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	mixed	optional arguments
	 * @return mixed
	 */
	public function filter($input, $options=null) {

		$output = '';
		if (is_array($options)) {
			$options = $options[0];
		}		
		$input = $this->to_array($input);
		
		if (empty($input)) {
			return '';
		}
		
		if ($this->is_array_input) {
			foreach ($input as &$item) {
				if ($item) {
					//$post = get_post($item);
					$post = get_post_complete($item);
					if (!is_object($post)) {
						$item = __('Referenced post not found.', CCTM_TXTDOMAIN);
					}
					$link_text = $post['post_title'];
					if (!empty($options)) {
						if (is_array($options) && isset($options[0])) {
							$link_text = $options[0];
						}
						else {
							$link_text = $options;
						}				
					}
					$item = sprintf('<a href="%s" title="%s">%s</a>', get_permalink($post['ID']), $post['post_title'], $link_text);
				}
			}
			return $input;
		}
		else {
			//$post = get_post($input[0]);
			$post = get_post_complete($item[0]);
			if (!is_object($post)) {
				return _e('Referenced post not found.', CCTM_TXTDOMAIN);
			}
			if ($options) {
				$link_text = $options;
			}
			else {
				$link_text = $post['post_title'];
			}		
			return sprintf('<a href="%s" title="%s">%s</a>', get_permalink($post['ID']), $post['post_title'], $link_text);
		}

	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>to_link</em> filter takes a post ID and converts it into a full anchor tag. Be default, the post title will be used as the clickable text, but you can supply your own text.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field',$fieldtype,$is_repeatable=false) {
		return "<?php print_custom_field('$fieldname:to_link', 'Click here'); ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Full link &lt;a&gt; tag', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/to_link_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/