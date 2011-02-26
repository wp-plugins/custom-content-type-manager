See: http://jqueryui.com/demos/sortable/
http://stackoverflow.com/questions/2509801/jquery-connected-sortable-lists-save-order-to-mysql
http://forum.jquery.com/topic/save-sortable-li-list-order-to-database
http://stackoverflow.com/questions/2529269/forcing-an-item-to-remain-in-place-in-a-jquery-ui-sortable-list
http://stackoverflow.com/questions/4928002/jquery-sortable-set-item-to-an-index-programmatically

5 - below Posts
10 - below Media
15 - below Links
20 - below Pages
25 - below comments
60 - below first separator
65 - below Plugins
70 - below Users
75 - below Tools
80 - below Settings
100 - below second separator

Put these into the enqueue 
			<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script -->
			<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js" type="text/javascript"></script>
			<script src="http://jquery-ui.googlecode.com/svn/tags/latest/external/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>
			<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>

<script>
	var i=0;
	function save_order()
	{
		jQuery(".store_me").each(function(){
	        jQuery(this).toggleClass("example");
	        jQuery(this).val(i);
			i=i+1;
      	});
	}
</script>


<style>
	#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
	#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
	#sortable li span { position: absolute; margin-left: -1.3em; }
</style>

<script>
jQuery(function() {
	jQuery( "#sortable" ).sortable();
	jQuery( "#sortable" ).disableSelection();
	jQuery( "#the-list2" ).sortable();
	jQuery( "#the-list2" ).disableSelection();

	
});
</script>



<ul id="sortable">
	<li class="ui-state-default"><input id="o1" name="sort[]" type="text" class="store_me" /><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 1</li>
	<li class="ui-state-default"><input id="o2" name="sort[]" type="text" class="store_me" /><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 2</li>
	<li class="ui-state-default"><input id="o3" name="sort[]" type="text" class="store_me" /><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 3</li>
	<li class="ui-state-default"><input id="o4" name="sort[]" type="text" class="store_me" /><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 4</li>
	<li class="ui-state-default"><input id="o5" name="sort[]" type="text" class="store_me" /><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 5</li>
</ul>
<hr/>
<span onclick="javascript:save_order();">Save Order</span>

<hr/>

<hr/>

<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope='col' id='name' class='manage-column column-name'  style="">Plugin</th>
		<th scope='col' id='description' class='manage-column column-description'  style="">Description</th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope='col'  class='manage-column column-name'  style="">Plugin</th>
		<th scope='col'  class='manage-column column-description'  style="">Description</th>	
	</tr>
</tfoot>

<tbody id="the-list2">
	<tr id='akismet' class='inactive'>
		<td class='plugin-title'>
			<strong>Akismet</strong>
			<div class="row-actions-visible"><span class='activate'><a href="" title="Activate this plugin" class="edit">Activate</a> | </span><span class='delete'><a href="" title="Delete this plugin" class="delete">Delete</a></span></div>
		</td>
		<td class='column-description desc'>
			<div class='plugin-description'><p>Used by millions, Akismet is quite possibly the best way in the world to <strong>protect your blog from comment and trackback spam</strong>. It keeps your site protected from spam even while you sleep. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://akismet.com/get/?return=true">Sign up for an Akismet API key</a>, and 3) Go to your <a href="">Akismet configuration</a> page, and save your API key.</p>
			</div>
			<div class='inactive second plugin-version-author-uri'>
				Version 2.5.3 | By <a href="" title="">Automattic</a> | <a href="http://akismet.com/" title="Visit plugin site">Visit plugin site</a>
			</div>
		</td>
	</tr>
	
	<tr id='myseparator' style='background-color:black;'>
		<td colspan="2">
			First Separator
		</td>
	</tr>

	<tr id='custom-content-type-manager' class='active'>
		<td class='plugin-title'><strong>Custom Content Type Manager</strong>
			<div class="row-actions-visible"><span class='0'><a href="">Settings</a> | </span><span class='deactivate'><a href="" title="">Deactivate</a></span></div>
		</td>
		<td class='column-description desc'>
			<div class='plugin-description'><p>Allows users to create custom content types (also known as post types) and standardize custom fields for each content type, including dropdowns, checkboxes, and images. This gives WordPress CMS functionality making it easier to use WP for eCommerce or content-driven sites.</p></div>
			<div class='active second plugin-version-author-uri'>Version 0.8.7 | By <a href="http://www.fireproofsocks.com/" title="Visit author homepage">Everett Griffiths</a> | <a href="http://tipsfor.us/plugins/custom-content-type-manager/" title="Visit plugin site">Visit plugin site</a></div>
		</td>
	</tr>
	
</tbody>
</table>