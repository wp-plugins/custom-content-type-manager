=== Custom Content Type Manager ===
Contributors: fireproofsocks
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=355ULXBFXYL8N
Tags: cms, content management, custom post types, custom content types, custom fields, images, image fields, ecommerce, modx
Requires at least: 3.0.1
Tested up to: 3.1.1
Stable tag: 0.8.8

Create custom content types (aka post types), standardize custom fields for each type, including dropdowns and images. Gives WP CMS functionality.

== Description ==

The Custom Content Type Manager plugin allows users to create custom content types (also known as post types) and standardize custom fields for each, including dropdowns, checkboxes, and images. This gives WordPress CMS functionality making it easier to use WP for eCommerce or content-driven sites.

One of the problems with WordPress' custom fields is that they are not standardized: users must add them one at a time each time they create a new post. Furthermore, by default, WordPress' custom fields supports only text fields. This plugin lets users define a list of custom fields for each content type so that the same custom fields appear on each new post in a uniform way. 

For example, you can define a custom content type for "movie", then add a textarea field for "Plot Summary", an image field for "Poster Image", and a dropdown field for "Rating". All of these fields are available in the template's `single-movie.php` template file by using the included print_custom_field() function, e.g. `<?php print_custom_field('rating'); ?>`

Custom content types get their own link in the admin menus and their own URL structure.

Please note that this plugin is still in development and I won't consider it stable until version 1.0!!! I try to make my code clean and functional, but there are no guarantees!  If you need certain features developed *hire me*: only when I'm under contract can I afford to guarantee functionality.  Please be willing to file bugs at http://code.google.com/p/wordpress-custom-content-type-manager/issues/list

== Installation ==

1. Upload this plugin's folder to the `/wp-content/plugins/` directory or install it using the traditional WordPress plugin installation.
1. Activate the plugin through the 'Plugins' menu in the WordPress manager.
1. Upon activation you can adjust the plugin settings by clicking the newly created "Custom Content Types" menu item, or click this plugin's "Settings" link on the Plugins page.
1. After clicking the Settings link, you will see a list of content types -- there are two built-in types listed: post and page. To test this plugin, try adding a new content type named "movie" by clicking the "Add Custom Content Type" button at the top of the page.
1. There are a *lot* of options when setting up a new content type, but all the necessary ones are shown on the first page.  Pay attention to the "Name", "Show Admin User Interface", and "Public" settings. "Show Admin User Interface" *must* be checked in order for you to be able to create or edit new instances of your custom content type. 
1. Save the new content by clicking the "Create New Content Type" button.
1. Your content type should now be listed under on the main Custom Content Types Manager settings page. Activate your new content type by clicking the blue "Activate" link.
1. Once you have activated the content type, you should see a new menu item in the left-hand admin menu. E.g. "Movies" in our example.
1. Try adding some custom fields to your new content type by clicking on the "Manage Custom Fields" link on the settings page.
1. You can add as many custom fields as you want by clicking the "Add Custom Field" button at the top of the page, e.g. try adding a "plot_summary" field using a "textarea" input type, and try adding a "rating" dropdown. 
1. When you are finished configuring your custom fields, click the "Save Changes" button.
1. Now try adding a new instance of your content type ("Movies" in this example). Click the link in the left-hand admin menu to add a movie.
1. Your new "Movie" post will have the custom fields you defined.
1. If you have added any media custom fields, be sure to upload some images using the WordPress "Media" menu in the left-hand menu.


== Frequently Asked Questions ==

Please see the online [FAQ](http://code.google.com/p/wordpress-custom-content-type-manager/wiki/FAQ) for the most current information. 


== Screenshots ==

1. After activating this plugin, you can create custom content types (post types) by using the configuration page for this plugin. Click the "Custom Content Types" link under the Settings menu or click this plugin's "Settings" shortcut link in on the Plugin administration page.
2. You can create a new content type by clicking the button at the top of the settings page.
3. There are a lot of options available when you create a new content type, only some of them are pictured.
4. You can define new custom fields by clicking on the "Manage Custom Fields" link for any content type.
5. Clicking the "activate" link for any content type will cause its fields to be standardized and it will show up in the administration menus.
6. Once you have defined custom fields for a content type and you have activated that content type, those custom fields will show up when you edit a new post. Here's what the custom fields look like when I create a new "Movie" post.

== Changelog ==

You can always checkout the most recent version of the code by going to your wp-content/plugins directory and executing the following command from the command-line:

	svn checkout http://plugins.svn.wordpress.org/custom-content-type-manager/trunk custom-content-type-manager 
	
= In Development (in the trunk) =

* pending...

= 0.8.8 =

* More CRUD-like interface for creating and editing custom fields one at a time.
* Object-Oriented class structure implemented for custom fields in accordance with future plans for more field types
* Drag and Drop interface added for Custom Fields to change sort order
* Added support for built-in taxonomies (Categories and Tags).
* Fixed unreported bugs affecting custom tpls. 
* Fixed bug causing popup thickbox to load incorrectly: [Issue 17](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=17&can=1)
* Styling for the manager updated to match what's used by WordPress 3.1.
* Greatly improved administration interface including support for icons and a series of tabs for dividing the multi-part form for creating/editing content types.
* Reduced MySQL requirements to version 4.1.2 (same as WordPress 3.1) after feedback that the plugin is working fine in MySQL 4: [issue 28](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=28&can=1)
* Fixed typos in CCTMTests.php re the CCTM_TXTDOMAIN: [issue 29](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=29&can=1).
* Added optional prefix for template *function get_all_fields_of_type()*: [Issue 26](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=26&can=1)
* Three new template functions added per [Issue 25](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=25&can=1)
** *get_custom_field_meta()*
** *print_custom_field_meta()*
** *get_custom_field_def()*
See [Template Functions](http://code.google.com/p/wordpress-custom-content-type-manager/wiki/TemplateFunctions) in the wiki.


= 0.8.7 =
* Adds HTML head and body tags back to the tpls/post_selector/main.tpl to correct issue 17 (http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=17&can=1).

= 0.8.6 =
* Fixes bad CSS declaration: [issue 1](http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=1)
* Fixed omission in sample template placeholders.

= 0.8.5 =
* Resubmitting to placate WP's repository.

= 0.8.4 =

* Resubmitting to placate WP's repository.

= 0.8.3 =

* Customized manager templates. This is useful if you need to customize the manager interface for the custom fields.
* Updated sample templates under the "View Sample Template" link for each content type.
* Added image/media uploading functionality directly from the custom fields.
* Fixed glitch in Javascript element counter that screwed up dropdown menu creation: due to this bug, you could only add a dropbox to the first element because the wrapper div's id was fixed statically as the same id, so adding a dropdown menu always wrote the new HTML to that same div (inside the first custom field).
* Control buttons now at top and bottom of manage custom fields.
* Links to bug tracking / wiki for this project.
* Some basic HTML cleanup.
* Moved some files around for better organization.

=0.8.2=
* WordPress 3.0.4 fixed some bugs that affected the functionality of this plugin: now you CAN add custom content posts to WordPress menus.
* WordPress has not recognized the updates to this plugin (apparently due to a glitch), so currently the only way to get the most recent version of this is to check it out via SVN.

= 0.8.1 =
* Fixes problem saving posts. The problem had to do with wp-nonces and the admin referrer was being checked, but not generated, so the check failed. Oops.

= 0.8.0 =
* Initial public release. Collaborators can check out code at http://code.google.com/p/wordpress-custom-content-type-manager/

== Requirements ==

* WordPress 3.0.1 or greater
* PHP 5.2.6 or greater
* MySQL 4.1.2 or greater

These requirements are tested during WordPress initialization; the plugin will not load if these requirements are not met. Error messaging will fail if the user is using a version of WordPress older than version 2.0.11. 


== About ==

This plugin was written in part for the book [WordPress 3 Plugin Development Essentials](http://www.amazon.com/WordPress-3-Plugin-Development-Essentials/dp/184951352X/ref=sr_1_2?ie=UTF8&s=books&qid=1302098332&sr=1-2) published by Packt. It was inspired by the [Custom-Post Type UI](http://wordpress.org/extend/plugins/custom-post-type-ui/) plugin written by Brad Williams. The Custom-Post Type UI plugin offers some of the same features, but I felt that its architecture was flawed: it stores data as taxonomical terms, which is conceptually awkward at a development level, and more importantly, it limits the each custom field to 200 characters of data, making it impossible to store certain types of custom content.

On the surface, this plugin is similar, but this plugin "correctly" stores custom field data as post meta data, which allows allows for different input types (e.g. checkboxes, dropdowns, and images) and the custom fields offered by this plugin can support data of virtually unlimited size. For example, you could define a WYSIWYG custom field for your custom content type and it could hold many pages of data.

The architecture for this plugin was also inspired by [MODx](http://modx.com/). WordPress is making progress as a viable content management system, but even after the thousands of lines of code in this plugin, it still does not support all the features in a system like MODx. WordPress templates are particularly limited by comparison. WordPress is great system for many scenarios, but if you're feeling that WordPress is starting to tear apart at the seams when it comes to custom content, it may be worth a look at another plugin or some of the other available systems.

== Future TO-DO == 

If you are eager to see one of these features implemented in a future release, please share your feedback at the official Issues page: http://code.google.com/p/wordpress-custom-content-type-manager/issues/list

And if you REALLY want some of these features implemented, you can hire me to complete portions of your project or make a donation: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=355ULXBFXYL8N  Many of the surges in development in this plugin were instigated by projects that required this plugin's use.

== Upgrade Notice ==

= 0.8.8 =
Improved administration interface, new template functions, lots bug fixes; this is a big release.  If you are upgrading to 0.8.8 from a previous version, you should *uninstall* the plugin, then *re-install*.  This will ensure that the data-structure in the database is updated appropriately.

= 0.8.7 =
Adds HTML head and body tags back to the tpls/post_selector/main.tpl to correct issue 17 (http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=17&can=1).

= 0.8.6 =
Fixes CSS declaration that conflicted with Posts->Categories. Corrects examples in sample templates.

= 0.8.5 = 
Fixes some major bugs in managing custom fields. Now allows direct image uploading. Allows customized manager templates. (re-submit of 0.8.3)

= 0.8.4 = 
Was not picked up correctly by WordPress' repo

= 0.8.3 = 
Was not picked up correctly by WordPress' repo

= 0.8.2 =
Fixes a couple other glitches: apostrophes in media custom fields, editing content types.
Resubmitting this to get the updates to show up in WordPress' repository.  Sorry folks... seems that the WP captain has jumped ship, so I have no working instructions on how to get my updates to percolate down to the users.  

= 0.8.1 =
Fixing glitch in saving posts and pages.

= 0.8.0 =
Initial public release.

== See also and References ==
* http://kovshenin.com/archives/extending-custom-post-types-in-wordpress-3-0/
* http://kovshenin.com/archives/custom-post-types-in-wordpress-3-0/
* http://axcoto.com/blog/article/307
* Attachments in Custom Post Types:
http://xplus3.net/2010/08/08/archives-for-custom-post-types-in-wordpress/
* Taxonomies:
http://net.tutsplus.com/tutorials/wordpress/introducing-wordpress-3-custom-taxonomies/
* Editing Attachments
http://xplus3.net/2008/11/17/custom-thumbnails-wordpress-plugin/
