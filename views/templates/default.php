<?php if ( ! defined('WP_CONTENT_DIR')) exit('No direct script access allowed');
/*------------------------------------------------------------------------------
Template used for CCTM manager pages. This page should be included by the 
controllers with the following variables set:

$data['page_title']:	Header of this Admin page
$data['msg']:			Any message (e.g. after form submission)
$data['menu']:			Navigation links (e.g. back, cancel, etc)
$data['content']:		Main content block
------------------------------------------------------------------------------*/
?>
<div class="wrap">

	<?php /*---------------- HEADER --------------------------- */ ?>	
	<div id="cctm_header">
		<img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="custom-content-type-manager-logo" width="88" height="55" style="float:left; margin-right:20px;"/>
		<p class="cctm_header_text">Custom Content Type Manager <span class="cctm_version">[<?php print CCTM::get_current_version(); ?>]</span><br/>
		<span class="cctm_page_title"><?php print $data['page_title']; ?></span>
		</p>
	</div>
	
	
	
	<?php print $data['msg']; ?>
	
	<div id="cctm_nav"><?php print $data['menu']; ?></div>
	
	<?php print $data['content']; ?>
	
	<?php /*--------------- FOOTER --------------------------*/ ?>
	<div id="cctm_footer">
		<p style="margin:10px;">
			<span class="cctm-link">
				<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FABHDKPU7P6LN" target="_blank"><img class="cctm-img" src="<?php print CCTM_URL; ?>/images/heart.png" height="32" width="32" alt="heart"/>
					<?php _e('Support this Plugin', CCTM_TXTDOMAIN); ?>
				</a>
			</span>
			<span class="cctm-link">
				<a href="?page=cctm&a=help">
					<img class="cctm-img" src="<?php print CCTM_URL; ?>/images/help.png" height="32" width="32" alt="help"/> 
					<?php _e('Help', CCTM_TXTDOMAIN); ?>
				</a>
			</span>
			<span class="cctm-link">
				<a href="?page=cctm&a=bug_report">
					<img class="cctm-img" src="<?php print CCTM_URL; ?>/images/space-invader.png" height="32" width="32" alt="bug"/> 
					<?php _e('Report a Bug', CCTM_TXTDOMAIN); ?></a></span>
			<span class="cctm-link">
				<a href="http://eepurl.com/dlfHg" target="_blank">
					<img class="cctm-img" src="<?php print CCTM_URL; ?>/images/newspaper.png" height="32" width="32" alt="Newsletter"/> 
					<?php _e('Get eMail Updates', CCTM_TXTDOMAIN); ?>
				</a>
			</span>
			<span class="cctm-link">
				<a href="http://wordpress.org/tags/custom-content-type-manager?forum_id=10" target="_blank">
					<img class="cctm-img" src="<?php print CCTM_URL; ?>/images/forum.png" height="32" width="32" alt="forum"/> 			
					<?php _e('Forum', CCTM_TXTDOMAIN); ?>
				</a>
			</span>		
		</p>
	</div>

</div>
