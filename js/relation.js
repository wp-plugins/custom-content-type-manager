// Global storage for the fieldname we're uploading a file for.
var cctm_fieldname;
var append_or_replace = 'append';

/*------------------------------------------------------------------------------
This pops WP's media uploader
http://www.webmaster-source.com/2010/01/08/using-the-wordpress-uploader-in-your-plugin-or-theme/

TODO:

Hide the "Save All Changes" button via CSS???
<p class="ml-submit">
<input id="save" class="button savebutton" type="submit" value="Save all changes" name="save">
<input id="post_id" type="hidden" value="0" name="post_id">
</p>
TODO: Normally, the target of the thickbox should be 'media-upload.php?type=image&amp;TB_iframe=true'
but we'll have to abstract that in order to filter the tabs.
------------------------------------------------------------------------------*/
function cctm_upload(fieldname, upload_type) {
	cctm_fieldname = fieldname; // pass this to global scope
	append_or_replace = upload_type; // pass this to global scope
	
	tb_show('', 'media-upload.php?post_id=0&amp;type=file&amp;TB_iframe=true');
	return false;
}

/*------------------------------------------------------------------------------
Overrides the send_to_editor() function in the media-upload script

The 'html' bit has something like this when you click "Insert into Post" 
(but NOT if you click "Save all Changes"):

<a href="http://cctm:8888/sub/?attachment_id=603" rel="attachment wp-att-603"><img src="http://cctm:8888/sub/wp-content/uploads/2011/11/Photo-on-2011-07-14-at-23.01-300x225.jpg" alt="" title="Photo on 2011-07-14 at 23.01" width="300" height="225" class="alignnone size-medium wp-image-603" /></a>
------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
	// Override WP's "Insert into Post" function: we want our own preview html for this.
	window.send_to_editor = function(html) {
	
		var attachment_id; 
		
		var matches = html.match(/attachment_id=(\d+)/);
		if (matches != null) {
    		attachment_id = matches[1];
    	}
    	
		var data = {
		        "action" : 'get_selected_posts',
		        "fieldname" : cctm_fieldname, // Read from global scope
		        "post_id": attachment_id,
		        "get_selected_posts_nonce" : cctm.ajax_nonce
		    };
	
		jQuery.post(
		    cctm.ajax_url,
		    data,
		    function( response ) {
		    	// Write the response to the div
		    	if (append_or_replace == 'append') {
			    	jQuery('#cctm_instance_wrapper_'+cctm_fieldname).append(response);
		    	}
		    	else {
		    		jQuery('#cctm_instance_wrapper_'+cctm_fieldname).html(response);
		    	}
				
		    }
		);
		
		tb_remove();
	}
});