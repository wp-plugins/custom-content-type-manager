=== Custom Content Type Manager ===
Contributors: fireproofsocks
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=355ULXBFXYL8N
Tags: cms, content management, custom post types, custom content types, custom fields, images, image fields, ecommerce, modx
Requires at least: 3.0.1
Tested up to: 3.0.5
Stable tag: 0.8.6

Create custom content types (aka post types), standardize custom fields for each type, including dropdowns and images. Gives WP CMS functionality.

== Description ==

The Custom Content Type Manager plugin allows users to create custom content types (also known as post types) and standardize custom fields for each content type, including dropdowns, checkboxes, and images. This gives WordPress CMS functionality making it easier to use WP for eCommerce or content-driven sites.

One of the problems with WordPress' custom fields is that they are not standardized: users must add them one at a time each time they create a new post. Furthermore, by default, WordPress' custom fields supports only text fields. This plugin lets users define a list of custom fields for each content type so that the same custom fields appear on each new post in a uniform way. 

For example, you can define a custom content type for "movie", then add a textarea field for "Plot Summary", an image field for "Poster Image", and a dropdown field for "Rating". All of these fields are available in the template's `single-movie.php` template file by using the included print_custom_field() function, e.g. `<?php print_custom_field('rating'); ?>`

Custom content types get their own link in the admin menus and their own URL structure.

Note that this plugin is still in development. Please file bugs at http://code.google.com/p/wordpress-custom-content-type-manager/issues/list

== Installation ==

1. Upload this plugin's folder to the `/wp-content/plugins/` directory or install it using the traditional WordPress plugin installation.
1. Activate the plugin through the 'Plugins' menu in the WordPress manager.
1. Under the "Settings" menu, click the newly created "Custom Content Types" link, or click this plugin's "Settings" link on the Plugins page.
1. After clicking the Settings link, you'll see a list of content types -- there are two built-in types listed: post and page. To test this plugin, try adding a new content type named "movie" by clicking the "Add Custom Content Type" button at the top of the page.
1. There are a *lot* of options when setting up a new content type, but pay attention to the "Name", "Show Admin User Interface", and "Public" settings. "Show Admin User Interface" *must* be checked in order for you to be able to create or edit new instances of your custom content type. 
1. Save the new content by clicking the "Create New Content Type" button.
1. Your content type should now be listed under on the Custom Content Types Manager settings page. Activate your new content type by clicking the blue "Activate" link.
1. Once you have activated the content type, you should see a new menu item in the left-hand admin menu. E.g. "Movies" in our example.
1. Try adding some custom fields to your new content type by clicking on the "Manage Custom Fields" link on the settings page.
1. You can add as many custom fields as you want by clicking the "Add Custom Field" button at the top of the page, e.g. try adding a "plot_summary" field using a "textarea" input type, and try adding a "rating" dropdown. 
1. When you are finished configuring your custom fields, click the "Save Changes" button.
1. Now try adding a new instance of your content type ("Movies" in this example). Click the link in the left-hand admin menu to add a movie.
1. Your new "Movie" post will have the custom fields you defined.
1. If you have added any media custom fields, be sure to upload some images using the WordPress "Media" menu in the left-hand menu.


== Frequently Asked Questions ==

= What does activating a custom content type do? =

When you activate a custom content type, you ensure that it gets registered with WordPress. Once the content type is registered, a menu item will get created (so long as you checked the "Show Admin User Interface" box) and you ensure that its custom fields become standardized. If the "Public" box was checked for this content type, then the general public can access posts created under this content type using the URL structure defined by the "Permalink Action" and "Query Var" settings, e.g. http://site.com/?post_type=book&p=39

"Activating" a built-in post-type (i.e. pages or posts) will force their custom fields to be standardized. If you do not intend to standardize the custom fields for pages or posts, then there is no reason for you to activate them. 

= What does deactivating a custom content type do? =

If you deactivate a custom content type, its settings remain in the database, but every other trace of it vanishes: any published posts under this content type will not be visible to the outside world, and the WordPress manager will no longer generate a link in the admin menu for you to create or edit posts in this content type.

Deactivating a built-in post-type (a.k.a. content type) merely stops standardizing the custom fields; deactivating a built-in post-type has no other affect.

= What types of custom fields are supported? =

Text fields, textarea, WYSIWYG, dropdowns (with customizable options), checkboxes, image fields, media fields (which allow the user to select an image, video, or audio clip), and relation fields (which allow the user to select another post of any type to be related to).

= How do I add images or video into a custom field? =

The media-related custom fields tie into WordPress' "attachment" posts, so if you have already uploaded images or video using the Media menu, they will show up for selection when edit a post using a custom image or media field.  You can now choose "Add New Image" when you browse existing images.

= How do I make my custom field values show up in my templates? =

Content and templates must go hand in hand. If you have defined custom fields, you have to modify your theme files to accommodate them.  There are two included theme functions intended to help you with this task:

* get_custom_field() -- gets the value
* print_custom_field() -- prints the value

In this plugin's settings area, each content-type has a link to "View Sample Templates" -- this page gives you a fully customized example showing demonstrating a custom theme file for your custom content type.

See the includes/functions.php file in this plugin's directory for some other theme functions that are in development.

= How do I use a Custom Image Field =

The trick here is that the custom field stores a foreign key, which points to the wp_posts table where the post_type is an "attachment". So you use the get_custom_field() function and pass its output to one of WordPress' built-in image functions. For example, put the following code in your theme file (assuming your custom field is named 'my_image_field'):

`<?php $image_id = get_custom_field('my_image_field'); ?>`
`<?php print wp_get_attachment_image($image_id, 'full'); ?>`

See the wiki here: http://code.google.com/p/wordpress-custom-content-type-manager/wiki/CreateImageField


= How can I use this plugin to support an eCommerce site? =

There are many ways to structure a site depending on what you are selling. For an example, let's say you are selling T-shirts. You could create a "shirt" content type, then you could define custom fields for size, color, and perhaps several image fields. Once you had defined the custom content type, you could simply create several "shirt" posts for each shirt design that you are selling.

== Known Bugs ==

* Prior to WordPress 3.0.4 you cannot add menu items to navigation menus when this plugin is enabled (under Appearance --> Menus). The Ajax call to wp-admin/admin-ajax.php encounters a 403 error: "Are you sure you want to do this?".
* Don't use the same name for a taxonomy and a content-type (post-type) -- this isn't a bug per se, it's just good advice. Saving a content-type now checks names against registered taxonomies, but nothing prevents you from using another plugin to register other taxonomies with names that conflict with existing post-types.

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

= 0.8.7 =
* Adds HTML head and body tags back to the tpls/post_selector/main.tpl to correct issue 17 (http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=17&can=1).

= 0.8.6 =
* Fixes bad CSS declaration (issue #1 http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=1)
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
* MySQL 5.0.41 or greater

These requirements are tested during WordPress initialization; the plugin will not load if these requirements are not met. Error messaging will fail if the user is using a version of WordPress older than version 2.0.11. 


== About ==

This plugin was written in part for a forthcoming book on WordPress Plugin Development published by Packt. It was inspired by the [Custom-Post Type UI](http://wordpress.org/extend/plugins/custom-post-type-ui/) plugin written by Brad Williams. The Custom-Post Type UI plugin offers some of the same features, but I felt that its architecture was flawed: it stores data as taxonomical terms, which is conceptually awkward at a development level, and more importantly, it limits the each custom field to 200 characters of data, making it impossible to store certain types of custom content.

On the surface, this plugin is similar, but this plugin "correctly" stores custom field data as post meta data, which allows allows for different input types (e.g. checkboxes, dropdowns, and images) and the custom fields offered by this plugin can support data of virtually unlimited size. For example, you could define a WYSIWYG custom field for your custom content type and it could hold many pages of data.

The architecture for this plugin was also inspired by [MODx](http://modxcms.com/). WordPress is making progress as a viable content management system, but even after the thousands of lines of code in this plugin, it still does not support all the features in a system like MODx. WordPress templates are particularly limited by comparison. WordPress is great system for many scenarios, but if you're feeling that WordPress is starting to tear apart at the seams when it comes to custom content, it may be worth a look at another plugin or some of the other available systems.

== Future TO-DO == 

If you are eager to see one of these features implemented in a future release, please share your feedback at the official Issues page: http://code.google.com/p/wordpress-custom-content-type-manager/issues/list

== Upgrade Notice ==

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
