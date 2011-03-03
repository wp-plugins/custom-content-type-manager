<?php
/*------------------------------------------------------------------------------

------------------------------------------------------------------------------*/

$def['label']['name']			= 'label';
$def['label']['label']			= __('Label', CCTM_TXTDOMAIN);
$def['label']['value']			= '';
$def['label']['extra']			= '';			
$def['label']['description']	= '';
$def['label']['type']			= 'text';
$def['label']['sort_param']		= 1;

$def['name']['name']			= 'name';
$def['name']['label']			= __('Name', CCTM_TXTDOMAIN);
$def['name']['value']			= '';
$def['name']['extra']			= '';			
$def['name']['description']		= __('The name identifies the option_name in the wp_postmeta database table. The name should contain only letters, numbers, and underscores. You will use this name in your template functions to identify this custom field.', CCTM_TXTDOMAIN);
$def['name']['type']			= 'text';
$def['name']['sort_param']		= 2;

$def['description']['name']			= 'description';
$def['description']['label']		= __('Description',CCTM_TXTDOMAIN);
$def['description']['value']		= '';
$def['description']['extra']		= 'rows="5" cols="60"';
$def['description']['description']	= '';
$def['description']['type']			= 'textarea';
$def['description']['sort_param']	= 3;

$def['type']['name']		= 'type';
$def['type']['label']		= __('Input Type', CCTM_TXTDOMAIN);
$def['type']['value']		= 'text';
$def['type']['extra']		= '';
$def['type']['description']	= '';
$def['type']['type']		= 'dropdown';
$def['type']['options']		= array('checkbox','dropdown','image','media','relation','text','textarea','wysiwyg');
$def['type']['sort_param']	= 4;

$def['default_value']['name']			= 'default_value';
$def['default_value']['label']			= __('Default Value', CCTM_TXTDOMAIN);
$def['default_value']['value']			= '';
$def['default_value']['extra']			= '';
$def['default_value']['description']		= __('The default value will appear in form fields when a post is first created. For checkboxes, use a default value of "1" if you want it to be checked by default.', CCTM_TXTDOMAIN);
$def['default_value']['type']			= 'text';
$def['default_value']['sort_param']		= 5;


$def['sort_param']['name']			= 'sort_param';
$def['sort_param']['label']			= __('Sort Order',CCTM_TXTDOMAIN);
$def['sort_param']['value']			= '';
$def['sort_param']['extra']			= ' size="2" maxlength="4"';
$def['sort_param']['description']	= __('This controls where this field will appear on the page. Fields with smaller numbers will appear higher on the page.',CCTM_TXTDOMAIN);
$def['sort_param']['type']			= 'hidden';
$def['sort_param']['sort_param']	= 6;

/*EOF*/