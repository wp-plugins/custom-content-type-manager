<?php
/*------------------------------------------------------------------------------
Form def included by CCTM.php
------------------------------------------------------------------------------*/
$def['post_type']['name'] 			= 'post_type';
$def['post_type']['label'] 			= __('Name', CCTM_TXTDOMAIN). ' *';
$def['post_type']['value'] 			= '';
$def['post_type']['extra'] 			= '';
$def['post_type']['description'] 	= __('Unique singular name to identify this post type in the database, e.g. "movie","book". This may show up in your URLs, e.g. ?movie=star-wars. This will also make a new theme file available, starting with prefix named "single-", e.g. <strong>single-movie.php</strong>. The name should be lowercase with only letters and underscores. This name cannot be changed!', CCTM_TXTDOMAIN);
$def['post_type']['type'] 			= 'text';
$def['post_type']['sort_param'] 	= 1;
	
$def['singular_label']['name']			= 'singular_label';
$def['singular_label']['label']			= __('Singular Label', CCTM_TXTDOMAIN);
$def['singular_label']['value']			= '';
$def['singular_label']['extra']			= '';
$def['singular_label']['description']	= __('Human readable single instance of this content type, e.g. "Post"', CCTM_TXTDOMAIN);
$def['singular_label']['type']			= 'text';
$def['singular_label']['sort_param']	= 2;

$def['label']['name']			= 'label';
$def['label']['label']			= __('Menu Label (Plural)', CCTM_TXTDOMAIN);
$def['label']['value']			= '';
$def['label']['extra']			= '';
$def['label']['description']	= __('Plural name used in the admin menu, e.g. "Posts"', CCTM_TXTDOMAIN);
$def['label']['type']			= 'text';
$def['label']['sort_param']		= 3;

$def['description']['name']			= 'description';
$def['description']['label']		= __('Description', CCTM_TXTDOMAIN);
$def['description']['value']		= '';
$def['description']['extra']		= '';
$def['description']['description']	= '';	
$def['description']['type']			= 'textarea';
$def['description']['sort_param']	= 4;

$def['show_ui']['name']			= 'show_ui';
$def['show_ui']['label']			= __('Show Admin User Interface', CCTM_TXTDOMAIN);
$def['show_ui']['value']			= '1';
$def['show_ui']['extra']			= '';
$def['show_ui']['description']	= __('Should this post type be visible on the back-end?', CCTM_TXTDOMAIN);
$def['show_ui']['type']			= 'checkbox';
$def['show_ui']['sort_param']	= 5;

$def['capability_type']['name']			= 'capability_type';
$def['capability_type']['label']		= __('Capability Type', CCTM_TXTDOMAIN);
$def['capability_type']['value']		= 'post';
$def['capability_type']['extra']		= '';
$def['capability_type']['description']	= __('The post type to use for checking read, edit, and delete capabilities. Default: "post"', CCTM_TXTDOMAIN);
$def['capability_type']['type']			= 'text';
$def['capability_type']['sort_param']	= 6;

$def['public']['name']			= 'public';
$def['public']['label']			= __('Public', CCTM_TXTDOMAIN);
$def['public']['value']			= '1';
$def['public']['extra']			= '';
$def['public']['description']	= __('Should these posts be visible on the front-end?', CCTM_TXTDOMAIN);
$def['public']['type']			= 'checkbox';
$def['public']['sort_param']	= 7;

$def['hierarchical']['name']		= 'hierarchical';
$def['hierarchical']['label']		= __('Hierarchical', CCTM_TXTDOMAIN);
$def['hierarchical']['value']		= '';
$def['hierarchical']['extra']		= '';
$def['hierarchical']['description']	= __('Allows parent to be specified (Page Attributes should be checked)', CCTM_TXTDOMAIN);
$def['hierarchical']['type']		= 'checkbox';
$def['hierarchical']['sort_param']	= 8;

$def['supports_title']['name']			= 'supports[]';
$def['supports_title']['id']			= 'supports_title';
$def['supports_title']['label']			= __('Title', CCTM_TXTDOMAIN);
$def['supports_title']['value']			= 'title';
$def['supports_title']['checked_value'] = 'title';
$def['supports_title']['extra']			= '';
$def['supports_title']['description']	= __('Post Title', CCTM_TXTDOMAIN);
$def['supports_title']['type']			= 'checkbox';
$def['supports_title']['sort_param']	= 20;

$def['supports_editor']['name']			= 'supports[]';
$def['supports_editor']['id']			= 'supports_editor';
$def['supports_editor']['label']		= __('Content', CCTM_TXTDOMAIN);
$def['supports_editor']['value']		= 'editor';
$def['supports_editor']['checked_value'] = 'editor';
$def['supports_editor']['extra']		= '';
$def['supports_editor']['description']	= __('Main content block.', CCTM_TXTDOMAIN);
$def['supports_editor']['type']			= 'checkbox';
$def['supports_editor']['sort_param']	= 21;

$def['supports_author']['name']			= 'supports[]';
$def['supports_author']['id']			= 'supports_author';
$def['supports_author']['label']		= __('Author', CCTM_TXTDOMAIN);
$def['supports_author']['value']		= '';
$def['supports_author']['checked_value'] = 'author';
$def['supports_author']['extra']		= '';
$def['supports_author']['description']	= __('Track the author.', CCTM_TXTDOMAIN);
$def['supports_author']['type']			= 'checkbox';
$def['supports_author']['sort_param']	= 22;

$def['supports_thumbnail']['name']		= 'supports[]';
$def['supports_thumbnail']['id'] 		= 'supports_thumbnail';
$def['supports_thumbnail']['label'] 	= __('Thumbnail', CCTM_TXTDOMAIN);
$def['supports_thumbnail']['value'] 	= '';
$def['supports_thumbnail']['checked_value' ] = 'thumbnail';
$def['supports_thumbnail']['extra'] 		= '';
$def['supports_thumbnail']['description'] 	= __("Featured image. The active theme must also support post-thumbnails. Replace 'sample' with the name of this post-type then include the following line in your theme's functions.php file: add_theme_support( 'post-thumbnails', array( 'sample' ) );", CCTM_TXTDOMAIN);
$def['supports_thumbnail']['type'] 			= 'checkbox';
$def['supports_thumbnail']['sort_param'] 	= 23;

$def['supports_excerpt']['name']			= 'supports[]';
$def['supports_excerpt']['id']				= 'supports_excerpt';
$def['supports_excerpt']['label']			= __('Excerpt', CCTM_TXTDOMAIN);
$def['supports_excerpt']['value']			= '';
$def['supports_excerpt']['checked_value'] = 'excerpt';
$def['supports_excerpt']['extra']			= '';
$def['supports_excerpt']['description']		= __('Small summary field.', CCTM_TXTDOMAIN);
$def['supports_excerpt']['type']			= 'checkbox';
$def['supports_excerpt']['sort_param']	= 24;

$def['supports_trackbacks']['name']				= 'supports[]';
$def['supports_trackbacks']['id']				= 'supports_trackbacks';
$def['supports_trackbacks']['label']			= __('Trackbacks', CCTM_TXTDOMAIN);
$def['supports_trackbacks']['value']			= '';
$def['supports_trackbacks']['checked_value']	= 'trackbacks';
$def['supports_trackbacks']['extra']			= '';
$def['supports_trackbacks']['description']		= '';
$def['supports_trackbacks']['type']				= 'checkbox';
$def['supports_trackbacks']['sort_param']		= 25;

$def['supports_custom-fields']['name']			= 'supports[]';
$def['supports_custom-fields']['id']			= 'supports_custom-fields';
$def['supports_custom-fields']['label']			= __('Supports Custom Fields', CCTM_TXTDOMAIN);
$def['supports_custom-fields']['value']			= '';
$def['supports_custom-fields']['checked_value'] = 'custom-fields';
$def['supports_custom-fields']['extra']			= '';
$def['supports_custom-fields']['description']	= '';
$def['supports_custom-fields']['type']			= 'checkbox';
$def['supports_custom-fields']['sort_param']	= 26;

$def['supports_comments']['name']			= 'supports[]';
$def['supports_comments']['id']				= 'supports_comments';
$def['supports_comments']['label']			= __('Enable Comments', CCTM_TXTDOMAIN);
$def['supports_comments']['value']			= '';
$def['supports_comments']['checked_value'] 	= 'comments';
$def['supports_comments']['extra']			= '';
$def['supports_comments']['description']	= '';
$def['supports_comments']['type']			= 'checkbox';
$def['supports_comments']['sort_param']		= 27;

$def['supports_revisions']['name']			= 'supports[]';
$def['supports_revisions']['id']			= 'supports_revisions';
$def['supports_revisions']['label']			= __('Store Revisions', CCTM_TXTDOMAIN);
$def['supports_revisions']['value']			= '';
$def['supports_revisions']['checked_value'] = 'revisions';
$def['supports_revisions']['extra']			= '';
$def['supports_revisions']['description']	= '';
$def['supports_revisions']['type']			= 'checkbox';
$def['supports_revisions']['sort_param']	= 28;

$def['supports_page-attributes']['name']			= 'supports[]';
$def['supports_page-attributes']['id']				= 'supports_page-attributes';
$def['supports_page-attributes']['label']			= __('Enable Page Attributes', CCTM_TXTDOMAIN);
$def['supports_page-attributes']['value']			= '';
$def['supports_page-attributes']['checked_value'] 	= 'page-attributes';
$def['supports_page-attributes']['extra']			= '';
$def['supports_page-attributes']['description']		= __('(template and menu order; hierarchical must be checked)', CCTM_TXTDOMAIN);
$def['supports_page-attributes']['type']			= 'checkbox';
$def['supports_page-attributes']['sort_param']		= 29;

	
$def['menu_position']['name']			= 'menu_position';
$def['menu_position']['label']			= __('Menu Position', CCTM_TXTDOMAIN);
$def['menu_position']['value']			= '';
$def['menu_position']['extra']			= '';
$def['menu_position']['description']	= 
	sprintf('%1$s 
		<ul style="margin-left:40px;">
			<li><strong>5</strong> - %2$s</li>
			<li><strong>10</strong> - %3$s</li>
			<li><strong>20</strong> - %4$s</li>
			<li><strong>60</strong> - %5$s</li>
			<li><strong>100</strong> - %6$s</li>
		</ul>'
		, __('This setting determines where this post type should appear in the left-hand admin menu. Default: null (below Comments)', CCTM_TXTDOMAIN)
		, __('below Posts', CCTM_TXTDOMAIN)
		, __('below Media', CCTM_TXTDOMAIN)
		, __('below Posts', CCTM_TXTDOMAIN)
		, __('below Pages', CCTM_TXTDOMAIN)
		, __('below first separator', CCTM_TXTDOMAIN)
		, __('below second separator', CCTM_TXTDOMAIN)
	);
$def['menu_position']['type']			= 'text';
$def['menu_position']['sort_param']		= 30;

	
$def['menu_icon']['name']			= 'menu_icon';
$def['menu_icon']['label']			= __('Menu Icon', CCTM_TXTDOMAIN);
$def['menu_icon']['value']			= '';
$def['menu_icon']['extra']			= '';
$def['menu_icon']['description']	= __('Menu icon URL.', CCTM_TXTDOMAIN);
$def['menu_icon']['type']			= 'text';
$def['menu_icon']['sort_param']		= 31;

$def['use_default_menu_icon']['name']			= 'use_default_menu_icon';
$def['use_default_menu_icon']['label']			= __('Use Default Menu Icon', CCTM_TXTDOMAIN);
$def['use_default_menu_icon']['value']			= '1';
$def['use_default_menu_icon']['extra']			= '';
$def['use_default_menu_icon']['description']	= __('If checked, your post type will use the posts icon', CCTM_TXTDOMAIN);
$def['use_default_menu_icon']['type']			= 'checkbox';
$def['use_default_menu_icon']['sort_param']		= 32;

$def['rewrite_slug']['name']		= 'rewrite_slug';
$def['rewrite_slug']['label']		= __('Rewrite Slug', CCTM_TXTDOMAIN);
$def['rewrite_slug']['value']		= '';
$def['rewrite_slug']['extra']		= '';
$def['rewrite_slug']['description']	= __("Prepend posts with this slug - defaults to post type's name", CCTM_TXTDOMAIN);
$def['rewrite_slug']['type']		= 'text';
$def['rewrite_slug']['sort_param']	= 35;

$def['rewrite_with_front']['name']			= 'rewrite_with_front';
$def['rewrite_with_front']['label']			= __('Rewrite with Permalink Front', CCTM_TXTDOMAIN);
$def['rewrite_with_front']['value']			= '1';
$def['rewrite_with_front']['extra']			= '';
$def['rewrite_with_front']['description']	= __("Allow permalinks to be prepended with front base - defaults to checked", CCTM_TXTDOMAIN);
$def['rewrite_with_front']['type']			= 'checkbox';
$def['rewrite_with_front']['sort_param']	= 35;

$def['rewrite']['name']			= 'permalink_action';
$def['rewrite']['label']		= __('Permalink Action', CCTM_TXTDOMAIN);
$def['rewrite']['value']		= 'Off';
$def['rewrite']['options']		= array('Off','/%postname%/','Custom'); // ,'Custom'),
$def['rewrite']['extra']		= '';
$def['rewrite']['description']	= sprintf(
	'%1$s
	<ul style="margin-left:20px;">
		<li><strong>Off</strong> - %2$s</li>
		<li><strong>/%postname%/</strong> - %3$s</li>
		<li><strong>Custom</strong> - Evaluate the contents of slug</li>
	<ul>'
		, __('Use permalink rewrites for this post_type? Default: Off', CCTM_TXTDOMAIN)
		, __('URLs for custom post_types will always look like: http://site.com/?post_type=book&p=39 even if the rest of the site is using a different permalink structure.', CCTM_TXTDOMAIN)
		, __('You MUST use the custom permalink structure: "/%postname%/". Other formats are <strong>not</strong> supported.  Your URLs will look like http://site.com/movie/star-wars/', CCTM_TXTDOMAIN)
	);
$def['rewrite']['type']			= 'dropdown';
$def['rewrite']['sort_param']	= 37;


$def['query_var']['name']			= 'query_var';
$def['query_var']['label']			= __('Query Variable', CCTM_TXTDOMAIN);
$def['query_var']['value']			= '';
$def['query_var']['extra']			= '';
$def['query_var']['description']	= __('(optional) Name of the query var to use for this post type.
	E.g. "movie" would make for URLs like http://site.com/?movie=star-wars. 
	If blank, the default structure is http://site.com/?post_type=movie&p=18', CCTM_TXTDOMAIN);
$def['query_var']['type']			= 'text';
$def['query_var']['sort_param']	= 38;

$def['can_export']['name']			= 'can_export';
$def['can_export']['label']			= __('Can Export', CCTM_TXTDOMAIN);
$def['can_export']['value']			= '1';
$def['can_export']['extra']			= '';
$def['can_export']['description']	= __('Can this post_type be exported.', CCTM_TXTDOMAIN);
$def['can_export']['type']			= 'checkbox';
$def['can_export']['sort_param']		= 40;

$def['show_in_nav_menus']['name']			= 'show_in_nav_menus';
$def['show_in_nav_menus']['label']			= __('Show in Nav Menus', CCTM_TXTDOMAIN);
$def['show_in_nav_menus']['value']			= '1';
$def['show_in_nav_menus']['extra']			= '';
$def['show_in_nav_menus']['description']	= __('Whether post_type is available for selection in navigation menus. Default: value of public argument', CCTM_TXTDOMAIN);
$def['show_in_nav_menus']['type']			= 'checkbox';
$def['show_in_nav_menus']['sort_param']	= 40;

/*EOF*/