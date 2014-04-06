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
    
    
    public static function make() {
    
    }
}