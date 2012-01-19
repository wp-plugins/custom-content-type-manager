<?php
/**
 * @package CCTM_OutputFilter
 * 
 * Converts an array of image ids to HTML.
 */

class CCTM_gallery extends CCTM_OutputFilter {

	/**
	 * Apply the filter.
	 *
	 * @param 	integer 	input
	 * @param	string	optional formatting tpl
	 * @return mixed
	 */
	public function filter($input, $options='<div class="cctm_gallery" id="cctm_gallery_[+i+]"><img src="[+guid+]" alt="[+post_title+]/></div>') {
	
		if (empty($input)) {
			return '';
		}

		$the_array = array();
		
		if (is_array($input)) {
			$the_array = $input; // No JSON converting necessary: PHP array supplied.
		}
		else {
			$output = json_decode($input, true);
	
			// See http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=121
			if ( !is_array($output) ) {
				$the_array = array($output);
			}
			else {
				$the_array = $output;
			}
		}
		
		$output = '';
		$i = 1;
		foreach($the_array as $image_id) {
			$p = get_post_complete($image_id);
			$p['i'] = $i;
			$output .= CCTM::parse($options, $p);
			$i++;
		}
		
		return $output;
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __("The <em>gallery</em> filter converts a list of image IDs into HTML img tags.", CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	public function get_example($fieldname='my_field') {
		return "<?php print_custom_field('".$fieldname.":gallery'; ?>";
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Gallery', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/gallery_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/