<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Custom Content Type Manager <a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=1" class="button add-new-h2"><?php _e('Add New Content Type'); ?></a></h2>

	<?php print $msg; ?>

	<p>
		<a class="cctm_helper_link" target="new" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=355ULXBFXYL8N"><?php _e('Support this Plugin', CCTM_TXTDOMAIN); ?></a>
		<a class="cctm_helper_link" target="new" href="http://projects.fireproofsocks.com/projects/cctm/wiki"><?php _e('Documentation', CCTM_TXTDOMAIN); ?></a>
		<a class="cctm_helper_link" target="new" href="http://projects.fireproofsocks.com/projects/cctm/issues/new"><?php _e('Report a Bug', CCTM_TXTDOMAIN); ?></a>
	</p>

	<table class="widefat" cellspacing="0" id="all-plugins-table">
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
	
		<tbody class="plugins">

		<?php print $row_data; ?>
		
		</tbody>
	</table>

</div>