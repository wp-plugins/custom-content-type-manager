/*------------------------------------------------------------------------------
This is called by the Widget button click.
------------------------------------------------------------------------------*/
function widget_summarize_posts() {
	// Make us a place for the thickbox
	jQuery('body').append('<div id="summarize_posts_thickbox"></div>');
	
	// Prepare the AJAX query
	var data = {
	        "action" : 'summarize_posts_widget',
	        "summarize_posts_widget_nonce" : cctm.ajax_nonce
	    };
	    
	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	
	    	// Write the response to the div -- TODO this for the widget
			jQuery('#summarize_posts_thickbox').html(response);

			var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
			W = W - 80;
			H = H - 114; // 84?
			// then thickbox the div
			tb_show('', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=summarize_posts_thickbox' );			
	    }
	);
	
}

/*------------------------------------------------------------------------------

------------------------------------------------------------------------------*/
function save_widget_criteria(form_id) {
	alert('here...');
	tb_remove();
}