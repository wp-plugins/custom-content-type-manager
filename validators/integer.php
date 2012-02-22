<?php
/**
 * @package CCTM_Validator
 *
 * Abstract class for validation rules.  Classes that implement 
 */
class CCTM_integer extends CCTM_Validator {

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
	public function get_description() {
		return __('Ensure that the input is a natural whole number, e.g. 123', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('Integer', CCTM_TXTDOMAIN);
	}


	/**
	 * Implement this if your validation rule requires some options: this should
	 * return some form elements that will dynamically be shown on the page via 
	 * an AJAX request if this validator is selected.  
	 * Do not include the entire form, just the inputs you need!
	 */
	public function get_options($current_value) { 
		$options = '<input type="checkbox"> <label for="">Allow Negative</label>';
	}
		
	/**
	 * Run the rule: check the user input. Return the (filtered) value that should
	 * be used to repopulate the form.
	 *
	 * @param string 	$input (as it is stored in the database)
	 * @return string
	 */
	public function validate($input) {
	
	}
	
}
/*EOF*/