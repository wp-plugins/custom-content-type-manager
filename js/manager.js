/*------------------------------------------------------------------------------
Note that the incrementor cctm[fieldname] is set in wrapper/_text_multi.tpl
@param	string fieldname is the CSS ID of the field we're adding to.
------------------------------------------------------------------------------*/
function add_field_instance(fieldname) {
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

/*------------------------------------------------------------------------------
Generic function. Remove the HTML identified by the target_id
@param	string	target_id -- CSS id of the item to be removed.
------------------------------------------------------------------------------*/
function remove_html( target_id ) {
	jQuery('#'+target_id).remove();
	jQuery('#default_value').val(''); // <-- used in the field definitions
}

/*------------------------------------------------------------------------------
Remove all selected posts from the repeatable field
@param	string	CSS field id, e.g. cctm_myimage
------------------------------------------------------------------------------*/
function remove_all_relations(field_id) {
	jQuery('#cctm_instance_wrapper_'+field_id).html('');
}