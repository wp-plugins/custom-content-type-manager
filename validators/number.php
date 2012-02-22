<?php
/**
 * Numeric validation can be configured to allow a range of numbers, 
 * @package CCTM_Validator
 *
 */
class CCTM_number extends CCTM_Validator {

	public $options = array(
		'min' => '',
		'max' => '',
		'allow_negative' => '',
		'allow_decimals' => '',
		'decimal_places' => '',
	);

	/**
	 * @return string	a description of what the validation rule is and does.
	 */
	public function get_description() {
		return __('Ensure that the input is a number of the type you have configured.', CCTM_TXTDOMAIN);		
	}


	/**
	 * @return string	the human-readable name of the validation rules.
	 */
	public function get_name() {
		return __('Number', CCTM_TXTDOMAIN);
	}


	/**
	 * Implement this if your validation rule requires some options: this should
	 * return some form elements that will dynamically be shown on the page via 
	 * an AJAX request if this validator is selected.  
	 * Do not include the entire form, just the inputs you need!
	 */
	public function get_options($current_value) { 
		$options = '<label class="cctm_label" for="'.$this->get_field_id('min').'">'.__('Minimum Value Allowed', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('min').'" id="'.$this->get_field_id('min').'">
			<label class="cctm_label" for="'.$this->get_field_id('max').'">'.__('Maximum Value Allowed', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('max').'" id="'.$this->get_field_id('max').'">
			<input type="checkbox" name="'.$this->get_field_name('allow_negative').'" id="validation_options_allow_negative" value="1" '.$this->is_checked('allow_negative').'> 
			<label class="cctm_checkbox_label" for="'.$this->get_field_id('allow_negative').'">'.__('Allow Negative Numbers', CCTM_TXTDOMAIN).'</label>
			<input type="checkbox" name="'.$this->get_field_name('allow_decimals').'" id="'.$this->get_field_id('allow_decimals').'" value="1" '.$this->is_checked('allow_decimals').'> 
			<label class="cctm_checkbox_label" for="'.$this->get_field_id('allow_decimals').'">'.__('Allow Decimals', CCTM_TXTDOMAIN).'</label>
			<label class="cctm_label" for="'.$this->get_field_id('decimal_places').'">'.__('Maximum Decimal Places', CCTM_TXTDOMAIN).'</label>
			<input type="text" name="'.$this->get_field_name('decimal_places').'" id="'.$this->get_field_id('decimal_places').'">
			';
			
			return $options;
	}
		
	/**
	 * Run the rule: check the user input. Return the (filtered) value that should
	 * be used to repopulate the form.
	 *
	 * @param string 	$input (as it is stored in the database)
	 * @return string
	 */
	public function validate($input) {
		if (!is_numeric($input)) {
			$this->error_msg = 'The %s field must be numeric.';
		}
		
		return $input;
	}
	
}
/*EOF*/