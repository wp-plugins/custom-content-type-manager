/*------------------------------------------------------------------------------
 Include this file to fire off an Ajax request to download a CCTM file.
------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
	jQuery.post(
	    cctm.ajax_url,
	    {
	        action : 'download_def',
	        download_def_nonce : cctm.ajax_nonce
	    },
	    function( response ) { }
	);
});
