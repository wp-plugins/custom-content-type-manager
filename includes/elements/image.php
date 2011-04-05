<?php
/**
* CCTM_image
*
* Implements an HTML text input.
*
*/
class CCTM_image extends FormElement
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
		global $post;
		
		$media_html = '';

		// It has a value
		if ( !empty($def['value']) )
		{
			$def['preview_html'] = wp_get_attachment_image( $def['value'], 'thumbnail', true );
			$attachment_obj = get_post($def['value']);
			//$def['preview_html'] .= '<span class="formgenerator_label">'.$attachment_obj->post_title.'</span><br />';
			$def['preview_html'] .= '<span class="formgenerator_label">'.$attachment_obj->post_title.' <span class="formgenerator_id_label">('.$def['value'].')</span></span><br />';
			
		}
		// It's not set yet
		else
		{
			$def['preview_html'] = '';
		}
		
		$def['controller_url'] = CCTM_URL.'/post-selector.php?post_type=attachment&b=1&post_mime_type=';
		$def['click_label'] = __('Choose Image');
		$tpl = '
			<span class="formgenerator_label formgenerator_media_label" id="formgenerator_label_[+name+]">[+label+]</span>
			<input type="hidden" id="[+id+]" name="[+name+]" value="[+value+]" /><br />
			<div id="[+id+]_media">[+preview_html+]</div>
			<br class="clear" />
			<a href="[+controller_url+]&fieldname=[+id+]" name="[+click_label+]" class="thickbox button">[+click_label+]</a>
			<br class="clear" /><br />';
		return FormGenerator::parse($tpl, $def);
		
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
			
			
	 */
	public function get_edit_field_form($def) {
		$out = '

			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_0">
			 	<label for="label" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_label">'.__('Label', CCTM_TXTDOMAIN).'</label>
			 	<input type="text" name="label" class="'.$this->get_field_class('label','text').'" id="label" value="'.$def['label'].'"/>
			 	' . $this->get_description('label') . '
			 </div>
		
			 <div class="formgenerator_element_wrapper" id="custom_field_wrapper_1">
				 <label for="name" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_name">'
					. __('Name', CCTM_TXTDOMAIN) .
			 	'</label>
				 <input type="text" name="name" class="'.$this->get_field_class('name','text').'" id="name" value="'.$def['name'].'"/>'
				 . $this->get_description('name') . '
			 </div>';
			
			// Initialize / defaults
			$preview_html = '';
			$click_label = __('Choose Image');
			$label = __('Default Value', CCTM_TXTDOMAIN);
			$controller_url = CCTM_URL.'/post-selector.php?post_type=attachment&b=1&post_mime_type=';
			
			// Handle the display of the Default Image thumbnail
			if ( !empty($def['default_value']) )
			{
				$preview_html = wp_get_attachment_image( $def['default_value'], 'thumbnail', true );
				$attachment_obj = get_post($def['default_value']);
				//$def['preview_html'] .= '<span class="formgenerator_label">'.$attachment_obj->post_title.'</span><br />';
				// Wrap it
				$preview_html .= '<span class="formgenerator_label">'.$attachment_obj->post_title.' <span class="formgenerator_id_label">('.$def['default_value'].')</span></span><br />';
				
			}
			
			$out .= '
				<div class="formgenerator_element_wrapper" id="custom_field_wrapper_2">
					<span class="formgenerator_label formgenerator_media_label" id="formgenerator_label_default_value">'.$label.' <a href="'.$controller_url.'&fieldname=default_value" name="default_value" class="thickbox button">'.$click_label.'</a></span> 
					<input type="hidden" id="default_value" name="default_value" value="'.$def['default_value'].'" /><br />
					<div id="default_value_media">'.$preview_html.'</div>
					
					<br />
				</div>';

			
			 $out .= '<div class="formgenerator_element_wrapper" id="custom_field_wrapper_4">
			 	<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">'.__('Description', CCTM_TXTDOMAIN) .'</label>
			 	<textarea name="description" class="'.$this->get_field_class('description','textarea').'" id="description" rows="5" cols="60">'.$def['description'].'</textarea>
			 	' . $this->get_description('description') .'
			 </div>
			 ';
			 
			 return $out;
	}

}


/*EOF*/