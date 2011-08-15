<?php
/*------------------------------------------------------------------------------
This is run only when this plugin is uninstalled. All cleanup code goes here.

WARNING: uninstalling a plugin fails when developing locally via MAMP.
I think it's a WordPress bug (version 3.0.1). Perhaps related to how WP
attempts (and fails) to connect to the local site.
------------------------------------------------------------------------------*/

if ( defined('WP_UNINSTALL_PLUGIN'))
{
	include_once('includes/constants.php');
	include_once('includes/CCTM.php');
	include_once('includes/FormElement.php');
	
	// If the custom fields modified anything, we need to give them this 
	// opportunity to clean it up.
	$available_custom_field_types = CCTM::get_available_custom_field_types();
	foreach ( $available_custom_field_types as $field_type ) {
		$element_file = CCTM_PATH.'/includes/elements/'.$field_type.'.php';
		if ( file_exists($element_file) )
		{
			include_once($element_file);
			if ( class_exists(CCTM::FormElement_classname_prefix.$field_type) )
			{
				$field_type_name = CCTM::FormElement_classname_prefix.$field_type;
				$FieldObj = new $field_type_name();
				$FieldObj->uninstall();
			}
		}
	}
	
	delete_option( CCTM::db_key );
	delete_option( CCTM::db_key_settings );
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
/*EOF*/