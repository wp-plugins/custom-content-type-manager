<?php
/**
 * @package CCTM_Validator
 *
 * Abstract class for validation rules.  Classes that implement 
 */
abstract class CCTM_Validator {

	/**
	 * If a field does not validate, set this message.
	 */
	public $error_msg;
	
	/**
	 * Most validation rules should be publicly visible when you define a field,
	 * but if desired, you can hide a rule from the menu.
	 */
	public $show_in_menus = true;
	

	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	abstract public function get_description();


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	abstract public function get_name();


	/**
	 * Implement this if your validation rule requires some options: this should
	 * return some form elements that will dynamically be shown on the page if the 
	 * option is selected.  Do not include the entire form, just the inputs you need.
	 */
	public function get_options($current_value) { }
		
	/**
	 * Run the rule: check the user input.
	 *
	 * @param string 	$input
	 * @return string
	 */
	abstract public function validate($input);	
	
}
/*EOF*/