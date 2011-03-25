<?php
/**
* CCTM_dropdown
*
* Implements an HTML text input.
*
*/
class CCTM_dropdown extends FormElement
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
	
		//print_r($data); 
/*
		// Some error messaging.
		if ( !isset($data['options']) || !is_array($data['options']) )
		{
			return '<p><strong>Custom Content Error:</strong> No options supplied for '.$data['name'].'</p>';
		}
		
		$tpl = '
			<label for="[+name+]" class="formgenerator_label" id="formgenerator_label_[+name+]">[+label+]</label>
				<select name="[+name+]" class="formgenerator_dropdown formgenerator_dropdown_label" id="[+name+]"[+extra+]>
					[+options+]  
				</select>
				[+special+]';
		
		$option_str = '';
		foreach ( $data['options'] as $option )
		{
			if ( empty($option) )
			{
				$option_str .= '<option value="">Pick One</option>' . "\n";
			}
			else
			{
				$option = htmlspecialchars($option); // Filter the values
				$is_selected = '';
				if ( isset($data['value']) && $data['value'] == $option )
				{
					$is_selected = 'selected="selected"';
				}
				$option_str .= '<option value="'.$option.'" '.$is_selected.'>'.$option.'</option>' . "\n";
			}
		}
		
		if ( isset($data['special']) && is_array($data['special']) )
		{
			
			$data['special'] = self::_get_special($data);
		}
		
		$data['options'] = $option_str; // overwrite the array with the string.
		
		return self::parse($tpl, $data);
		
*/
		// Some error messaging.
		if ( !isset($def['options']) || !is_array($def['options']) ) {
			return sprintf('<p><strong>%$1s</strong> %$2s %$3s</p>'
				, __('Custom Content Error', CCTM_TXTDOMAIN)
				, __('No options supplied for the following custom field: ', CCTM_TXTDOMAIN)
				, $data['name']
			);
		}
		
		$output = '<label for="'.$def['name'].'" class="formgenerator_label" id="formgenerator_label_'.$def['name'].'">'.$def['label'].'</label>
				<select name="'.$def['name'].'" class="formgenerator_dropdown formgenerator_dropdown_label" id="'.$def['name'].'">
					[+options+]
				</select>';
		
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
	
		// Used when adding simple options
		$option_tpl = '<div id="dropdown_option_[+i+]">
					<input type="text" name="options[]" value="[+value+]"/> <span class="button" onclick="javascript:remove_html(\'dropdown_option_[+i+]\');">[+delete_label+]</span>
				</div>';

		$out = '

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
			 		<input type="text" name="default_value" class="'.$this->get_css_class('default_value','text').'" id="default_value" value="'
			 			.$def['default_value']
			 		.'" />
			 	' . $this->get_description('default_option') .'
			 </div>';
			
			// Start options
			// this html should match up with the js html in manager.js
			$option_html = '
				<div id="%s">
					<input type="text" name="options[]" value="%s"/> <span class="button" onclick="javascript:remove_html(\'%s\');">%s</span>
				</div>';
			$cnt_options = 0;
			if (isset($def['options'])) {
				$cnt_options = count($def['options']);
			}
			
#			$option_html_for_js = FormElement::make_js_safe( sprintf($option_html, 'cctm_dropdown_option', '', $option_css_id, __('Delete') ) );
			
			$out .= '
				<div class="formgenerator_element_wrapper" id="dropdown_options">
					<label for="options" class="formgenerator_label formgenerator_select_label" id="formgenerator_label_options">'.__('Options', CCTM_TXTDOMAIN) .' <span class="button" onclick="javascript:append_dropdown_option(\'dropdown_options\',\''.__('Delete').'\',\''.$cnt_options.'\');">'.__('Add Option',CCTM_TXTDOMAIN).'</span>
					</label>
					<br />';
			
			$i = 0;
			foreach ($def['options'] as $opt_val) {
				$option_css_id = 'cctm_dropdown_option'.$i;
				$out .= sprintf($option_html, $option_css_id, $opt_val, $option_css_id, __('Delete') );
/*
				$out .= '<div id="'.$option_css_id.'">
					<input type="text" name="options[]" value="'.$o.'"/> <span class="button" onclick="javascript:remove_html(\''.$option_css_id.'\');">'
					. __('Delete').'</span>
				</div>';
*/
				$i = $i + 1;
			}
				
			$out .= '</div>
			
			
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_4">
			 	<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">'.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="'.$this->get_css_class('description','textarea').'" id="description" rows="5" cols="60">'.$def['description'].'</textarea>
			 	' . $this->get_description('description') .'
			 </div>
			 ';
			 
			 return $out;
	}

}


/*EOF*/