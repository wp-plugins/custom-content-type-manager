<script type="text/javascript">
	jQuery( document ).ready( function() {
		jQuery("[+name+]").addClass( "mceEditor" );
		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
			tinyMCE.execCommand( "mceAddControl", false, "[+name+]" );
		}
	});
</script>		
<p align="right">
  <a class="button" onclick="javascript:show_rtf_view('[+id+]');">Visual</a>
  <a class="button" onclick="javascript:show_html_view('[+id+]');">HTML</a>
</p>

<textarea name="[+name+]" class="[+class+]" id="[+id+]" %s>[+value+]</textarea>