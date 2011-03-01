<script>
	
	function save_order()
	{
		var i=0;
		jQuery(".store_me").each(function(){
	        jQuery(this).toggleClass("example");
	        jQuery(this).val(i);
			i=i+1;
      	});
	}
</script>


<script>
jQuery(function() {
	jQuery( "#custom-field-list" ).sortable();
	jQuery( "#custom-field-list" ).disableSelection();

	
});
</script>


<form id="custom_post_type_manager_basic_form" method="post" action="<?php self::_link_manage_custom_fields($post_type);?>">

<div class="wrap">
	<h2>
	<a href="?page=<?php print self::admin_menu_slug;?>" title="<?php _e('Back'); ?>"><img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" /></a>
	<?php print $post_type; ?> : Custom Fields </h2>

	<?php print $msg; ?>

	<?php print self::_link_create_custom_field($post_type); ?>
	<?php if (!$def_cnt) { return; } ?>	
	<input type="submit" 
		class="button-primary" 
		onclick="javascript:save_order();" value="<?php _e('Save Field Order', CCTM_TXTDOMAIN ); ?>" />

	
	<?php wp_nonce_field($action_name, $nonce_name); ?>
	
<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope='col' id='sorter' class=''  style="">&nbsp;</th>
		<th scope='col' id='name' class='manage-column column-name'  style=""><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope='col' id='description' class='manage-column column-description'  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope='col' id='sorter' class=''  style="">&nbsp;</th>
		<th scope='col' id='name' class='manage-column column-name'  style=""><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope='col' id='description' class='manage-column column-description'  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $fields; ?>
	
</tbody>
</table>

 <?php print self::_link_create_custom_field($post_type); ?>

	<input type="submit" 
		class="button-primary" 
		onclick="javascript:save_order();" value="<?php _e('Save Field Order', CCTM_TXTDOMAIN ); ?>" />

</form>

</div>