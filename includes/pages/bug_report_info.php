<div class="wrap">
	<h2>
		<a href="?page=<?php print self::admin_menu_slug;?>" title="<?php _e('Back'); ?>"><img src="<?php print CCTM_URL; ?>/images/cctm-logo.jpg" alt="summarize-posts-logo" width="88" height="55" /></a> 
	Custom Content Type Manager <a href="?page=<?php print self::admin_menu_slug; ?>" class="button add-new-h2"><?php _e('Back'); ?></a></h2>

	<p>I am sorry that you are having trouble with this plugin, but thank you for taking the time to file a bug. It helps out everybody who uses this code, so your input is valuable!</p>
	<div class="error">
			<img src="<?php print CCTM_URL; ?>/images/warning-icon.png" width="50" height="44" style="float:left; padding:10px;"/>
			<p><strong>Remember:</strong> as stated in the <a href="http://wordpress.org/extend/plugins/custom-content-type-manager/">readme.txt</a> file, this plugin is still in development. It will not be considered stable until version 1.0. The only way I can guarantee that something will work for you is if you <a href="http://fireproofsocks.com/contact/">hire me</a>.</p>
			<p>&nbsp;</p>
	</div>
	<p>When reporting bugs, remember the following key points:</p>

	<ol>
		<li><strong>If the bug can't be reproduced, it can't be fixed.</strong> Provide <em>detailed</em> instructions so that someone else can make the plugin fail for themselves.</li>
		<li><strong>Be ready to provide extra information if the programmer needs it.</strong> If they didn't need it, they wouldn't be asking for it.</li>
		<li><strong>Write clearly.</strong> Make sure what you write can't be misinterpreted. Avoid pronouns, and error on the side of providing too much information instead of too little.</li>
	</ol>

	
	
	
	<h3>System Info</h3>
	<p>Paste the following text into your bug report so I can better diagnose the problem you are experiencing.</p>
	
<textarea rows="20" cols="60" class="sample_code_textarea" style="border: 1px solid black;">
*SYSTEM INFO* <?php print "\n"; ?>
------------------------ <?php print "\n"; ?>
Plugin Version: <?php print CCTM::version; print ' '; print CCTM::version_meta; print "\n"; ?>
WordPress Version: <?php global $wp_version; print $wp_version; print "\n";?>
PHP Version: <?php print phpversion(); print "\n"; ?>
MySQL Version: <?php 
global $wpdb;
$result = $wpdb->get_results( 'SELECT VERSION() as ver' );
print $result[0]->ver;
print "\n";
?>
Server OS: <?php print PHP_OS; print "\n"; ?>
------------------------ <?php print "\n"; ?>
Other Active plugins: <?php 
print "\n";
$active_plugins = get_option('active_plugins'); 
$all_plugins = get_plugins();
foreach ($active_plugins as $plugin) {
//	print_r($all_plugins[$plugin]);
	if ( $all_plugins[$plugin]['Name'] != 'Custom Content Type Manager' ) {
		printf (' * %s v.%s [%s]'
			, $all_plugins[$plugin]['Name']
			, $all_plugins[$plugin]['Version']
			, $all_plugins[$plugin]['PluginURI']
		);
		print "\n";
	}
}
?>
</textarea>

	<p>The gist of this was inspired by <a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html">How to Report Bugs Effectively</a> by Simon Tatham.</p>

	<br/>
	
	<?php include('components/footer.php'); ?>
	
</div>