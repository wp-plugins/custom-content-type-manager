<?php
/**
 * The View object stores data which is accessed directly by the view file
 * The view class leverages a separate "Data" class used for storage so we can
 * easily pass the data to the view file.
 *
 * @package CCTM
 */
namespace CCTM;

class View {
    
    public static $Log;
    public static $Data;
    public static $POST;
    public static $GET;
    
    public static $sub_dir = '/views/'; // defines where in the app the view files are.
    
    /**
     * @object Pimple dependency container
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Data = $dependencies['Data'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
        
        // Some defaults for our views
        self::$Data->help = 'https://code.google.com/p/wordpress-custom-content-type-manager/';
    }
    
    /**
     * Our Getter
     */
    public function __get($key) {
        return (isset(self::$Data->$key)) ? self::$Data->$key : null;
    }
    
    /**
     * Our setter
     */
    public function __set($key,$value) {
        self::$Data->$key = $value;
    }

	//------------------------------------------------------------------------------
	/**
	 * Load up a PHP file into a string via an include statement. MVC type usage here.
	 *
	 * @param string  $filename (relative to the "views/" directory)
	 * @param string  $path (optional) pathname. Can be overridden for 3rd party fields
	 * @return string the parsed contents of that file
	 */    
	public static function make($filename, $path=null, $debug=false) {
        self::$Log->debug('View parameters -- filename: ' .print_r($filename,true). ' data: '.print_r((array)self::$Data,true). ' path: '.$path, __CLASS__,__LINE__);
		if (empty($path)) {
			$path = CCTM_PATH . self::$sub_dir;
		}
		$data =& self::$Data;
		if (is_file($path.$filename)) {
			ob_start();
			include $path.$filename;
			if ($debug) {
    			ob_get_clean();		
    			return print_r((array) self::$Data->notset);                
			}
			return ob_get_clean();
		}
		self::$Log->error('View file does not exist: ' .$path.$filename, __CLASS__,__LINE__);
		return 'View file does not exist: ' .$path.$filename;
	}
}