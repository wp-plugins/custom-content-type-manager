<?php
/*------------------------------------------------------------------------------
PHP controller called via thickbox when a user wants to export a content 
definition

http://www.vbulletin.com/forum/showthread.php/70959-HTTP-Headers-to-force-file-to-download-rather-than-auto-open

require_once( realpath('../../../').'/wp-config.php' );
require_once( realpath('../../../').'/wp-admin/includes/post.php'); // TO-DO: what if the wp-admin dir changes?

if ( !current_user_can('edit_posts') )
{
	wp_die(__('You do not have permission to do that.'));
}


if ( submitted ) {
	$save_me = array();
	$save_me['title'] = 
	$save_me['author'] = 
	$save_me['description'] = 
	$save_me['url'] = 
	$save_me['timestamp_creation'] = time();
	$save_me['mother_site'] = site_url();
	$save_me['payload'] = CCTM::$data;
	
	serialize($save_me);
} 
else {

}
------------------------------------------------------------------------------*/


?>
<html>
<head>
	<title>Export</title>
</head>
<body>

<p>Please add a bit more information to your package.</p>

<form>
	<label for="title" id="title_label">Title</label><br/>
	<input type="text" name="title" id="title" value="" /><br/>
	
	<label for="author" id="author_label">Author</label><br/>
	<input type="text" name="author" id="author" value="" /><br/>

	<label for="url" id="author_label">Your URL</label><br/>
	<input type="text" name="url" id="url" value="" /><br/>

	<label for="description" id="description_label">Description</label><br/>
	<textarea name="description" id="description" value=""></textarea>
	<br/>
	<input type="submit" name="submit" value="Export"/>
	 
</form>
</body>

</html>