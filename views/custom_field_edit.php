<script type="text/javascript">
jQuery(document).ready( function() {
    jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
    jQuery('.postbox h3').click( function() {
        jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
    });
}
</script>

<form id="custom_post_type_manager_basic_form" method="post" action="">


	<table class="custom_field_info">
		<tr>
			<td colspan="2">
				<h3 class="field_type_name"><?php print $data['name']; ?></h3>
			</td>
		</tr>
		<tr>
			<td>
				<span class="custom_field_icon"><?php print $data['icon']; ?></span>
			</td>
			<td>
				<span class="custom_field_description"><?php print $data['description']; ?>
				<br />
				<a href="<?php print $data['url']; ?>" target="_blank"><?php _e('More Information', CCTM_TXTDOMAIN); ?></a>
				</span>
			</td>
		</tr>
	</table>
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<p><strong><?php _e('Field Type', CCTM_TXTDOMAIN); ?>:</strong> <?php print $data['field_type']; ?> &nbsp; <a href="?page=cctm_fields&a=change_field_type&field=<?php print $data['field_name']; ?>&_wpnonce=<?php print wp_create_nonce('cctm_change_field_type'); ?>"><?php _e('Change Field Type', CCTM_TXTDOMAIN); ?></a></p>
	
	<?php print $data['fields']; ?>
	
	<h3><?php _e('Associations', CCTM_TXTDOMAIN); ?></h3>
	<p class="cctm_decscription"><?php _e('Which post-types should this field be attached to?', CCTM_TXTDOMAIN); ?></p>
	
	<?php print $data['associations']; ?>
	
	<br />
	<input type="submit" class="button-primary" value="<?php _e('Save', CCTM_TXTDOMAIN ); ?>" />

	<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_fields&a=list_custom_field_types" title="<?php _e('Cancel'); ?>" class="button"><?php _e('Cancel'); ?></a>
</form>