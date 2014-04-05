<?php
/*------------------------------------------------------------------------------
Plugin Name: CCTM : Advanced Custom Post Types
Description: Allows users to create custom post types and custom fields, including dropdowns, checkboxes, and images. This gives WordPress CMS functionality making it easier to use WP for eCommerce or content-driven sites.
Author: Everett Griffiths
Version: 0.9.7.13
Author URI: http://www.craftsmancoding.com/
Plugin URI: http://code.google.com/p/wordpress-custom-content-type-manager/
------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------
CONFIGURATION (for the developer): 

Define the names of functions and classes used by this plugin so we can test 
for conflicts prior to loading the plugin and message the WP admins if there are
any conflicts.

$function_names_used -- add any functions declared by this plugin in the 
	main namespace (e.g. utility functions or theme functions).

$class_names_used -- add any class names that are declared by this plugin.

Warning: the text-domain for the __() localization functions is hardcoded.
------------------------------------------------------------------------------*/

/**
 * Warning: this can interfere with WordPress's behavior. 
 * So we should only get involved if it's one of our classes.
 * See http://stackoverflow.com/questions/11833034/non-destructive-spl-autoload-register
 */
spl_autoload_register(function($class) {
    // Strip the 'CCTM\' namespace identifier if present
    $prefix = 'CCTM\\';
    if (substr($class, 0, strlen($prefix)) == $prefix) {
        $class = substr($class, strlen($prefix));
    }
    // So our namespaces correspond to our folder structure
    $file = dirname(__FILE__).'/src/'.str_replace('\\', '/', $class).'.php';

    // Allow the user directory to provide overrides
    if (false) {
        // ... TODO ...    
    }
    elseif (is_readable($file)) {
        require_once $file;
    }
},false);

// Bootstrapping: Dependency injection
// new \CCTM\Log(new \CCTM\File());

// Run tests when the plugin is activated.
register_activation_hook(__FILE__, '\CCTM\Selfcheck::run');

//register setting
add_action('admin_init', function(){ 
    CCTM\License::register_option();
    CCTM\License::activate();
});

require_once 'includes/constants.php';
include_once 'includes/functions.php';

// Register Ajax Controllers (easier to hard-code than do scan dirs)
// pattern is: 'wp_ajax_{file-basename}', CCTM\Ajax::{file-basename}
add_action('wp_ajax_bulk_add',                  '\CCTM\Ajax::bulk_add');
add_action('wp_ajax_download_def',              '\CCTM\Ajax::download_def');
add_action('wp_ajax_format_getpostsquery_args', '\CCTM\Ajax::format_getpostsquery_args');
add_action('wp_ajax_get_posts',                 '\CCTM\Ajax::get_posts');
add_action('wp_ajax_get_search_form',           '\CCTM\Ajax::get_search_form');
add_action('wp_ajax_get_selected_posts',        '\CCTM\Ajax::get_selected_posts');
add_action('wp_ajax_get_shortcode',             '\CCTM\Ajax::get_shortcode');
add_action('wp_ajax_get_tpl',                   '\CCTM\Ajax::get_tpl');
add_action('wp_ajax_get_validator_options',     '\CCTM\Ajax::get_validator_options');
add_action('wp_ajax_get_widget_post_tpl',       '\CCTM\Ajax::get_widget_post_tpl');
add_action('wp_ajax_list_custom_fields',        '\CCTM\Ajax::list_custom_fields');
add_action('wp_ajax_post_content_widget',       '\CCTM\Ajax::post_content_widget');
add_action('wp_ajax_post_content_widget',       '\CCTM\Ajax::post_content_widget');
add_action('wp_ajax_preview_def',               '\CCTM\Ajax::preview_def');
add_action('wp_ajax_summarize_posts_form',      '\CCTM\Ajax::summarize_posts_form');
add_action('wp_ajax_summarize_posts_get_args',  '\CCTM\Ajax::summarize_posts_get_args');
add_action('wp_ajax_summarize_posts_widget',    '\CCTM\Ajax::summarize_posts_widget');
add_action('wp_ajax_upload_image',              '\CCTM\Ajax::upload_image');

// Load up the textdomain(s) for translations
CCTM\Load::file('/config/lang/dictionaries.php');
CCTM\CCTM::$license = CCTM\License::check();

// Generate admin menu, bootstrap CSS/JS
add_action('admin_init', '\CCTM\CCTM::admin_init');

// Create custom plugin settings menu
add_action('admin_menu', '\CCTM\CCTM::create_admin_menu');
add_filter('plugin_action_links', '\CCTM\CCTM::add_plugin_settings_link', 10, 2 );

require_once 'loader.php';


/*EOF*/