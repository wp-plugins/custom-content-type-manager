<li>
	<img class="cctm_tiny_thumb" src="<?php print $data['thumbnail_src']; ?>" height="30" width="30" alt="" onclick="javascript:select_post('<?php print $data['field_id']; ?>','<?php print $data['ID']; ?>','');"/> 
	<?php print $data['post_title']; ?> (<?php print $data['ID']; ?>)
	
	<a href="<?php print $data['guid']; ?>" target="_new"><?php _e('Preview', CCTM_TXTDOMAIN); ?></a>
</li>