<?php
/**
 * 
 *
 * @package CCTM
 */
namespace CCTM;

class Cache {

    public static $Log;
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(object $Log) {
        self::$Log = $Log;
    }
        
}