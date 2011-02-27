<script>
	
	// var default_menu_icon_checkbox = '[+use_default_menu_icon.value+]';
	
	jQuery(document).ready(function(){
			if( jQuery('#[+use_default_menu_icon.id+]:checked').val() == '1' )
			{
	            jQuery('#menu_icon_container').hide();
	        } 
	        else 
	        {
	            jQuery('#menu_icon_container').show();	
	        }
	});
	
	jQuery(function() {
		jQuery( "#tabs" ).tabs();
	});
	
	function toggle_image_detail()
	{

		if( jQuery('#[+use_default_menu_icon.id+]:checked').val() == '1' )
		{
            jQuery('#menu_icon_container').hide();
            console.log('hiding');
        } 
        else 
        {
            jQuery('#menu_icon_container').show();
        	console.log( jQuery('#[+use_default_menu_icon.id+]:checked').val() );
        	console.log('showing');

        }


/*		jQuery('#'+css_id).slideToggle(400);
		    return false; */

	}
	
	function send_to_menu_icon(src)
	{
		jQuery('#menu_icon').val(src);
	}
	
</script>

<div id="tabs">
	<ul>
		<li><a href="#basic-tab">Basic</a></li>
		<li><a href="#fields-tab">Fields</a></li>
		<li><a href="#menu-tab">Menu</a></li>
		<li><a href="#attributes-tab">Page Attributes</a></li>
		<li><a href="#urls-tab">URLs</a></li>
		<li><a href="#advanced-tab">Advanced</a></li>
	</ul>

	<div style="clear:both;"></div>	
	
	<div id="basic-tab">
		<!--!Post Type -->
		[+post_type+]
		
		<!--!Singular Label -->
		[+singular_label+]
				
		<!--!Plural Label -->
		[+label+]
		
		<!--!Description-->
		<!-- description -->
		<div class="formgenerator_element_wrapper" id="custom_field_wrapper_description">
					
			<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">[+description.label+]</label>
			<textarea name="[+description.name+]" class="formgenerator_textarea" id="description" rows="4" cols="60">[+description.value+]</textarea>
		</div>
		
		
		<!--!Show UI -->
		[+show_ui+]
		
		<!--!Public -->
		[+public+]
		
	</div>
	<!-- ================================================================================================ -->	
	<div id="fields-tab">
		<!--!Supports -->
		
		[+supports_title+]
			
		[+supports_editor+]
		
		[+supports_author+]
					
		[+supports_excerpt+]
		
		[+supports_custom-fields+]

	</div>
	<!-- ================================================================================================ -->
	<div id="menu-tab">
	
		<!--!Menu Position-->
		[+menu_position+]
		
		<!--!Use Default Menu Icon -->
		<!-- use_default_menu_icon -->
		<div class="formgenerator_element_wrapper" id="custom_field_wrapper_use_default_menu_icon">
			<input type="checkbox" name="[+use_default_menu_icon.name+]" class="formgenerator_checkbox" id="use_default_menu_icon" value="[+use_default_menu_icon.checked_value+]" [+use_default_menu_icon.is_checked+] onclick="javascript:toggle_image_detail('menu_icon_container');"/> 
			<label for="use_default_menu_icon" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_use_default_menu_icon">[+use_default_menu_icon.label+]</label>
			<span class="formgenerator_description">[+use_default_menu_icon.description+]</span>
		</div>
		
		<div id="menu_icon_container" style="display: none;">
		
		<!--!Menu Icon -->
			<!-- menu_icon -->
			<div class="formgenerator_element_wrapper" id="custom_field_wrapper_menu_icon">		
				<label for="[+menu_icon.id+]" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_menu_icon">[+menu_icon.label+]</label>
				<input type="text" name="[+menu_icon.name+]" class="formgenerator_text" id="[+menu_icon.id+]" value="[+menu_icon.value+]" size="100"/>
						<span class="formgenerator_description">[+menu_icon.description+]</span>
			</div>
		
			<div style="width:300px">
			[+icons+]
			</div>
		</div>

	</div>
	
	<!-- ================================================================================================ -->
	<div id="attributes-tab">
			
		[+supports_page-attributes+]
		
		<!-- Featured Image -->
		[+supports_thumbnail+]
		
		<!--!Hierarchical -->
		[+hierarchical+]
	</div>
	
	<!-- ================================================================================================ -->
	
	<div id="urls-tab">
		<!--!Rewrite with Front -->
		[+rewrite_with_front+]
		
		[+rewrite+]
		
		<!--!Rewrite Slug -->
		[+rewrite_slug+]
		
		<!--!Permalink Action -->
		[+permalink_action+]
		
		<!--!Query Var -->
		[+query_var+]
	</div>
	
	<!-- ================================================================================================ -->
	<div id="advanced-tab">

		<!-- Capability Type -->
		[+capability_type+]
		
		<!--! Show in Nav Menus -->			
		[+show_in_nav_menus+]
		
		<!--!Can Export -->
		[+can_export+]
		
	
		[+supports_trackbacks+]
		
		[+supports_comments+]
		
		[+supports_revisions+]
	
		<h3>Taxonomies</h3>
		[+taxonomy_categories+]
		
		[+taxonomy_tags+]
	</div>
</div>


