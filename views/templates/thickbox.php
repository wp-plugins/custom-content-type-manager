<?php
/*------------------------------------------------------------------------------
This template is used as the basis for thickboxes launched by relation fields.
The form id is important: select_posts_form.  That id is referenced by several
javscript functions, so it should not be changed.
------------------------------------------------------------------------------*/
?>
<div id="cctm_thickbox">
	<form id="select_posts_form">
		<input type="hidden" name="fieldname" id="fieldname" value="<?php print $data['fieldname']; ?>" />
		<input type="hidden" name="page_number" id="page_number" value="<?php print $data['page_number']; ?>" />
		<input type="hidden" name="orderby" id="orderby" value="<?php print $data['orderby']; ?>" />
		<input type="hidden" name="order" id="order" value="<?php print $data['order']; ?>" />
		
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