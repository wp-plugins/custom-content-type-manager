/*------------------------------------------------------------------------------
Generic function. Remove the HTML identified by the target_id
@param	string	target_id -- CSS id of the item to be removed.
------------------------------------------------------------------------------*/
function remove_html( target_id ) {
	jQuery('#'+target_id).remove();
}

