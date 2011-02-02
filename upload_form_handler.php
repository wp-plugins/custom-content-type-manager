<?php
/*------------------------------------------------------------------------------
This is complicated.  You can't submit $_FILES to be uploaded via a simple
Ajax form submission (other form fields are fine to submit like this). 
So the problem is that we NEED to submit the upload form via Ajax, and because
the upload form is iFramed in a thickbox, we can't submit the page, else we 
lose the thickbox, so we HAVE to submit the form via Ajax. The solution is 
to post the data to this page, which then submits itself. Definitely more
janky than genius here, but I just want this to work.

The first time this page is posted to, the $_POST['secondary'] is not set.
------------------------------------------------------------------------------*/
/*

if ( isset( $_POST['async-upload'] ) )
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<title>Ajax File Upload</title>

	<script type="text/javascript" language="javascript">
		function submitform(){
			document.getElementById('uploadForm').submit();
		}
	</script>
</head>
<body onload="javascript:submitform()">
 	<p>No person should ever see this.</p>
	<form name="uploadForm" id="uploadForm" action="upload_form_handler.php" method="post">
		<input type="file" name="upload_this" value="<?php print $_POST['async-upload']; ?>">
		<input type="submit" value="Submit"/>
	</form>                
</body>
</html>
<?php
}
else
*/
require_once( realpath('../../../').'/wp-config.php' );
require_once('../../../wp-admin/admin.php');

//if ( !empty($_POST) && isset($_POST['secondary']) && !empty($_FILES) )
if ( !empty($_POST) && !empty($_FILES) )// && isset($_POST['async-upload']) && !empty($_FILES) )
{

	$id = media_handle_upload('async-upload',''); //post id of Client Files page
	
	// on success, $id should be an inteter (last_insert_id), on error, it's a WP_Error Object 
	if ( is_object($id) )
	{
		print "<p>There was a problem uploading. Please try using WordPress' <a href='media-new.php'>built-in manager</a> to upload new media.</p>";
	}
	else
	{
		/*------------------------------------------------------------------------------
		This javascript should refresh the parent frame after the form submits.
		------------------------------------------------------------------------------*/
		?>
		<script type="text/javascript">
			function addLoadEvent(func) {
				var oldonload = window.onload;
				if (typeof window.onload != 'function') {
					window.onload = func;
				} else {
					window.onload = function() {
				  		if (oldonload) {
				    		oldonload();
				  		}
			  			func();
					}
				}
			}
			
			addLoadEvent(parent.clear_search);
		</script>
		
		<p><span class="button" onclick="javascript:parent.clear_search();">Click here</span> if your page does not refresh.</p>
			
<?php
	}

}
else
{
	//Form not submitted yet
?>

	<!-- span class="button" onclick="javascript:parent.clear_search();">Back</span -->
	
<?php 
}



/*EOF*/