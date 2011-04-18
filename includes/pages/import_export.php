<div class="wrap">
	<h2>
	<img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" class="polaroid"/> 
	Custom Content Type Manager</h2>

	<?php print $msg; ?>

	<h2>Export</h2>
	<p><a href="">Save to File</a></p>
	<!-- 
	
	-->

	<form>
		<label>Import Settings File</label>
		<input type="file" id="cctm_settings_file" name="cctm_settings_file" />
	</form>

	<br/>
	
	<?php include('components/footer.php'); ?>
	
</div>