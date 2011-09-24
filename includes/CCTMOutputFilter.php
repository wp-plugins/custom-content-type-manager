<?php
/**
 * @package CCTMOutputFilter
 *
 * Abstract class for standardizing output filters.
 */
abstract class CCTMOutputFilter {

	/**
	 * @return string	a description of what the filter is and does.
	 */
	abstract public function get_description();


	/**
	 * Show the user how to use the filter inside a template file.
	 *
	 * @return string 	a code sample 
	 */
	abstract public function get_example();


	/**
	 * @return string	the human-readable name of the filter.
	 */
	abstract public function get_name();

	/**
	 * @return string	the URL where the user can read more about the filter
	 */
	abstract public function get_url();

}
/*EOF*/