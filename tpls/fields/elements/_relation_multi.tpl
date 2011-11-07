<span class="cctm_relation" id="cctm_post_[+post_id+]">
	
	<input type="hidden" name="[+name_prefix+][+name+][]" id="[+id_prefix+][+id+][+post_id+]" value="[+post_id+]"/>
	<table>
		<tr>
			<td>
				<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
				<a href="[+preview_url+]" target="_blank" title="[+preview+]">[+img_thumbnail+]</a>				
			</td>
			<td>
				<p>[+post_title+] <span class="cctm_id_label">([+post_id+])</span>
				<span class="cctm_close_rollover" onclick="javascript:remove_html('cctm_post_[+post_id+]');"></span><br/>
				[+post_date+]</p>
			</td>
		</tr>
	</table>
</span>