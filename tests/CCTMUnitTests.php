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
//require_once(CCTM_PATH .'/includes/CCTM_Validator.php');
//require_once(CCTM_PATH .'/validators/CCTM_FormElement.php');

//require_once(CCTM_PATH .'/includes/CCTM_FormElement.php');

class CCTMUnitTests extends UnitTestCase {
	
	
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
	//!Test Global Settings
	//------------------------------------------------------------------------------
	// Delete Posts
	// Delete Custom Fields
	// Save Empty Fields
	// Show Pages in RSS Feed
	
	// 
	
	//------------------------------------------------------------------------------
	//!Test Validators
	//------------------------------------------------------------------------------
	function testEmail() {
		$V = CCTM::load_object('emailaddress','validators');
		
		$email = 'notan-emailaddress.';
		
		$V->validate($email);		
		$this->assertFalse(empty($V->error_msg));
	}

	function testEmail2() {
		$V = CCTM::load_object('emailaddress','validators');
		$email = 'someone@yahoo.com';
		$V->validate($email);

		$this->assertTrue(empty($V->error_msg));
	}

	function testEmail3() {
		$V = CCTM::load_object('emailaddress','validators');
		$email = 'payer@player-hater.com';
		$V->validate($email);
		$this->assertTrue(empty($V->error_msg));
	}

	//------------------------------------------------------------------------------
	function testNumber() {
		$V = CCTM::load_object('number','validators');
		$number = 'asdf';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}


	function testNumber3() {
		$V = CCTM::load_object('number','validators');
		$number = '123';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber4() {
		$V = CCTM::load_object('number','validators');
		$V->min = 4;
		$V->max = 6;
		$number = '10';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}

	function testNumber5() {
		$V = CCTM::load_object('number','validators');
		$V->min = 4;
		$V->max = 6;
		$number = '5';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber6() {
		$V = CCTM::load_object('number','validators');
		$V->allow_negative = 1;
		$V->max = 6;
		$number = '-5';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber7() {
		$V = CCTM::load_object('number','validators');
		$V->allow_negative = 0;
		$V->max = 6;
		$number = '-5';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}
	
	//------------------------------------------------------------------------------
	//!Output Filters
	//------------------------------------------------------------------------------
	function testFilter1() {
		$post = CCTM::filter(1,'get_post');
		$this->assertTrue($post['post_title'] =='Post1');
	}
	function testFilter2() {
		$posts = CCTM::filter(array(1,2),'get_post');
		print_r($posts); exit;
		$this->assertTrue($post['post_title'] =='Post1');
	}
	
	
	
	
	//------------------------------------------------------------------------------
	//! Helper Classes
	//------------------------------------------------------------------------------
	function testGetValidators() {
		$classes = CCTM::get_available_helper_classes('validators');
		$this->assertTrue(isset($classes['emailaddress']));
		$this->assertTrue(isset($classes['url']));
		$this->assertTrue(isset($classes['number']));
	}	

	function testGetOutputFilters() {
		$classes = CCTM::get_available_helper_classes('filters');
		$this->assertTrue(isset($classes['default']));
		$this->assertTrue(isset($classes['do_shortcode']));
		$this->assertTrue(isset($classes['email']));
		$this->assertTrue(isset($classes['excerpt']));
		$this->assertTrue(isset($classes['formatted_list']));
		$this->assertTrue(isset($classes['gallery']));
		$this->assertTrue(isset($classes['get_post']));
		$this->assertTrue(isset($classes['help']));
		$this->assertTrue(isset($classes['raw']));
		$this->assertTrue(isset($classes['to_array']));
		$this->assertTrue(isset($classes['to_image_array']));
		$this->assertTrue(isset($classes['to_image_src']));
		$this->assertTrue(isset($classes['to_image_tag']));
		$this->assertTrue(isset($classes['to_link']));
		$this->assertTrue(isset($classes['to_link_href']));
		$this->assertTrue(isset($classes['userinfo']));
		$this->assertTrue(isset($classes['wrapper']));
	}

	function testCustomFields() {
		$classes = CCTM::get_available_helper_classes('fields');
		$this->assertTrue(isset($classes['checkbox']));
		$this->assertTrue(isset($classes['colorselector']));
		$this->assertTrue(isset($classes['date']));
		$this->assertTrue(isset($classes['dropdown']));
		$this->assertTrue(isset($classes['image']));
		$this->assertTrue(isset($classes['media']));
		$this->assertTrue(isset($classes['multiselect']));
		$this->assertTrue(isset($classes['relation']));
		$this->assertTrue(isset($classes['text']));
		$this->assertTrue(isset($classes['textarea']));
		$this->assertTrue(isset($classes['user']));
		$this->assertTrue(isset($classes['wysiwyg']));

		// 3rd Party stuff
		$this->assertTrue(isset($classes['exercises']));

	}
	// Test bogus PHP file in one of the dirs... the turd in the punchbowl
	function testBogusValidator() {
		$V = CCTM::load_object('bogus','validators');
		$this->assertFalse($V);
	}

	//------------------------------------------------------------------------------
	//!Parser
	//------------------------------------------------------------------------------
	function testParser1() {
		$tpl = 'Hello my name is [+name+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Hello my name is John');
	}	
	function testParser2() {
		$tpl = 'Hello my name is [+name+][+unused+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Hello my name is John');
	}
	function testParser3() {
		$tpl = 'Hello my name is [+name+][+unused+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash,true);
		$this->assertTrue($output == 'Hello my name is John[+unused+]');
	}
	function testParser4() {
		$tpl = '[+post_id:to_link_href+]';
		$hash = array('post_id' => 1);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == get_permalink(1));
	}
	function testParser5() {
		$tpl = '[+post_id:to_link:Click me here+]';
		$hash = array('post_id' => 1);
		$actual = CCTM::parse($tpl,$hash);
		$post_id = 1;
		$P = get_post($post_id);
		$expected = '<a href="'.get_permalink(1).'" title="'.$P->post_title.'">Click me here</a>';
		$this->assertTrue($expected == $actual);
	}
	function testParser6() {
		$tpl = 'This is my formatting string';
		$hash = 'This should be an array';
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == $tpl);
	}	
	// Custom Field with custom settings -- does the link appear?
}
 
/*EOF*/