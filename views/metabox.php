<script type="text/javascript">
	function set_continue_editing() {
		jQuery('#continue_editing').val(1);
		return true;
	}
</script>

<div class="metabox-holder">

<form id="custom_post_type_manager_basic_form" method="post" action="">

	<input type="hidden" name="continue_editing" id="continue_editing" value="0" />
	
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<?php print $data['fields']; ?>
	
	<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('Metabox Definition', CCTM_TXTDOMAIN); ?></span></h3>
		<div class="inside">			
			<?php // print $data['associations']; ?>
		</div><!-- /inside -->
	</div><!-- /postbox -->
		
	<br />
	<input type="submit" class="button-primary" value="<?php _e('Save', CCTM_TXTDOMAIN ); ?>" />
	
	<input type="submit" class="button" onclick="javascript:set_continue_editing();" value="<?php _e('Save and Continue Editing', CCTM_TXTDOMAIN ); ?>" />

	<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_fields&a=list_custom_field_types" title="<?php _e('Cancel'); ?>" class="button"><?php _e('Cancel'); ?></a>
</form>

</div><!-- /metabox-holder -->