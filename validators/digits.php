<?php
class CCTM_numeric extends CCTM_Validator {

	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Requires that the input consists only of digits, e.g. 000123', CCTM_TXTDOMAIN);
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name(
		return __('Digits', CCTM_TXTDOMAIN);
	);
		
	/**
	 * Run the rule: check the user input.
	 *
	 * @param string 	$input
	 * @return string
	 */
	public function validate($input) {
		
	}	

}
/*EOF*/