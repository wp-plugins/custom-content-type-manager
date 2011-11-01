<label for="[+id_prefix+][+search_term.id+]" class="[+label_class+]" id="[+search_term.id+]_label">[+search_term.label+]</label>
<input class="[+input_class+] input_field" type="text" name="[+name_prefix+][+search_term.id+]" id="[+id_prefix+][+search_term.id+]" value="[+search_term.value+]" />


<label for="[+id_prefix+][+yearmonth.id+]" class="[+label_class+]" id="[+id+]_label">[+yearmonth.label+]</label>

<select size="[+yearmonth.size+]" name="[+name_prefix+][+yearmonth.name+]" class="[+input_class+]" id="[+id_prefix+][+yearmonth.id+]">
	<option value="">[+show_all_dates+]</option>
	[+yearmonth.options+]
</select>

[+post_type+]

<span class="button" onclick="javascript:refine_search();">[+search+]</span>
<br/>
<span class="button" onclick="javascript:reset_search();">Show All</span>
