// Used for various iterations, e.g. id'ing css elements
var i = 0;

/*------------------------------------------------------------------------------
Add a dropdown option
@param	string	target_id: target CSS id
@param	string	delete_label: the translated label for the delete button
@param	integer	local_i: a number representing 
------------------------------------------------------------------------------*/
function append_dropdown_option( target_id, delete_label, local_i )
{
	if (!i) {
		i = local_i;
	}
	my_html = '<div id="cctm_dropdown_option'+i+'"><input type="text" name="options[]" value=""/> <span class="button" onclick="javascript:remove_html(\'cctm_dropdown_option'+i+'\');">'+delete_label+'</span></div>';
	jQuery('#'+target_id).append(my_html);
	i = i + 1;
}

/*------------------------------------------------------------------------------
Remove the HTML identified by the target_id
------------------------------------------------------------------------------*/
function remove_html( target_id )
{
	console.log(target_id);
	jQuery('#'+target_id).remove();	
}
	
