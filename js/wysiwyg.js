/*------------------------------------------------------------------------------
TinyMCE make HTML view
------------------------------------------------------------------------------*/
function show_html_view(id) {
	tinyMCE.execCommand('mceRemoveControl', false, id);
}

/*------------------------------------------------------------------------------
TinyMCE make Rich-Text-Formatted view
------------------------------------------------------------------------------*/
function show_rtf_view(id) {
	tinyMCE.execCommand('mceAddControl', false, id);
}