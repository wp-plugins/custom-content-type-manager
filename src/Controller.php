<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM;

class Controller {
    
    public static $Log;
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
    }

	//------------------------------------------------------------------------------
	/**
	 * Nonces exist in the $_POST array using the key named like this:
	 * conroller_name + _nonce.  The nonce is always named "ajax_nonce".
	 * WARNING: The response returned by the ajax-controllers *must* be wrapped in
	 * some kind of HTML tag, otherwise you can't use jQuery('#target_id').html(x)
	 * to write it.
	 *
	 * @param string $name of the method being called
	 * @param mixed $args sent to that method
	 */
	public function __call($name, $args) {
	
        $file = CCTM_PATH.'/controllers/'.$name.'.php';
//        print $file; exit;
		if (preg_match('/[^a-z_\-]/',$name) ) { //|| !file_exists($file)) {
            $msg = sprintf(__('Invalid Controller: %s', CCTM_TXTDOMAIN), "<em>$name</em>");
			self::$Log->error($msg,__FILE__,__LINE__);
			die($msg);
		}

        if (!empty($_POST)) {
            // Post route
        }
        // Get route
        
/*
		$nonce = CCTM::get_value($_REQUEST, $name.'_nonce');
		if ( ! wp_verify_nonce( $nonce, 'ajax_nonce' ) ) {
			CCTM::log(sprintf(__('Invalid nonce for %s', CCTM_TXTDOMAIN), "<em>$name</em>"),__FILE__,__LINE__);
			die(sprintf(__('Invalid nonce for %s', CCTM_TXTDOMAIN), "<em>$name</em>"));
		}
*/

		include $file;

		exit; // terminate the request
	}
    
    
}