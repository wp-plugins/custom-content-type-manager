<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM;

class View {
    
    public static $Log;
    public static $POST;
    public static $GET;
    
    /**
     * @object Pimple dependency container
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
    }
    
    
	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the views/ directory)
	 * @param array   $data (optional) associative array of data
	 * @param string  $path (optional) pathname. Can be overridden for 3rd party fields
	 * @return string the parsed contents of that file
	 */    
	public static function make($filename, $data=array(), $path=null) {
        self::$Log->debug('View parameters -- filename: ' .print_r($filename,true). ' data: '.print_r($data,true). ' path: '.$path, __CLASS__,__LINE__);
		if (empty($path)) {
			$path = CCTM_PATH . '/views/';
		}
		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			return ob_get_clean();
		}
		self::$Log->error('View file does not exist: ' .$path.$filename, __CLASS__,__LINE__);
		return 'View file does not exist: ' .$path.$filename;
	}

}