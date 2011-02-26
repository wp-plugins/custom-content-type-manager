<div class="wrap">
	<h2>
	<img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" /> 
	Custom Content Type Manager <a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=1" class="button add-new-h2"><?php _e('Add New Content Type'); ?></a></h2>

	<?php print $msg; ?>

	<p>
		<span class="cctm-link"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/heart.png" height="32" width="34" alt="bug"/><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=355ULXBFXYL8N"><?php _e('Support this Plugin', CCTM_TXTDOMAIN); ?></a></span>
		<span class="cctm-link"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/potion.png" height="31" width="22" alt="bug"/><a href="http://code.google.com/p/wordpress-custom-content-type-manager/"><?php _e('Documentation', CCTM_TXTDOMAIN); ?></a></span>
		<span class="cctm-link"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/space-invader.png" height="32" width="32" alt="bug"/> <a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/list"><?php _e('Report a Bug', CCTM_TXTDOMAIN); ?></a></span>
	</p>

	<table class="wp-list-table widefat plugins" cellspacing="0" id="all-plugins-table">
		<thead>
			<tr>
				<th scope="col" class="manage-column">Content Type (post type)</th>
				<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th scope="col" class="manage-column">Content Type (post type)</th>
				<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
			</tr>
		</tfoot>
	
		<tbody id="the-list" class="plugins">

		<?php print $row_data; ?>
		
		</tbody>
	</table>

</div>