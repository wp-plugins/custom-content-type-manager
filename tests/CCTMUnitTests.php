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
 * @package CCTM
 * @author Everett Griffiths
 * @url http://fireproofsocks.com/
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');

class CCTMUnitTests extends UnitTestCase {


	
	/**
	 * Test whether a regular category page displays posts and 
	 * any pages from custom post-types that have been categorized
	 */
    function testCategories() {
    
    }

	/**
	 * Make sure we didn't accidentally bundle software that's under the 
	 * Creative Commons License.
	 */
	function testNoCCL() {
	
	}

}
 
/*EOF*/