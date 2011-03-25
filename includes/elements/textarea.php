<?php
/**
* CCTM_textarea
*
* Implements an HTML text input.
*
*/
class CCTM_textarea extends FormElement
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
		$def = $this->get_defaults();
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
		$output = sprintf('
			%s 
			<input type="text" name="%s" class="%s" id="%s" value="%s"/>
			'
			, $this->wrap_label()
			, self::post_name_prefix . $this->name
			, $this->get_css_class()
			, $this->name
			, $def['default_value']
		);
		
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
	public function get_create_field_form($def) {
		return $this->get_edit_field_form($def);
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
	public function get_edit_field_form($def) {
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
			 	<label for="default_value" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_default_value">'.__('Default Value', CCTM_TXTDOMAIN) .'</label>
			 		<textarea name="default_value" class="'.$this->get_css_class('default_value','textarea').'" id="default_value" rows="5" cols="60">'
			 			.$def['default_value']
			 		.'</textarea>
			 	' . $this->get_description('default_value') .'
			 </div>

			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_3">
			 	<label for="extra" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_extra">'.__('Extra', CCTM_TXTDOMAIN) .'</label>
			 		<input type="text" name="extra" class="'.$this->get_css_class('extra','text').'" id="extra" value="'.htmlentities(stripslashes($def['extra'])).'"/>
			 	' . $this->get_description('extra') .'
			 </div>
			 
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_4">
			 	<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">'.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="'.$this->get_css_class('description','textarea').'" id="description" rows="5" cols="60">'.$def['description'].'</textarea>
			 	' . $this->get_description('description') .'
			 </div>
			 
			 
			 ';
	}

}


/*EOF*/