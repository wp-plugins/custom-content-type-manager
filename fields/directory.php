<?php
/**
 * CCTM_dropdown
 *
 * Implements an HTML select element with options (single select).
 *
 * @package CCTM_FormElement
 */
class CCTM_directory extends CCTM_FormElement
{
	public $props = array(
		'label' => '',
		'name' => '',
		'description' => '',
		'class' => '',
		'extra' => '',
		'default_value' => '',
		'required' => '',
		'source_dir' => '',
		'glob' => '',
		'traverse_dirs' => 0,
		// 'type' => '', // auto-populated: the name of the class, minus the CCTM_ prefix.

	);


	//------------------------------------------------------------------------------
	/**
	 * This function provides a name for this type of field. This should return plain
	 * text (no HTML). The returned value should be localized using the __() function.
	 *
	 * @return string
	 */
	public function get_name() {
		return __('Directory', CCTM_TXTDOMAIN);
	}


	//------------------------------------------------------------------------------
	/**
	 * This function gives a description of this type of field so users will know
	 * whether or not they want to add this type of field to their custom content
	 * type. The returned value should be localized using the __() function.
	 *
	 * @return string text description
	 */
	public function get_description() {
		return __('List files matching a pattern contained in a given folder. Output is relative to the defined source directory.', CCTM_TXTDOMAIN);
	}


	//------------------------------------------------------------------------------
	/**
	 * This function should return the URL where users can read more information about
	 * the type of field that they want to add to their post_type. The string may
	 * be localized using __() if necessary (e.g. for language-specific pages)
	 *
	 * @return string  e.g. http://www.yoursite.com/some/page.html
	 */
	public function get_url() {
		return 'http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Directory';
	}


	//------------------------------------------------------------------------------
	/**
	 * Get an instance of this field (used when you are creating or editing a post
	 * that uses this type of custom field).
	 *
	 * @param string  $current_value of the field for the current post
	 * @return string
	 */
	public function get_edit_field_instance($current_value) {

		// Format for multi-select
		if ($this->is_repeatable) {
			$current_value = $this->get_value($current_value, 'to_array');
			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_user_multi.tpl'
					, 'fields/options/_user.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_user_multi.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_user_multi.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}
		// For regular dropdowns
		else {
			$current_value = $this->get_value($current_value, 'to_string');

			$optiontpl = CCTM::load_tpl(
				array('fields/options/'.$this->name.'.tpl'
					, 'fields/options/_user.tpl'
				)
			);
			$fieldtpl = CCTM::load_tpl(
				array('fields/elements/'.$this->name.'.tpl'
					, 'fields/elements/_user.tpl'
					, 'fields/elements/_default.tpl'
				)
			);
			$wrappertpl = CCTM::load_tpl(
				array('fields/wrappers/'.$this->name.'.tpl'
					, 'fields/wrappers/_user.tpl'
					, 'fields/wrappers/_default.tpl'
				)
			);
		}


		// Get the options.  This currently is not skinnable.
		$this->all_options = '';

		if (!isset($this->required) || !$this->required) {
			$hash['value'] = '';
			$hash['option'] = '';
			$this->all_options .= CCTM::parse($optiontpl, $hash); // '<option value="">'.__('Pick One').'</option>';
		}

		// Get the files
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename) {
		// echo "$filename\n";
			if (preg_match('/\.php$/i',$filename)) {
				$this->options[] = $filename;
			}   
		}

//		$this->options = array(); // TODO <---*****
		$opt_cnt = count($this->options);

		$i = 1;
		// Populate the options
		foreach ( $this->options as $o ) {
			//die(print_r($o, true));
			$hash = $this->get_props();

			// We hardcode this one because we always need to store the user ID as the value for normalization
			$hash['value'] = $o->data->ID;

			foreach ($o->data as $k => $v) {
				if (!isset($hash[$k])) {
					$hash[$k] = $v;
				}
			}

			$hash['is_checked'] = '';

			if ($this->is_repeatable) {
				if ( in_array(trim($hash['value']), $current_value )) {
					$hash['is_selected'] = 'selected="selected"';
				}
			}
			else {
				if ( trim($current_value) == trim($hash['value']) ) {
					$hash['is_selected'] = 'selected="selected"';
				}
			}

			$hash['i'] = $i;
			$hash['id'] = $this->name;

			$this->all_options .= CCTM::parse($optiontpl, $hash);
		}



		// Populate the values (i.e. properties) of this field
		$this->id      = $this->name;

		// wrap
		$this->content = CCTM::parse($fieldtpl, $this->get_props());
		return CCTM::parse($wrappertpl, $this->get_props());

	}


	//------------------------------------------------------------------------------
	/**
	 * Note that the HTML in $option_html should match the JavaScript version of
	 * the same HTML in js/dropdown.js (see the append_dropdown_option() function).
	 * I couldn't think of a clean way to do this, but the fundamental problem is
	 * that both PHP and JS need to draw the same HTML into this form:
	 * PHP draws it when an existing definition is *edited*, whereas JS draws it
	 * when you dynamically *create* new dropdown options.
	 *
	 * @param array   $def nested array of existing definition.
	 * @return string
	 */
	public function get_edit_field_definition($def) {

		// Standard
		$out = $this->format_standard_fields($def);
		
		// Options
		$is_checked = '';
		if (isset($def['traverse_dirs']) && $def['traverse_dirs']==1) {
			$is_checked = 'checked="checked"';
		}
		
		// Source Directory
		$out .= '
			<div class="postbox">
				<div class="handlediv" title="Click to toggle"><br /></div>
				<h3 class="hndle"><span>'. __('Options', CCTM_TXTDOMAIN).'</span></h3>
				<div class="inside">';
				
		$out .= '<div class="'.self::wrapper_css_class .'" id="source_dir_wrapper">
				 <label for="source_dir" class="cctm_label cctm_text_label" id="source_dir_label">'
			. __('Source Directory', CCTM_TXTDOMAIN) .
			'</label>
				 <input type="text" name="source_dir" class="cctm_text_short" id="source_dir" value="'.htmlspecialchars($def['source_dir']) .'"/><span class="cctm_description">'.
				 __('The source directory should be a full path to the directory without the trailing slash, e.g. <code>/home/my_user/dir</code>',CCTM_TXTDOMAIN).'</span>
			 	</div>';

		// Glob
		$out .= '<div class="'.self::wrapper_css_class .'" id="glob_wrapper">
				 <label for="glob" class="cctm_label cctm_text_label" id="glob_label">'
			. __('Pattern', CCTM_TXTDOMAIN) .
			'</label>
				 <input type="text" name="glob" class="cctm_text_short" id="glob" value="'.htmlspecialchars($def['glob']) .'"/> <span class="cctm_description">'
			. __('Enter the pattern used for matching. Use comas to separate possible matches, e.g. <code>.jpg,.jpeg</code>',CCTM_TXTDOMAIN) .'</span>
			 	</div>';
		// Traverse Directories?
		$out .= '<div class="'.self::wrapper_css_class .'" id="traverse_dirs_wrapper">
				 <label for="traverse_dirs" class="cctm_label cctm_checkbox_label" id="traverse_dirs_label">'
			. __('Traverse Directories?', CCTM_TXTDOMAIN) .
			'</label>
				 <br />
				 <input type="checkbox" name="traverse_dirs" class="cctm_checkbox" id="traverse_dirs" value="1" '. $is_checked.'/> <span>'.__('When checked, the contents of sub-directories will also be listed.',CCTM_TXTDOMAIN).'</span>
			 	</div>';
			 	
		$out .= '</div><!-- /inside -->
			</div><!-- /postbox -->';

		// Validations / Required
		$out .= $this->format_validators($def,false);
		
		// Output Filter
		$out .= $this->format_available_output_filters($def);

		return $out;
	}


	//------------------------------------------------------------------------------
	/**
	 * Validate and sanitize any submitted data. Used when editing the definition for
	 * this type of element. Default behavior here is to require only a unique name and
	 * label. Override this if customized validation is required.
	 *
	 *     into the field values.
	 *
	 * @param array   $posted_data = $_POST data
	 * @return array filtered field_data that can be saved OR can be safely repopulated
	 */
	public function save_definition_filter($posted_data) {
		$posted_data = parent::save_definition_filter($posted_data);
		if (empty($posted_data['alternate_input']) && empty($posted_data['options'])) {
			$this->errors['options'][] = __('At least one option or alternate input is required.', CCTM_TXTDOMAIN);
		}
		return $posted_data; // filtered data
	}

	//------------------------------------------------------------------------------
	/**
	 * Traverse a directory.  We need this in its own function so we can self-reference.
	 * 
	 * @param string starting directory (omit trailing slash)
	 * @param string glob
	 * @param boolean $traverse -- should we enter into sub-directories
	 */
	public function traverse_dir($dir,$glob='*',$traverse=false) {
		foreach(glob($dir.'/'.$glob,GLOB_BRACE) as $f)  {
			if (is_file($f)) {
				$this->options[] = $f;
			}
			elseif ($traverse) {
				$this->traverse_dir($f,$glob,$traverse);
			}
		}
	}
}


/*EOF*/