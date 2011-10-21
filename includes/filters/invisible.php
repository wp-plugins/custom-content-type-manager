<?php
/**
 * @package CCTM_invisible
 * 
 * The "invisible" filter isn't really a filter at all: it should only be 
 * used for "helper" custom fields that do not save data to the database
 * and therefore do not have any meta data to print.  
 *
 * Since the sample templates rely on the get_example() function here to
 * demonstrate how to use the particular field in a template file, you
 * can specify an "invisible" output filter on any custom field class
 * to make its example invisible to the sample template.
 */

class CCTM_invisible extends CCTMOutputFilter {

	/**
	 * Don't show this filter in any dropdown menus for a Default Output Filter
	 */
	public $show_in_menus = false;
	
	/**
	 * Apply the filter.
	 *
	 * @param 	mixed 	input
	 * @param	mixed	optional arguments
	 * @return mixed
	 */
	public function filter($input, $options=null) {
		return '';
	}


	/**
	 * @return string	a description of what the filter is and does.
	 */
	public function get_description() {
		return __('The <em>invisible</em> is only used on custom fields that should not be used in a template file.  This rare circumstance happens when a custom field appears only in the manager, e.g. a helper field that assists the admin or editor users enter data.  If a field has specified <em>invisible</em> as its output filter, then that field does not contain data and will not be available in a theme file.', CCTM_TXTDOMAIN);
	}


	/**
	 * Show the user how to use the filter inside a template file. The invisible filter 
	 * should be used when a field should NOT be used inside a template file.
	 *
	 * @return string 	a code sample (blank in this case)
	 */
	public function get_example($fieldname='my_field') {
		return '';
	}


	/**
	 * @return string	the human-readable name of the filter.
	 */
	public function get_name() {
		return __('Invisble', CCTM_TXTDOMAIN);
	}

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	public function get_url() {
		return __('http://code.google.com/p/wordpress-custom-content-type-manager/wiki/invisible_OutputFilter', CCTM_TXTDOMAIN);
	}
		
}
/*EOF*/