<?php

$back_link = sprintf(
			'?page=%s&%s=4&%s=%s'
			, self::admin_menu_slug
			, self::action_param
			, self::post_type_param
			, $post_type
		);
?>

<form id="custom_post_type_manager_basic_form" method="post" action="<?php print $action_link;?>">

<div class="wrap">
	<h2>
	<a href="?page=<?php print self::admin_menu_slug;?>" title="<?php _e('Back'); ?>"><img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" /></a>
	<?php print $post_type; ?> : <?php print $heading; ?> <a href="<?php print $back_link; ?>" class="button"><?php _e('Show all Fields', CCTM_TXTDOMAIN); ?></a></h2>

	<?php print $msg; ?>

	<?php print $icon; ?>
	
	<?php wp_nonce_field($action_name, $nonce_name); ?>
	
	<?php print $fields; ?>
		
	<input type="submit" class="button-primary" value="<?php _e('Save', CCTM_TXTDOMAIN ); ?>" /> <a href="<?php print $back_link; ?>" class="button"><?php _e('Cancel', CCTM_TXTDOMAIN); ?></a>

</form>

</div>