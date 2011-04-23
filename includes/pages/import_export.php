<?php
/*------------------------------------------------------------------------------
Note that thickbox links have to end with "?" because aparently WP is appending
random numbers to the end of the pages (probably due to an IE caching issue).
I was getting lots of errors like this in my logs:
[client 127.0.0.1] File does not exist: /path/to/html/wp-content/plugins/cctm/export.php&random=1303532738836, referer: http://pretasurf:8888/wp-admin/admin.php?page=cctmimport_export

------------------------------------------------------------------------------*/
?>
<div class="wrap">
	<h2>
	<img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" class="polaroid"/> 
	Custom Content Type Manager</h2>

	<?php print $msg; ?>

	<h2>Export</h2>

	<p><a class="thickbox" href="<?php print CCTM_URL; ?>/export.php?">Save to File</a></p>
	<!-- 
	
	-->

	<form>
		<label>Import Settings File</label>
		<input type="file" id="cctm_settings_file" name="cctm_settings_file" />
	</form>

	<br/>
	
	<?php include('components/footer.php'); ?>
	
</div>