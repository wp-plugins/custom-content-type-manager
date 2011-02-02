<html>
<head>
	<title>Ajax Post Selector</title>
	<!-- 
	This is loaded via a thickbox iFrame from the WP manager when a post-selection field is generated
	It is parsed by the PostSelector.php class
	-->
	<script type="text/javascript" src="../../../../wp-includes/js/jquery/jquery.js"></script>

	<!-- script src="[+cctm_url+]/uploader/fileuploader.js" type="text/javascript"></script>
	<link href="[+cctm_url+]/uploader/fileuploader.css" rel="stylesheet" type="text/css" -->

	<style>
		#media-upload-header {
			display: none;
		}
	</style>
</head>
<body>
<!-- Global variables, used by search_media JS function for persistent storage -->
<input type="hidden" id="post_selector_page" value="[+page+]" />
	<!-- Safari seems to need the CSS and JS inside the body when loaded via WP. Standalone, it works fine. -->
	<style>	
		[+media_selector_css+]
	</style>
	<script type="text/javascript">
		/*------------------------------------------------------------------------------
		Adds Upload form
		------------------------------------------------------------------------------*/
		function add_upload_form()
		{
			jQuery.get("[+cctm_url+]/upload_form.php","", write_results_to_page);
			//write_results_to_page('xxxxxx');
		}
	
		/*------------------------------------------------------------------------------
		
		------------------------------------------------------------------------------*/
		function change_page(new_page)
		{
			jQuery("#post_selector_page").val(new_page);
			search_media("[+default_mime_type+]");
		}
	
		/*------------------------------------------------------------------------------
		Clears the search form
		------------------------------------------------------------------------------*/
		function clear_search()
		{	
			console.log('clear search was clicked...');
			jQuery("#media_search_term").val(''); 
			search_media("[+default_mime_type+]");
		}

		/*------------------------------------------------------------------------------
		Handle uploading the file.
		------------------------------------------------------------------------------*/
		function handle_upload()
		{
			var the_file = jQuery("#async-upload").val();
			jQuery.post("[+cctm_url+]/upload_form_handler.php",{"async-upload":the_file}, write_results_to_page);
		}
		
		/*------------------------------------------------------------------------------
		Main AJAX function to kick off the query.
		------------------------------------------------------------------------------*/
		function new_media()
		{
			// jQuery.get("media-upload.php", { "flash":"0","inline":"false"}, write_results_to_page);
			// jQuery.get("media-upload.php","", write_results_to_page);
			jQuery.get("[+cctm_url+]/upload.php","", write_results_to_page);
			
		}

		/*------------------------------------------------------------------------------
		Main AJAX function to kick off the query.
		------------------------------------------------------------------------------*/
		function search_media(mime_type)
		{
			var search_term = jQuery("#media_search_term").val();
			var yyyymm = jQuery("#m").val();
			var page = jQuery("#post_selector_page").val();
			jQuery.get("[+ajax_controller_url+]", { "mode":"query", "s":search_term,"fieldname":"[+fieldname+]","post_mime_type":mime_type,"m":yyyymm,"page":page,"post_type":"[+post_type+]" }, write_results_to_page);
			console.log('[+fieldname+]');
		}
	
		/*------------------------------------------------------------------------------
		Where the magic happens: this sends our selection back to WordPress
		------------------------------------------------------------------------------*/
		function send_back_to_wp( post_id, thumbnail_html )
		{
			jQuery('#[+fieldname+]').val(post_id);
			jQuery('#[+fieldname+]_media').html(thumbnail_html);
			tb_remove();
			return false;
		}

		/*------------------------------------------------------------------------------
		Show / Hide 
		------------------------------------------------------------------------------*/
		function toggle_image_detail(css_id)
		{
			jQuery('#'+css_id).slideToggle(400);
	    	return false;
		}


		/*------------------------------------------------------------------------------
		SYNOPSIS: 
			Write the incoming data to the page. 
		INPUT: 
			data = the html to write to the page
			status = an HTTP code to designate 200 OK or 404 Not Found
			xhr = object
		OUTPUT: 
			Writes HTML data to the 'ajax_search_results_go_here' id.
		------------------------------------------------------------------------------*/
		function write_results_to_page(data,status, xhr) 
		{
			if (status == "error") {
				var msg = "Sorry but there was an error: ";
		    	console.error(msg + xhr.status + " " + xhr.statusText);
			}
			else
			{
				jQuery('#ajax_search_results_go_here').html(data);
			}
		}		
		
	</script>

<div id="[+fieldname+]_post_selector_wrapper">
	<p id="media-search-term-box" class="search-box">
		<input type="text" id="media_search_term" name="s" value="" />
		<span class="button" onclick="javascript:search_media('[+default_mime_type+]');">[+search_label+]</span>
		<span class="button" onclick="javascript:clear_search();">[+clear_label+]</span>
	</p>
	
	<h3>Narrow Results</h3>
	<ul class="subsubsub">
		[+post_mime_type_options+]
	</ul>

	
	<div class="tablenav">			
		<div class="alignleft actions">
			<select name="m" onchange="javascript:search_media('[+default_mime_type+]');">
				[+date_options+]
			</select>
			
			<!-- Work in progress here... -->
			<span class="button" onclick="javascript:add_upload_form()">Add New Image</span>
			
		</div>	
	</div>




</div>
<br class="clear" />

<div id="ajax_search_results_go_here">[+default_results+]</div>

</body>
</html>