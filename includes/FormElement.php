<?php
/**
 * TODO: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=42
 * We need a class that can be extended on a per custom-field-type basis.
 * Think about this... each form element gets its own array in the custom_fields array...
 * but each form element might have completely disparate attributes.  The whole FormGenerator
 * approach may not be the best way to do this.
 * How should this be extended by other users?  I should register an action that savvy users
 * can tap into.
 *
 * Example def
 * Array
 * (
 * 		[label] => Class
 *		[name] => class
 * 		[description] => Used to style the display.
 * 		[type] => dropdown
 * 		[options] => Array
 * 		(
 * 			[0] => regular
 * 			[1] => long
 * 		)
 * 		[default_value] => regular
 * 		[sort_param] => 8
 * )
 *
 * There are layers here... first there is a child class of FormElement that
 
 * @package
 */


abstract class FormElement {

	// Set default properties here
	public $props = array(
		'label' => '',
		'name' => '', // uniquely identifies this custom-field; corresponds to wp_postmeta.meta_key for each post
		'description' => '',
		'type' => '', // e.g. checkbox, dropbox, text
		'options' => array(),
		'default_value' => '',
		
		// Should these be used at all?
		'input_css_class'	=> '',
		'label_css_class'	=> '',
		'wrapper_css_class'	=> '',
		
		// 'sort_param' => '', // handled automatically
	);

	public $descriptions = array();
	
	public $css_class = '';

	// Stores any errors with fields.  The format here is array( 'field_name' => array('Error msg1','Error msg2') )
	public $errors = array();

	// Vars from $props that you don't want any dev in a childclass to change
	// during runtime.
	private $protected_instance_vars = array('sort_param', 'name');

	// Added to each key in the $_POST array, e.g. $_POST['cctm_firstname']
	const post_name_prefix = 'cctm_';
	const input_type_class_prefix = 'cctm_';
	const input_id_prefix = 'cctm_';

	
	/* Always include this CSS class in generated input labels, e.g. 
	<label for="xyz" class="formgenerator_label formgenerator_text_label" id="xyz_label">
		Address</label>
	*/
	const label_css_class_prefix = 'formgenerator_label '; // include a space
	const label_css_id_prefix = 'formgenerator_label_';
	const css_class_description = 'formgenerator_description';
	const error_css = 'cctm_error'; // used for validation errors
	//------------------------------------------------------------------------------
	/**
	 * When we instantiate an instance of a particular FormElement, we pass it 
	 * a definition hash.
	 * @param	array	$def: Associative array used to populate the $props variable. 
	 */
	public function __construct() {
		$this->descriptions['extra'] = __('Any extra attributes for this text field, e.g. <code>size="10"</code>', CCTM_TXTDOMAIN);
		$this->descriptions['default_value'] = __('The default value is presented to users when a new post is created.', CCTM_TXTDOMAIN);
		$this->descriptions['description'] = __('The description is visible when you view all custom fields or when you use the <code>get_custom_field_meta()</code> function.');
		$this->descriptions['label'] = __('The label is displayed when users create or edit posts that use this custom field.', CCTM_TXTDOMAIN);
		$this->descriptions['name'] = __('The name identifies the meta_key in the wp_postmeta database table. The name should contain only letters, numbers, and underscores. You will use this name in your template functions to identify this custom field.', CCTM_TXTDOMAIN);
		$this->descriptions['value_when_checked'] = __('What value should be stored in the database when this checkbox is checked?', CCTM_TXTDOMAIN);
		$this->descriptions['value_when_unchecked'] =  __('What value should be stored in the database when this checkbox is unchecked? Normally, checkboxes do not store a value when not checked, but you have the option to store a value when the checkbox is not checked. This makes it behave more like a dropdown or radio button.', CCTM_TXTDOMAIN);

/*
		
		// Validation: make sure the declaration is legit
		if ( !isset($def['name']) ) {
			CCTM::$errors[] = __('FormElement definition must contain a "name" attribute.', CCTM_TXTDOMAIN);
		}
		if ( !empty(CCTM::$errors) ) {
			return;
		}
		
		
		
		// The name of this type of FormElement should come directly from the class name
		$this->props['type'] = str_replace(
			CCTM::FieldElement_classname_prefix,
			'',
			__CLASS__ );
			
		// For parsing function
		$this->props['CCTM_URL'] = CCTM_URL;
		$this->props['CCTM_PATH'] = CCTM_PATH;
		
		// --- CSS Defaults ---
		// css class used on the HTML form element that is accepting input
		if ( !isset($def['input_css_class']) ) {
			$this->props['input_css_class'] = self::input_type_class_prefix . $this->props['type'];
		}
		// css class used on the label for the form element
		// formgenerator_label formgenerator_text_label
		if ( !isset($def['label_css_class']) ) {
			$this->props['label_css_class'] = self::input_type_class_prefix . $this->props['type'];
		}
		// css class used on the div that wraps all generated content for this FormElement instance
		if ( !isset($def['wrapper_css_class']) ) {
			$this->props['wrapper_css_class'] = self::input_type_class_prefix . $this->props['type'];
		}		
*/
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param unknown $k
	 * @return unknown
	 */
	public function __get($k) {
		if ( isset($this->props[$k]) ) {
			switch ($k) {
				// Ensures a unique key in $_POST
/*
			case 'name':
				return self::post_name_prefix . $this->props[$k];
				break;
*/
			default:
				return $this->props[$k];
			}

		}
		else {
			return ''; // Error?
		}
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @param unknown $k
	 * @param unknown $v
	 */
	public function __set($k, $v) {
		if ( !in_array($k, $this->protected_instance_vars) ) {
			$this->props[$k] = $v;
		}
	}


	//! Abstract Functions
	//------------------------------------------------------------------------------
	/**
	 * get_manager_form
	 * This function needs to return the form element(s) for an instance of this custom field
	 * when a post or page is being edited.
	 *
	 */
	abstract public function get_create_post_form($def);

	/**
	 *
	 */
	abstract public function get_edit_post_form($def);

	//------------------------------------------------------------------------------
	/**
	 * This should returm a form element(s) that handles all the controls required to define this
	 * type of field.  The default properties correspond to this class's public variables:
	 * name, id, label, type, default_value, value. Whatever inputs are defined here (as keys in the
	 * $_POST array) will be stored alongside the custom-field data for the parent post-type.
	 * THAT data (along with the current value of the field) is what's passed to the get_manager_form() function.
	 *
	 * @param mixed   $current_values should be an associative array.
	 */
	abstract public function get_create_field_form($current_values);

	/**
	 *
	 */
	abstract public function get_edit_field_form($current_values);




	//! Protected Functions
	//------------------------------------------------------------------------------
	/**
	 * Generate a CSS class for this type of field, typically keyed off the actual HTML
	 * input type, e.g. text, textarea, submit, etc.
	 
	 cctm_text
	 cctm_my_text_field
	 cctm_error
	 
	 *
	 * @param string  $input_type: the type of HTML field (if applicable)
	 * @return string a string representing a CSS class.
	 *
	 */
	protected function get_css_class( $id, $input_type='text' ) {
		// formgenerator_text
		// TODO!!! 
		$css_arr = array();
		# in_array(mixed needle, array haystack [, bool strict])
		$errors = array_keys($this->errors);
		if ( in_array( $id, $errors ) ) {
			$css_arr[] = self::error_css;
		}

		$css_arr[] = self::input_type_class_prefix . $id;
		$css_arr[] = self::input_type_class_prefix . $input_type;
		return implode(' ', $css_arr);
	}


	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @return unknown
	 */
	protected function get_css_id() {
		return $this->input_id_prefix . $this->props['name'];
	}

	//------------------------------------------------------------------------------
	/**
	* Behavior is determined by the function that calls this: see 
	* http://bytes.com/topic/php/answers/221-function-global-var-return-name-calling-function
	* @param	string	$name is the name of a field, e.g. 'my_name' in <input type="text" name="my_name" />
	* @return	string	A name safe for the context in which it was called.
	*/
	protected function get_name($name) {
		$backtrace = debug_backtrace();
		$calling_function = $backtrace[1]['function'];
		
		switch ($calling_function) {
			case 'get_create_post_form':
			case 'get_edit_post_form':
				
				break;
			case 'get_edit_field_form':
			case 'get_create_field_form':
			default: 

		}
	}

	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @return an associative array containing default values for this type of field.
	 */
	public function get_defaults() {
		return $this->props;
	}
	

	//------------------------------------------------------------------------------
	/**
	 *
	 *
	 * @return unknown
	 */
	protected function wrap_description() {
		// TODO: localize this?
		return sprintf('<span class="%s">%s</span>'
			, self::css_class_description
			, $this->description); 

	}



	/**
	 * This function returns an HTML label that wraps the label attribute for the instance of
	 * of this element.
	 * I added some carriage returns here for readability in the generated HTML
	 *
<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">Description</label>	 
	 
	 * @return string	HTML representing the label for this field.
	 */
	protected function wrap_label() {
		$wrapper = '
		<label for="%s" class="%s" id="%s">
			%s
		</label>
		';
		
		return sprintf($wrapper
			, $this->props['name']
			, self::label_css_class_prefix . $this->props['name']
			, self::label_css_id_prefix . $this->props['name']
			, $this->props['label']
		);  # TODO: __('label', ????) localized
	}


	//------------------------------------------------------------------------------
	/**
	 * This wraps the $input in a div with appropriate styling.
	 *
	 * @param string  $input is the contents of the field, needing
	 * @return sting	HTML representing the full HTML content for this field instance.
	 */
	protected function wrap_outer($input) {
		$wrapper = '
		<div class="formgenerator_element_wrapper" id="custom_field_%s">
		%s
		</div>';
		return sprintf($wrapper, $this->props['name'], $input);
	}



	//! Public Functions
	//------------------------------------------------------------------------------
	/**
	* Formats errors
	* @return string HTML describing any errors tracked in the class $errors variable
	*/
	public function format_errors() {
		$error_str = '';
		foreach ( $this->errors as $tmp => $errors ) {
			foreach ( $errors as $e ) {
				$error_str .= '<li>'.$e.'</li>
				';	
			}				
		}

		return sprintf('<div class="error">
			<h3>%1$s</h3>
			<ul style="margin-left:30px">
				%2$s
			</ul>
			</div>'
			, __('There were errors in your custom field definition.', CCTM_TXTDOMAIN)
			, $error_str
		);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * This static function acts as a per-fieldtype filter for the front-end for any given
	 * FormElement so that any type of custom field can customize its output to the
	 * front-end. It's called statically from the theme function: get_custom_field()
	 *
	 * Example of custom handling per field type:
	 * $img_atts = array();
	 * $img_html = get_custom_field('my_img_field', $img_atts);
	 * print $img_html; // prints <img src="/path/to/image.jpg" />
	 * print_r($img_atts); // prints Array('src'=>'/path/to/image.jpg', 'h'=>'100', 'w' => '50')
	 *
	 * question.  Override this function to provide special output filtering on a
	 *  field-type basis.
	 *
	 * @returm string The output should be string.  If you need more complex outputs,
	 * utilize the $extra input and set its values directly. It's passed by reference,
	 *  so any edits to $extra will be visible to the caller.
	 *
	 * @param string  $value is whatever was stored in this field for the post in
	 * @param unknown $extra (reference)
	 * @return unknown
	 */
	public function get_field_value($value, &$extra) {
		return $value;
	}

	//------------------------------------------------------------------------------
	/**
	* A little clearing house for getting descriptions for various components
	*
	* @param	string	$item to identify which description you want.
	* @return	string	HTML localized description
	*/
	public function get_description($item) {
		$tpl = '<span class="formgenerator_description">%s</span>';
		$out = '';
		switch ($item) {
			case 'extra':
				$out = 
			 		 __('Any extra attributes for this text field, e.g. <code>size="10"</code>', CCTM_TXTDOMAIN);
			 	break;
			case 'default_option':
				$out = 
			 		 __('The default option will appear selected. Make sure it matches a defined option.', CCTM_TXTDOMAIN);
				break;
			case 'default_value':
				$out = 
			 		 __('The default value is presented to users when a new post is created.', CCTM_TXTDOMAIN);
			 	break;
			case 'description':
				$out = __('The description is visible when you view all custom fields or when you use the <code>get_custom_field_meta()</code> function.');
				break;
			case 'label':
				$out = __('The label is displayed when users create or edit posts that use this custom field.', CCTM_TXTDOMAIN);
				break;
			case 'name':
				$out = 
			 		 __('The name identifies the meta_key in the wp_postmeta database table. The name should contain only letters, numbers, and underscores. You will use this name in your template functions to identify this custom field.', CCTM_TXTDOMAIN);
			 	break;
			case 'value_when_checked':
				$out = 
			 		 __('What value should be stored in the database when this checkbox is checked?', CCTM_TXTDOMAIN);
			 	break;
			case 'value_when_unchecked':
				$out = 
			 		 __('What value should be stored in the database when this checkbox is unchecked? Normally, checkboxes do not store a value when not checked, but you have the option to store a value when the checkbox is not checked. This makes it behave more like a dropdown or radio button.', CCTM_TXTDOMAIN);
			 	break;

		 }
		 
		 return sprintf($tpl, $out);
	}
 
 	//------------------------------------------------------------------------------
 	//------------------------------------------------------------------------------
 	/**
 	* @param	string	$html string, with linebreaks, quotes, etc.
 	* @return	string	Filtered: linebreaks removed, quotes escaped.
 	*/
 	public static function make_js_safe($html) {
 		$html = preg_replace("/\n\r|\r\n|\r|\n/",'',$html);
 		$html = preg_replace( '/\s+/', ' ', $html );
 		$html = addslashes($html);
 		$html = trim($html);
 	}
 	
	//------------------------------------------------------------------------------
	/**
	 * Get the full image tag for this field-type's icon.  The icon should be 48x48.
	 * Default behavior is to look inside the images/custom-fields directory
	 *
	 * @return string full HTML <img> tag for this field-type's icon.
	 */
	public function get_icon() {

		$field_type = str_replace(
			CCTM::FormElement_classname_prefix,
			'',
			get_class($this) );
		$dir = CCTM::get_custom_icons_src_dir();
		$icon_src = $dir . $field_type .'.png';

		// Use the default image if necessary
		if (!@fclose(@fopen($icon_src, 'r'))) {
			$icon_src = CCTM_URL.'/images/custom-fields/default.png';
		}

		return sprintf('<img src="%s"/>', $icon_src);
	}

	//------------------------------------------------------------------------------
	/**
	 * This function allows for custom handling of submitted post/page data just before
	 * it is saved to the database. Data validation and filtering should happen here,
	 * although it's difficult to enforce any validation errors.
	 *
	 * Output should be whatever string value you want to save in the wp_postmeta table
	 * for the post in question. This function will be called after the post/page has
	 * been submitted: this can be loosely thought of as the "on save" event
	 *
	 * @param mixed   $data associative array, must have a key for 'name'
	 */
	public function save_post_filter($data) {
		return $data;
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Validate and sanitize any submitted data. 
	 * Used when editing the definition for this type of element.
	 * Default behavior here is require only a unique name and label.
	 * @param	array	$post = $_POST data
	 * @param	array	$vars = lots of data, most importantly $vars['data'] = all stored data
	 * @param	string	$post_type the string defining this post_type
	 * @return	array	filtered field_data that can be saved OR can be safely repopulated
	 *					into the field values.
	 */
	public function save_settings_filter($post, $vars, $post_type) {
	
		$data = $vars['data'];
		if ( empty($post['name']) ) {
			$this->errors['name'][] = __('Name is required.', CCTM_TXTDOMAIN);
		}
		else {
			// Are there any invalid characters? 1st char. must be a letter
			if ( preg_match('/^[^a-z]{1}[^a-z_0-9]*/i', $post['name'])) {
				$this->errors['name'][] = sprintf(
					__('%s contains invalid characters. The name may only contain letters, numbers, and underscores, and it must begin with a letter.', CCTM_TXTDOMAIN)
					, '<strong>'.$post['name'].'</strong>');
				$data['name'] = preg_replace('/[^a-z_0-9]/', '', $data['name']);
			}
			// Is the name too long?
			if ( strlen($post['name']) > 20 ) {
				$post['name'] = substr($post['name'], 0 , 20);
				$this->errors['name'][] = __('The name is too long. Names must not exceed 20 characters.', CCTM_TXTDOMAIN);
			}
			// Run into any reserved words?
			if ( in_array($post, CCTM::$reserved_field_names ) ) {
				$this->errors['name'][] = sprintf(
					__('%s is a reserved name.', CCTM_TXTDOMAIN)
					, '<strong>'.$post['name'].'</strong>');
				$data['name'] = '';	
			}
			
			// Is that name already in use? 
			// if $vars['field_name'] is empty, then we're creating a new field.
			if ( !empty($vars['field_name'])
				&& $vars['field_name'] != $post['name']
				&& is_array($data[$post_type]['custom_fields']) 
				&& in_array( $post['name'], array_keys($data[$post_type]['custom_fields']) ) ) {
				$this->errors['name'][] = sprintf( __('The name %s is already in use. Please choose another.', CCTM_TXTDOMAIN), '<em>'.$post['name'].'</em>');
				$post['name'] = '';
			}
		}
		
		if ( empty($post['label']) ) {
			$this->errors['label'][] = __('Label is required.', CCTM_TXTDOMAIN);
		}
		
		$post['type'] = str_replace(
			CCTM::FormElement_classname_prefix,
			'',
			get_class($this) );
			
		return $post; // filtered data
	}

}

/*EOF FormElement.php */