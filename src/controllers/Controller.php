<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM\Controllers;

class Controller {

    public static $Log;
    public static $View;

    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$View = $dependencies['View'];
    }  
    
    /**
     * Our 404
     *
     */
    public function __call($name,$args) {
        print View::make('error.php', array('msg'=>'Page not found: '.$name));
        self::$Log->debug('Page not found: '.$name, __CLASS__, __LINE__);
        return;
    }
}