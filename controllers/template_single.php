<?php
//------------------------------------------------------------------------------
/**
* Manager Page -- called by page_main_controller()
* Show what a single page for this custom post-type might look like.  This is
* me throwing a bone to template editors and creators.
*
* I'm using a tpl and my parse() function because I have to print out sample PHP
* code and it's too much of a pain in the ass to include PHP without it executing.
*
* @param string $post_type
*/

$data 				= array();
$data['page_title']	= sprintf(__('Sample Themes for %s', CCTM_TXTDOMAIN), "<em>$post_type</em>");
$data['menu'] 		= sprintf('<a href="?page=cctm&a=list_post_types" class="button">%s</a>', __('Back', CCTM_TXTDOMAIN) );
$data['msg']		= '';
$data['post_type'] = $post_type;

// Validate post type
if (!self::_is_existing_post_type($post_type) ) {
	self::_page_display_error();
	return;
}

$current_theme_name = get_current_theme();
$current_theme_path = get_stylesheet_directory();

$hash = array();

$tpl = file_get_contents( CCTM_PATH.'/tpls/samples/single_post.tpl');
$tpl = htmlentities($tpl);

$data['single_page_msg'] = sprintf( __('WordPress supports a custom theme file for each registered post-type (content-type). Copy the text below into a file named <strong>%s</strong> and save it into your active theme.', CCTM_TXTDOMAIN)
	, 'single-'.$post_type.'.php'
);
$data['single_page_msg'] .= sprintf( __('You are currently using the %1$s theme. Save the file into the %2$s directory.', CCTM_TXTDOMAIN)
	, '<strong>'.$current_theme_name.'</strong>'
	, '<strong>'.$current_theme_path.'</strong>'
);


// built-in content types don't verbosely display what fields they display
/* Array
(
[product] => Array
(
    [supports] => Array
        (
            [0] => title
            [1] => editor
            [2] => author
            [3] => thumbnail
            [4] => excerpt
            [5] => trackbacks
            [6] => custom-fields
        )
*/

// Check the TYPE of custom field to handle image and relation custom fields.
// title, author, thumbnail, excerpt
$custom_fields_str = '';
$builtin_fields_str = '';
$comments_str = '';
// Built-in Fields
if ( is_array(self::$data['post_type_defs'][$post_type]['supports']) ) {
	if ( in_array('title', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t<h1><?php the_title(); ?></h1>\n";
	}
	if ( in_array('editor', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_content(); ?>\n";
	}
	if ( in_array('author', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_author(); ?>\n";
	}
	if ( in_array('thumbnail', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_post_thumbnail(); ?>\n";
	}
	if ( in_array('excerpt', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$builtin_fields_str .= "\n\t\t<?php the_excerpt(); ?>\n";
	}
	if ( in_array('comments', self::$data['post_type_defs'][$post_type]['supports']) ) {
		$comments_str .= "\n\t\t<?php comments_template(); ?>\n";
	}
}

// Custom fields
if ( isset(self::$data['post_type_defs'][$post_type]['custom_fields']) 
	&& is_array(self::$data['post_type_defs'][$post_type]['custom_fields']) ) {
	foreach ( 	$def = self::$data['post_type_defs'][$post_type]['custom_fields'] as $cf ) {
		$custom_fields_str .= sprintf("\t\t<strong>%s:</strong> <?php print_custom_field('%s'); ?><br />\n"
			, self::$data['custom_field_defs'][$cf]['label'], self::$data['custom_field_defs'][$cf]['name']);
	}
}

// Populate placeholders
$hash['post_type'] = $post_type;
$hash['built_in_fields'] = $builtin_fields_str;
$hash['custom_fields'] = $custom_fields_str;
$hash['comments'] = $comments_str;

$data['single_page_sample_code'] = self::parse($tpl, $hash);
//die('d.x.x.');
// include CCTM_PATH.'/views/sample_template.php';
$data['content'] = CCTM::load_view('sample_template.php', $data);
print CCTM::load_view('templates/default.php', $data);