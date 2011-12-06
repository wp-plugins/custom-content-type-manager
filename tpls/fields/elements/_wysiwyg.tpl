<script type="text/javascript">
	jQuery( document ).ready( function() {
		jQuery("[+id+]").addClass( "mceEditor" );
//		if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
			tinyMCE.execCommand( "mceAddControl", false, "[+id+]" );
//		}

		edCanvas = document.getElementById("[+id+]");

	});
</script>		
<p align="right">
  <a class="button" onclick="javascript:show_rtf_view('[+id+]');">Visual</a>
  <a class="button" onclick="javascript:show_html_view('[+id+]');">HTML</a>
</p>

<textarea name="[+name+]" class="[+class+]" id="[+id+]" [+extra+]>[+value+]</textarea>