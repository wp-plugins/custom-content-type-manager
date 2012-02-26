<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('administrator')) exit('Admins only.');
/*------------------------------------------------------------------------------
Lists all defined post types:
Post-types are separated into 3 sections:
	1. Built-in post-types whose custom fields may be managed: posts, pages
	2. Post-Types for which the CCTM can have full control over (this includes
		both active post-types and post-types which have full definitions for)
	3. "Foreign" post-types registered by some other plugin whose custom fields
		the CCTM may standardize upon request.
------------------------------------------------------------------------------*/
$data 				= array();
$data['page_title']	= __('List Content Types', CCTM_TXTDOMAIN);
$data['menu'] 		= sprintf('<a href="'.get_admin_url(false,'admin.php').'?page=cctm&a=create_post_type" class="button">%s</a>', __('Create Content Type', CCTM_TXTDOMAIN) );
$data['msg']		= CCTM::get_flash();

$customized_post_types =  array();
$displayable_types = array();

// this has the side-effect of sorting the post-types
if ( isset(CCTM::$data['post_type_defs']) && !empty(CCTM::$data['post_type_defs']) ) {
	$customized_post_types =  array_keys(CCTM::$data['post_type_defs']);
}
$displayable_types = array_merge(CCTM::$built_in_post_types , $customized_post_types);
$displayable_types = array_unique($displayable_types);

$data['row_data'] = '';

foreach ( $displayable_types as $post_type ) {
	// Skip the foreigners till later
	// these are the ones we KNOW are foreign:
	if ( isset(CCTM::$data['post_type_defs'][$post_type]['is_foreign']) && !empty(CCTM::$data['post_type_defs'][$post_type]['is_foreign']) ) {
		continue;
	}
	// these are the ones we don't know are foreign because hey, they're foreign
	if (!isset(CCTM::$data['post_type_defs'][$post_type])) {
		continue;
	}
	
	$hash = array(); // populated for the tpl
	$hash['post_type'] = $post_type;

	// Get our default links
	$deactivate    = sprintf(
			'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Deactivate this content type', CCTM_TXTDOMAIN)
			, __('Deactivate', CCTM_TXTDOMAIN)
		);
	$edit_link     = sprintf(
			'<a href="?page=cctm&a=edit_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Edit this content type', CCTM_TXTDOMAIN )
			, __('Edit', CCTM_TXTDOMAIN)
		);

	$duplicate_link     = sprintf(
			'<a href="?page=cctm&a=duplicate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Duplicate this content type', CCTM_TXTDOMAIN )
			, __('Duplicate', CCTM_TXTDOMAIN)
		);

	$manage_custom_fields  = sprintf(
			'<a href="?page=cctm&a=list_pt_associations&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
			, __('Manage Custom Fields', CCTM_TXTDOMAIN)
		);
	$view_templates   = sprintf('<a href="?page=cctm&a=template_single&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('View Sample Templates for this content type', CCTM_TXTDOMAIN )
			, __('View Sample Templates', CCTM_TXTDOMAIN)
		);
	

	// Built-in post types use a canned description and override a few other behaviors
	if ( in_array($post_type, CCTM::$built_in_post_types) ) {
		$deactivate    = sprintf(
			'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
			, $post_type
			, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
			, __('Release Custom Fields', CCTM_TXTDOMAIN)
		);
		$hash['description']  = __('Built-in post type.', CCTM_TXTDOMAIN);
		$hash['edit_manage_view_links'] = '<img src="'. CCTM_URL .'/images/wp.png" height="16" width="16" alt="wp"/> ' . $manage_custom_fields . ' | ' . $view_templates;
		// Not active
		if (!isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) || !CCTM::$data['post_type_defs'][$post_type]['is_active']) {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Standardize Custom Fields for this content type', CCTM_TXTDOMAIN)
					, __('Standardize Custom Fields', CCTM_TXTDOMAIN)
				) . '</span>';
		}
		// Active
		else {
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = sprintf(
				'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
				, $post_type
				, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
				, __('Release Custom Fields', CCTM_TXTDOMAIN)
			);
		}
	}
	// Whereas users define the description for custom post types
	else {
		$hash['description']  = CCTM::get_value(CCTM::$data['post_type_defs'][$post_type], 'description');
		$hash['edit_manage_view_links'] = $edit_link . ' | '. $manage_custom_fields . ' | ' . $view_templates . ' | ' . $duplicate_link;

		if ( isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) && !empty(CCTM::$data['post_type_defs'][$post_type]['is_active']) ) {
	
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = '<span class="deactivate">'.$deactivate.'</span>';
			$is_active = true;
		}
		else {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Activate this content type', CCTM_TXTDOMAIN)
					, __('Activate', CCTM_TXTDOMAIN)
				) . ' | </span>'
				. '<span class="delete">'. sprintf(
				'<a href="?page=cctm&a=delete_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Delete this content type', CCTM_TXTDOMAIN)
					, __('Delete', CCTM_TXTDOMAIN)
				).'</span>';
			$is_active = false;
		}
	}
	

	// Images
	$hash['icon'] = '';
	switch ($post_type) {
	case 'post':
		$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/post.png' . '" width="15" height="15"/>';
		break;
	case 'page':
		$hash['icon'] = '<img src="'. CCTM_URL . '/images/icons/page.png' . '" width="14" height="16"/>';
		break;
	default:
		if ( !empty(CCTM::$data['post_type_defs'][$post_type]['menu_icon']) && !CCTM::$data['post_type_defs'][$post_type]['use_default_menu_icon'] ) {
			$hash['icon'] = '<img src="'. CCTM::$data['post_type_defs'][$post_type]['menu_icon'] . '" />';
		}
		break;
	}
	$data['row_data'] .= CCTM::load_view('tr_post_type.php', $hash);
}

// Foreign post types... loop over all registered post-types, skip ones that don't have "is_foreign"
if (CCTM::get_setting('show_foreign_post_types')) {
	$registered_post_types = get_post_types();
	foreach($registered_post_types as $post_type) {
		// Only foreign post-types in this section
		if (in_array($post_type, CCTM::$reserved_post_types)) {
			continue;
		}
		if ( isset(CCTM::$data['post_type_defs'][$post_type]['post_type'])) {
			continue; // skip normally CCTM post-types
		}

		
		// Get our links
		$deactivate    = sprintf(
				'<a href="?page=cctm&a=deactivate_post_type&pt=%s" title="%s">%s</a>'
				, $post_type
				, __('Stop standardizing custom fields this content type', CCTM_TXTDOMAIN)
				, __('Release Custom Fields', CCTM_TXTDOMAIN)
			);
		// note the &f=1 to denote a foreign post-type
		$manage_custom_fields  = sprintf(
				'<a href="?page=cctm&a=list_pt_associations&pt=%s&f=1" title="%s">%s</a>'
				, $post_type
				, __('Manage Custom Fields for this content type', CCTM_TXTDOMAIN)
				, __('Manage Custom Fields', CCTM_TXTDOMAIN)
			);
		$view_templates   = sprintf('<a href="?page=cctm&a=template_single&pt=%s" title="%s">%s</a>'
				, $post_type
				, __('View Sample Templates for this content type', CCTM_TXTDOMAIN )
				, __('View Sample Templates', CCTM_TXTDOMAIN)
			);
			
		$hash['edit_manage_view_links'] = '<img src="'. CCTM_URL .'/images/spy.png" height="16" width="16" alt="spy"/> '. $manage_custom_fields . ' | ' . $view_templates;
		$hash['post_type'] = $post_type;
		
		if ( isset(CCTM::$data['post_type_defs'][$post_type]['is_active']) && !empty(CCTM::$data['post_type_defs'][$post_type]['is_active']) ) {
			$hash['class'] = 'active';
			$hash['activate_deactivate_delete_links'] = '<span class="deactivate">'.$deactivate.'</span>';
			$is_active = true;
		}
		else {
			$hash['class'] = 'inactive';
			$hash['activate_deactivate_delete_links'] = '<span class="activate">'
				. sprintf(
					'<a href="?page=cctm&a=activate_post_type&pt=%s" title="%s">%s</a>'
					, $post_type
					, __('Standardize Custom Fields for this content type', CCTM_TXTDOMAIN)
					, __('Standardize Custom Fields', CCTM_TXTDOMAIN)
				) . '</span>';
			$is_active = false;
		}
		
//		$hash['class'] = 'inactive';
//		$hash['activate_deactivate_delete_links'] = '';
		$hash['description'] = __('This post type has been registered by some other plugin.');
		$hash['icon'] = '<img src="'. CCTM_URL . '/images/forbidden.png' . '" width="16" height="16"/>';
		// $data['row_data'] .= CCTM::parse($tpl, $hash);
		$data['row_data'] .= CCTM::load_view('tr_post_type.php', $hash);
	}
}

$data['content'] = CCTM::load_view('list_post_types.php', $data);
print CCTM::load_view('templates/default.php', $data);
