<?php
/*------------------------------------------------------------------------------
This template is used as the basis for thickboxes launched by relation fields.
The form id is important: select_posts_form.  That id is referenced by several
javscript functions, so it should not be changed.

We rely on only ONE dedicated hidden field: the one for the fieldname.
All other search parameters (offset, limit, search_term, etc) are stored 
via http_build_query() inside of the search_parameters field.
------------------------------------------------------------------------------*/
?>
<div id="cctm_thickbox">
	<form id="select_posts_form">
		<input type="hidden" name="fieldname" id="fieldname" value="<?php print $data['fieldname']; ?>" />
		<input type="hidden" name="page_number" id="page_number" value="<?php print $data['page_number']; ?>" />
		<input type="hidden" name="orderby" id="orderby" value="<?php print $data['orderby']; ?>" />
		<input type="hidden" name="order" id="order" value="<?php print $data['order']; ?>" />

	<?php
/*
	
		
		<?php
			$excludes = CCTM::get_value($data, 'exclude', array());
			foreach ($excludes as $e) {
				print '<input type="hidden" name="exclude[]" id="exclude'.$e.'" value="'.$e.'" />';
			}
		?>
	
*/	
	?>	
		<input type="hidden" name="search_parameters" id="search_parameters" value="<?php print $data['search_parameters']; ?>" />
		
		<div id="cctm_thickbox_menu">
			<?php print $data['menu']; ?>	
		</div>
	
		<div id="cctm_search_posts_form">
			<?php print $data['search_form']; ?>	
		</div>
	
		<div id="cctm_thickbox_content">
			<?php print $data['content']; ?>
		</div>
	</div>
</div>