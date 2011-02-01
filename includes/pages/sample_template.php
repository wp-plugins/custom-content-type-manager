<div class="wrap">
<style type="text/css">
	.sample_code_textarea { 
		width: 100%; 
		margin: 0; 
		padding: 0; 
		border-width: 0; }
</style>


	<?php screen_icon(); ?>
	<h2>Custom Content Type Manager <a href="?page=<?php print self::admin_menu_slug; ?>" class="button add-new-h2"><?php _e('Back'); ?></a></h2>

	<h3 class="cctm_subheading"><?php _e('Single Page (Front End)',CCTM_TXTDOMAIN); ?></h3>
	<p>
		<?php print $single_page_msg; ?>
	</p>
	<br />

	<textarea cols="80" rows="10" class="sample_code_textarea" style="border: 1px solid black;"><?php print $single_page_sample_code; ?></textarea>


	<h3 class="cctm_subheading"><?php _e('CSS for Manager Pages', CCTM_TXTDOMAIN); ?></h3>
	<p>
		<?php print $manager_page_css_msg; ?>
	</p>	
	
	<textarea cols="80" rows="10" class="sample_code_textarea" style="border: 1px solid black;"><?php print $manager_page_sample_css; ?></textarea>



	<h3 class="cctm_subheading"><?php _e('HTML for Manager Pages', CCTM_TXTDOMAIN); ?></h3>
	<p>
		<?php print $manager_page_html_msg; ?>
	</p>	
	<textarea cols="80" rows="10" class="sample_code_textarea" style="border: 1px solid black;"><?php print $manager_page_sample_html; ?></textarea>
		
</div>