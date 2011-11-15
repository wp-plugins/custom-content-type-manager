/*------------------------------------------------------------------------------
This pops WP's media uploader
http://www.webmaster-source.com/2010/01/08/using-the-wordpress-uploader-in-your-plugin-or-theme/
------------------------------------------------------------------------------*/
function cctm_upload() {
	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	return false;
}

/*------------------------------------------------------------------------------
Overrides the send_to_editor() function in the media-upload script

The 'html' bit has something like this when you click "Insert into Post" 
(but NOT if you click "Save all Changes"):

<a href="http://cctm:8888/sub/?attachment_id=603" rel="attachment wp-att-603"><img src="http://cctm:8888/sub/wp-content/uploads/2011/11/Photo-on-2011-07-14-at-23.01-300x225.jpg" alt="" title="Photo on 2011-07-14 at 23.01" width="300" height="225" class="alignnone size-medium wp-image-603" /></a>
------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
	console.log('ready...');
	window.send_to_editor = function(html) {
		alert(html);
		console.log('here...');
		imgurl = jQuery('img',html).attr('src');
		// jQuery('#upload_image').val(imgurl);
		tb_remove();
	}
});