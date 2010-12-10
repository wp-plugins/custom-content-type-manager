<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Custom Content Type Manager <a href="?page=<?php print self::admin_menu_slug; ?>&<?php print self::action_param; ?>=1" class="button add-new-h2"><?php _e('Add New Content Type'); ?></a></h2>

	<?php print $msg; ?>
	
	<table class="widefat" cellspacing="0" id="all-plugins-table">
		<thead>
			<tr>
				<th scope="col" class="manage-column">Content Type (post type)</th>
				<th scope="col" class="manage-column">Description</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th scope="col" class="manage-column">Content Type (post type)</th>
				<th scope="col" class="manage-column">Description</th>
			</tr>
		</tfoot>
	
		<tbody class="plugins">

		<?php print $row_data; ?>
		
		</tbody>
	</table>
</div>