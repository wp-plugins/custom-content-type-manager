<?php
/*------------------------------------------------------------------------------
Previously, I was using the FormGenerator class to dogfood the generation of 
the Create/Edit "custom post type" form, but it's too much of a pain in the 
ass to customize the auto-generated forms.  The FormGenerator does not allow
for the degree of customization required for this form: the register_post_type()
function expects some really quirky input and LARGE, so here I am creating 
a "traditional" PHP/HTML form.

This page is intended to generate only the form elements so they can be 
piped into the basic_form.php page.
------------------------------------------------------------------------------*/
?>
<div class="formgenerator_element_wrapper" id="custom_field_number_0">			
	<p><strong>post_type:</strong> store</p>
	<input type="hidden" name="post_type" class="formgenerator_readonly" id="post_type" value="store"/>
	<span class="formgenerator_description">The name of the post-type cannot be changed. The name may show up in your URLs, e.g. ?movie=star-wars. This will also make a new theme file available, starting with prefix named "single-", e.g. <strong>single-movie.php</strong>.</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_1">			
	<label for="singular_label" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_singular_label">Singular Label</label>
	<input type="text" name="singular_label" class="formgenerator_text" id="singular_label" value="Store"/>
	<span class="formgenerator_description">Human readable single instance of this content type, e.g. "Post"</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_2">
	<label for="label" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_label">Menu Label (Plural)</label>
	<input type="text" name="label" class="formgenerator_text" id="label" value="Stores"/>
	<span class="formgenerator_description">Plural name used in the admin menu, e.g. "Posts"</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_3">			
	<label for="description" class="formgenerator_label formgenerator_textarea_label" id="formgenerator_label_description">Description</label>
	<textarea name="description" class="formgenerator_textarea" id="description" >Rolodex of Store locations</textarea>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_4">			
	<input type="checkbox" name="show_ui" class="formgenerator_checkbox" id="show_ui" value="1" checked="checked" /> 
	<label for="show_ui" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_show_ui">Show Admin User Interface</label>
	<span class="formgenerator_description">Should this post type be visible on the back-end?</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_5">			
	<label for="capability_type" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_capability_type">Capability Type</label>
	<input type="text" name="capability_type" class="formgenerator_text" id="capability_type" value="post"/>
	<span class="formgenerator_description">The post type to use for checking read, edit, and delete capabilities. Default: "post"</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_6">			
	<input type="checkbox" name="public" class="formgenerator_checkbox" id="public" value="1" checked="checked" /> 
	<label for="public" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_public">Public</label>
	<span class="formgenerator_description">Should these posts be visible on the front-end?</span>
</div>
		
<div class="formgenerator_element_wrapper" id="custom_field_number_7">
	<input type="checkbox" name="hierarchical" class="formgenerator_checkbox" id="hierarchical" value="1" checked="checked" /> 
	<label for="hierarchical" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_hierarchical">Hierarchical</label>
	<span class="formgenerator_description">Allows parent to be specified (Page Attributes should be checked)</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_8">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_title" value="title" checked="checked" /> 
	<label for="supports_title" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Title</label>
	<span class="formgenerator_description">Post Title</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_9">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_editor" value="editor"  /> 
	<label for="supports_editor" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Content</label>
	<span class="formgenerator_description">Main content block.</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_10">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_author" value="author"  /> 
	<label for="supports_author" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Author</label>
	<span class="formgenerator_description">Track the author.</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_11">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_thumbnail" value="thumbnail"  /> 
	<label for="supports_thumbnail" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Thumbnail</label>
	<span class="formgenerator_description">Featured image (the activetheme must also support post-thumbnails)</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_12">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_excerpt" value="excerpt"  /> 
	<label for="supports_excerpt" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Excerpt</label>
	<span class="formgenerator_description">Small summary field.</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_13">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_trackbacks" value="trackbacks"  /> 
	<label for="supports_trackbacks" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Trackbacks</label>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_14">
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_custom-fields" value="custom-fields"  /> 
	<label for="supports_custom-fields" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Supports Custom Fields</label>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_15">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_comments" value="comments"  /> 
	<label for="supports_comments" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Enable Comments</label>
</div>
		
<div class="formgenerator_element_wrapper" id="custom_field_number_16">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_revisions" value="revisions" checked="checked" /> 
	<label for="supports_revisions" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Store Revisions</label>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_17">			
	<input type="checkbox" name="supports[]" class="formgenerator_checkbox" id="supports_page-attributes" value="page-attributes"  /> 
	<label for="supports_page-attributes" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_supports[]">Enable Page Attributes</label>
	<span class="formgenerator_description">(template and menu order; hierarchical must be checked)</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_18">			
	<label for="menu_position" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_menu_position">Menu Position</label>
	<input type="text" name="menu_position" class="formgenerator_text" id="menu_position" value=""/>
	<span class="formgenerator_description">This setting determines where this post type should appear in the left-hand admin menu. Default: null (below Comments) 
		<ul style="margin-left:40px;">
			<li><strong>5</strong> - below Posts</li>
			<li><strong>10</strong> - below Media</li>
			<li><strong>20</strong> - below Posts</li>
			<li><strong>60</strong> - below Pages</li>
			<li><strong>100</strong> - below first separator</li>
		</ul>
	</span>
</div>
	
<div class="formgenerator_element_wrapper" id="custom_field_number_19">		
	<label for="menu_icon" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_menu_icon">Menu Icon</label>
	<input type="text" name="menu_icon" class="formgenerator_text" id="menu_icon" value=""/>
	<span class="formgenerator_description">Menu icon URL.</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_20">			
	<input type="checkbox" name="use_default_menu_icon" class="formgenerator_checkbox" id="use_default_menu_icon" value="1" checked="checked" /> 
	<label for="use_default_menu_icon" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_use_default_menu_icon">Use Default Menu Icon</label>
	<span class="formgenerator_description">If checked, your post type will use the posts icon</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_21">	
	<input type="checkbox" name="rewrite_with_front" class="formgenerator_checkbox" id="rewrite_with_front" value="1"  /> 
	<label for="rewrite_with_front" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_rewrite_with_front">Rewrite with Permalink Front</label>
			<span class="formgenerator_description">Allow permalinks to be prepended with front base - defaults to checked</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_22">			
	<label for="rewrite_slug" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_rewrite_slug">Rewrite Slug</label>
	<input type="text" name="rewrite_slug" class="formgenerator_text" id="rewrite_slug" value=""/>
	<span class="formgenerator_description">Prepend posts with this slug - defaults to post type's name</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_23">			
	<label for="permalink_action" class="formgenerator_label" id="formgenerator_label_permalink_action">Permalink Action</label>
		<select name="permalink_action" class="formgenerator_dropdown formgenerator_dropdown_label" id="permalink_action">
			<option value="Off" >Off</option><option value="/%postname%/" selected="selected">/%postname%/</option><option value="Custom" >Custom</option>  
		</select>
		
	<span class="formgenerator_description">Use permalink rewrites for this post_type? Default: Off
		<ul style="margin-left:20px;">
			<li><strong>Off</strong> - URLs for custom post_types will always look like: http://site.com/?post_type=book&p=39 even if the rest of the site is using a different permalink structure.</li>
			<li><strong>/ostname</strong> - You MUST use the custom permalink structure: "/%postname%/". Other formats are <strong>not</strong> supported.  Your URLs will look like http://site.com/movie/star-wars/</li>
			<li><strong>Custom</strong> - Evaluate the contents of slug</li>
		<ul>
	</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_24">			
	<label for="query_var" class="formgenerator_label formgenerator_text_label" id="formgenerator_label_query_var">Query Variable</label>
	<input type="text" name="query_var" class="formgenerator_text" id="query_var" value=""/>
	<span class="formgenerator_description">(optional) Name of the query var to use for this post type.
	E.g. "movie" would make for URLs like http://site.com/?movie=star-wars. 
	If blank, the default structure is http://site.com/?post_type=movie&p=18</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_25">			
	<input type="checkbox" name="show_in_nav_menus" class="formgenerator_checkbox" id="show_in_nav_menus" value="1" checked="checked" /> 
	<label for="show_in_nav_menus" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_show_in_nav_menus">Show in Nav Menus</label>
	<span class="formgenerator_description">Whether post_type is available for selection in navigation menus. Default: value of public argument</span>
</div>

<div class="formgenerator_element_wrapper" id="custom_field_number_26">			
	<input type="checkbox" name="can_export" class="formgenerator_checkbox" id="can_export" value="1" checked="checked" /> 
	<label for="can_export" class="formgenerator_label formgenerator_checkbox_label" id="formgenerator_label_can_export">Can Export</label>
	<span class="formgenerator_description">Can this post_type be exported.</span>
</div>	

<input type="hidden" id="custom_content_type_mgr_edit_content_type_nonce" name="custom_content_type_mgr_edit_content_type_nonce" value="c64e4f22ad" /><input type="hidden" name="_wp_http_referer" value="/blog/wp-admin/options-general.php?page=custom_content_type_mgr&amp;a=2&amp;pt=store" />

<div class="custom_content_type_mgr_form_controls">
	<input type="submit" name="Submit" class="button-primary" value="Save" />
	<a class="button" href="?page=custom_content_type_mgr">Cancel</a> 
</div>