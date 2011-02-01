<?php
/*------------------------------------------------------------------------------
This include is a bit of a cluster.  I wanted to demonstrate how to use 
"traditional" PHP files for templates, but this fugly thing got completely
out of hand...
------------------------------------------------------------------------------*/
?>
<tr class='<?php print $class; ?>'>
	<td class="plugin-title"><strong><?php print $post_type; ?></strong></td>
	<td class="desc">
		<p><?php print $description; ?></p>
	</td>
</tr>
<tr class='<?php print $class; ?> second'>
	<td class="plugin-title">
		<div class="row-actions-visible">
<?php 
//------------------------------------------------------------------------------
// We got 4 ways to end this story...
// Active Built-in Post Types
if ($is_active && in_array($post_type, self::$built_in_post_types)):	
//------------------------------------------------------------------------------
?>		
			<span class='deactivate'>
				<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=7&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title="<?php _e('Deactivate this content type', CCTM_TXTDOMAIN); ?>"><?php _e('Deactivate',CCTM_TXTDOMAIN); ?></a>
			</span>
		</div>
	</td>
	<td class="desc">
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=4&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('Manage Custom Fields', CCTM_TXTDOMAIN); ?></a> 
		| 
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=8&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('View Sample Templates', CCTM_TXTDOMAIN); ?> 
	</td>

<?php 
//------------------------------------------------------------------------------
// Inactive Built-In Post Types
elseif (!$is_active && in_array($post_type, self::$built_in_post_types) ): 
//------------------------------------------------------------------------------
?>
			<span class="activate">
				<a href="?page=<?php print self::admin_menu_slug;?>&<?php print self::action_param; ?>=6&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title="<?php _e('Activate custom field management', CCTM_TXTDOMAIN); ?>" class="edit"><?php _e('Activate', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="delete"></span>
		</div>
	</td>
	
	<td class="desc">
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=4&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('Manage Custom Fields', CCTM_TXTDOMAIN); ?></a>
		| 
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=8&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('View Sample Templates', CCTM_TXTDOMAIN); ?>
	</td>

	
<?php 
//------------------------------------------------------------------------------
// Active Custom Post Types -- include the "deactivate" links
elseif ($is_active): 
//------------------------------------------------------------------------------
?>

			<span class="activate">
				<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=7&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title="<?php _e('Deactivate this content type', CCTM_TXTDOMAIN); ?>"><?php _e('Deactivate', CCTM_TXTDOMAIN); ?></a>
			</span>
			<span class="delete"></span>
		</div>
	</td>
	
	<td class="desc">
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=2&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title=""><?php _e('Edit', CCTM_TXTDOMAIN); ?></a> 
		|
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=4&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('Manage Custom Fields', CCTM_TXTDOMAIN); ?></a>
		| 
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=8&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('View Sample Templates', CCTM_TXTDOMAIN); ?>
	</td>

<?php 
//------------------------------------------------------------------------------
// Inactive Custom Post Types
else: 				
//------------------------------------------------------------------------------
?>
			<span class="activate">
				<a href="?page=<?php print self::admin_menu_slug;?>&<?php print self::action_param; ?>=6&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title="<?php _e('Activate this content type', CCTM_TXTDOMAIN); ?>" class="edit"><?php _e('Activate', CCTM_TXTDOMAIN); ?></a> | 
			</span>
			<span class="delete">
				<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=3&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title="<?php _e('Delete this content type', CCTM_TXTDOMAIN); ?>" class="delete"><?php _e('Delete', CCTM_TXTDOMAIN); ?></a>
			</span>
		</div>
	</td>
	<td class="desc">
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=2&<?php print self::post_type_param; ?>=<?php print $post_type; ?>" title=""><?php _e('Edit', CCTM_TXTDOMAIN); ?></a>
		|
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=4&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('Manage Custom Fields', CCTM_TXTDOMAIN); ?></a>
		| 
		<a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=8&<?php print self::post_type_param; ?>=<?php print $post_type;?>" title=""><?php _e('View Sample Templates', CCTM_TXTDOMAIN); ?>	
	</td>
<?php
//------------------------------------------------------------------------------ 
endif; 
//------------------------------------------------------------------------------
?>
</tr>