<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM\Models;

class Model {

    public static $Log;
    public static $View;

    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Cache = $dependencies['Model'];
    }  
    
}