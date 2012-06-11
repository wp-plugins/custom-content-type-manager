<?php
/**
 * FOR THE DEVELOPER ONLY!!!
 *
 * This class contains unit tests using the SimpleTest framework: http://simpletest.org/
 * 
 * BEFORE YOU RUN TESTS
 *
 * These tests are meant to run in a controlled environment with a specific version of 
 * WordPress, with a specific theme, and with specific plugins enabled or disabled.
 * A dump of the database used is included as reference for all tests.
 *
 * RUNNING TESTS
 *
 *
 * http://codex.wordpress.org/Automated_Testing
 * 
 * @package CCTM
 * @author Everett Griffiths
 * @url http://fireproofsocks.com/
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');

class CCTMUnitTests extends UnitTestCase {
	
	private function _compare_html($a, $b) {
	
	}
	
	/**
	 * Test whether a regular category page displays posts and 
	 * any pages from custom post-types that have been categorized
	 */

/*
    function testCategories() {
    	$page = file_get_contents('http://cctm:8888/category/uncategorized/');
    	
    	print $page;
    }
*/
	// Archives
	// Categories
	// tags
	
/*
    function testTags() {
    	$page = file_get_contents('http://cctm:8888/category/uncategorized/');
    	
    	print $page;
    }
*/

	/**
	 * Make sure we didn't accidentally bundle software that's under the 
	 * Creative Commons License.
	 */
/*
	function testNoCCL() {
	
	}
*/


	/**
	 * Change post_type name
	 */

	/**
	 * Test RSS feed
	 */
	function testRSS() {
		$xml = file_get_contents('http://cctm:8888/feed/');
		
		$this->assertTrue($xml);
	}
	
	//------------------------------------------------------------------------------
	// Test Global Settings
	//------------------------------------------------------------------------------
	// Delete Posts
	// Delete Custom Fields
	// Save Empty Fields
	// Show Pages in RSS Feed
	
	// 
}
 
/*EOF*/