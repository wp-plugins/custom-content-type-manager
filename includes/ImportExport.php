<?php
/**
 * Used to handle various tasks involved with the importing and exporting of CCTM definition data.
 *
 *
 * @package
 */


class ImportExport {

	/**
	 * API for dedicated CCTM pastebin user.
	 */
	const pastebin_dev_key = '';
	const pastebin_endpoint = '';

	/**
	 * Initiates a download: prints headers with payload
	 * or an error.
	 */
	public static function export_to_desktop() {

		// The nonce here must line up with the nonce defined in
		// includes/CCTM.php ~line 2300 in the page_export() function
		$nonce = '';
		if ( isset($_GET['_wpnonce']) ) {
			$nonce = $_GET['_wpnonce'];
		}
		if (! wp_verify_nonce($nonce, 'cctm_download_definition') ) {
			die( __('Invalid request.', CCTM_TXTDOMAIN ) );
		}
		
		
		// Load up the settings
		$save_me = get_option( CCTM::db_key_settings, array() );
		// and tack on additional tracking stuff
		// consider user data: http://codex.wordpress.org/get_currentuserinfo
		$save_me['export_info']['_timestamp_export'] = time();
		$save_me['export_info']['_source_site'] = site_url();
		$save_me['export_info']['_charset'] = get_bloginfo('charset');
		$save_me['export_info']['_language'] = get_bloginfo('language');
		$save_me['export_info']['_wp_version'] = get_bloginfo('version');
		$save_me['export_info']['_cctm_version'] = CCTM::version;
		// And finally, the main event				
		$save_me['payload'] = get_option( CCTM::db_key, array() );
		
		// download-friendly name of the file
		$title = 'definition'; // default --> .cctm.json is appended
		if ( !empty($save_me['export_info']['title']) ) {
			$title = $save_me['export_info']['title'];
			$title = strtolower($title);
			$title = preg_replace('/\s+/', '_', $title); 
			$title = preg_replace('/[^a-z_]/', '', $title); 
		}
		
		
		if ( $download = json_encode($save_me) ) {
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=$title.cctm.json");
			header("Content-length: ".(string) mb_strlen($download, '8bit') );
			header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
			header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			print $download;
		}
		else {
			print __('There was a problem exporting your CCTM definition.', CCTM_TXTDOMAIN);
		}
	}



	/**
	 *
	 */
	public static function export_to_local_webserver() {

	}



	/**
	 * see http://pastebin.com/api
	 */
	public static function export_to_pastebin() {

	}


	/**
	 *
	 */
	public static function import_from_desktop() {

	}



	/**
	 *
	 */
	public static function import_from_local_webserver() {

	}


	/**
	 *
	 */
	public static function import_from_pastebin() {

	}


	/**
	 * Take a data structure and return true or false as to whether or not it's
	 * in the correct format for a CCTM definition.
	 */
	public static function validate_data_structure($data) {
	
	}

}


/*EOF*/