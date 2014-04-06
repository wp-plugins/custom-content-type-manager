<?php
/**
 * Maps a manager URL to a controller.
 * The controller should eventually return a view.
 *
 * @package CCTM
 */
namespace CCTM;

class Route {
    
    public static $Log;
    public static $Load;
    public static $View;
    public static $POST;
    public static $GET;
        
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Load = $dependencies['Load'];
        self::$View = $dependencies['View'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
        self::$Log->debug('Construct.', __CLASS__, __LINE__);        
    }


    
    /**
     * 
     *
     * @param string $routename
     * @return string HTML web page usually
     */
    public function get($routename) {

//        print $routename; exit;
        // is routename valid?

        // Our 404
        if(!$Controller = self::$Load->controller($routename.'xxx')) {
            print "not found.";
            // print View::{templatename}(array('msg'=>'Something')); <-- would not work well for mini views
            // print View::make({filename}, $data);
            return;
        }

        return (self::$POST) ? $Controller->post() : $Controller->get();
    }    
}