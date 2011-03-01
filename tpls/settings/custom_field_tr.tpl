<!-- 
Placeholders are mostly:

    [2] => Array
        (
            [label] => Product Image Thumbnail
            [name] => img_thumb
            [description] => 
            [type] => image
            [default_value] => 
            [sort_param] => 1
        )
 
-->
<tr id="cctm_custom_field_[+name+]" class="active">
	<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
	<td class='plugin-title'>
		[+icon+]
		<strong>[+label+]</strong> ([+name+])
	</td>
	<td class="column-description desc">
		<div class='plugin-description'><p>[+description+]</p></div>
		<div class='active second plugin-version-author-uri'>
			<input id="o1" name="[+name+][sort_param]" type="text" class="store_me" />
			<a href="#" title="Edit this field">Edit</a> | <a href="#" title="Delete this field">Delete</a></div>

	</td>
</tr>