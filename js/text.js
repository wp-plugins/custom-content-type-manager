/*------------------------------------------------------------------------------
Note that the incrementor cctm[fieldname] is set in wrapper/_text_multi.tpl
@param	string fieldname is the CSS ID of the field we're adding to.
------------------------------------------------------------------------------*/
function add_textfield_instance(fieldname) {
	// Increment the instance
	cctm[fieldname] = cctm[fieldname] + 1;
	
	var data = {
	        "action" : 'get_tpl',
	        "fieldname" : fieldname,
	        "instance" : cctm[fieldname],
	        "get_tpl_nonce" : cctm.ajax_nonce
	    };

	jQuery.post(
	    cctm.ajax_url,
	    data,
	    function( response ) {
	    	//alert('cctm_instance_wrapper_'+fieldname);
	    	// Write the response to the div
			jQuery('#cctm_instance_wrapper_'+fieldname).append(response);
	    }
	);
	
	return false;
}