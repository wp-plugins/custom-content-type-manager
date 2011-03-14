<?php
/**
* CCTM_checkbox
*
* Implements an HTML text input.
*
*/
class CCTM_checkbox extends FormElement
{
	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		# $this->props['type_label'] = __('Text', CCTM_TXTDOMAIN );
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param mixed $def associative array containing the full definition for this type of element.
	 * @param string HTML to be used in the WP manager for an instance of this type of element.
	 */
	public function get_create_post_form($def) {
		# print_r($def); exit;
		$this->props = $def;
		if ( $this->props['checked_by_default'])
		{
			$this->props['value'] = $def['value_when_checked'];
		}
		return $this->get_edit_post_form($def); // pass on to 
	}


	//------------------------------------------------------------------------------
	/**
	 * <div class="formgenerator_element_wrapper" id="custom_field_wrapper_address1">
	 * <label for="custom_content_address1" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_custom_content_address1">Address</label>
	 * <input type="text" name="custom_content_address1" class="formgenerator_text" id="custom_content_address1" value="3835 Cross Creek Road"/>
	 * </div>
	 *
	 * @param unknown $def
	 * @return unknown
	 */
	public function get_edit_post_form($def) {
		#print_r($def); exit;
		$is_checked = '';
		if ($def['value'] == $def['value_when_checked']) {
			$is_checked = 'checked="checked"';
		}
		$this->props = $def; # ???
#		print_r($this->props); exit;
		$output = sprintf(' 
			<input type="checkbox" name="%s" class="%s" id="%s" value="%s" %s/>
			'
			, self::post_name_prefix . $this->name
			, 'xxxxx' //$this->get_css_class()
			, $this->name
			, $def['value_when_checked']
			, $is_checked
		);
		$output .= $this->label; #$this->wrap_label();
		return $this->wrap_outer($output);
	}


	//------------------------------------------------------------------------------
	/**
	 * This should returm a form element(s) that handles all the controls required to define this
	 * type of field.  The default properties correspond to this class's public variables:
	 * name, id, label, type, default_value, value. Whatever inputs are defined here (as keys in the
	 * $_POST array) will be stored alongside the custom-field data for the parent post-type.
	 * THAT data (along with the current value of the field) is what's passed to the get_manager_form() function.
	 *
	 * @param unknown $default_vals
	 * @return unknown
	 */
	public function get_create_settings_form($def) {
		return $this->get_edit_settings_form($def);
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param unknown $current_values
			<style>
			input.cctm_error { 
				background: #fed; border: 1px solid red;
			}
			</style>
	 */
	public function get_edit_settings_form($def) {
		$is_checked = '';
		if ( $def['checked_by_default'] ) {
			$is_checked = 'checked="checked"';
		}
		return '
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_0">
			 	<label for="label" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_label">'.__('Label', CCTM_TXTDOMAIN).'</label>
			 	<input type="text" name="label" class="'.$this->get_css_class('label','text').'" id="label" value="'.$def['label'].'"/>
			 	' . $this->get_description('label') . '
			 </div>
		
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_1">
				 <label for="name" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_name">'
					. __('Name', CCTM_TXTDOMAIN) .
			 	'</label>
				 <input type="text" name="name" class="'.$this->get_css_class('name','text').'" id="name" value="'.$def['name'].'"/>'
				 . $this->get_description('name') . '
			 </div>

			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_2">
			 	<label for="value_when_checked" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_value_when_checked">'.__('Value when checked', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="value_when_checked" class="'.$this->get_css_class('value_when_checked','text').'" id="value_when_checked" value="'.htmlentities(stripslashes($def['value_when_checked'])).'"/>
			 	' . $this->get_description('value_when_checked') .'
			 </div>
			 
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_3">
			 	<label for="value_when_unchecked" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_value_when_unchecked">'.__('Value when unchecked', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="value_when_unchecked" class="'.$this->get_css_class('value_when_unchecked','text').'" id="value_when_unchecked" value="'.htmlentities(stripslashes($def['value_when_unchecked'])).'"/>
			 	' . $this->get_description('value_when_unchecked') .'
			 </div>

			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_4">
		 		<input type="checkbox" name="checked_by_default" class="'.$this->get_css_class('checked_by_default','checkbox').'" id="checked_by_default" value="1" '. $is_checked.'/>
			 	<label for="checked_by_default" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_checked_by_default">'.__('Checked by default', CCTM_TXTDOMAIN) .'</label>
			 	' . $this->get_description('checked_by_default') .'
			 </div>
			 
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_5">
			 	<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">'.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="'.$this->get_css_class('description','textarea').'" id="description" rows="5" cols="60">'.$def['description'].'</textarea>
			 	' . $this->get_description('description') .'
			 </div>
			 
			 
			 ';
	}

}


/*EOF*/