<?php
/**
 * A CRUD-ish container for managing custom fields and their definitions.
 *
 * @package CCTM
 */
namespace CCTM;

class Customfield {
    
    public static $Log;
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(object $Log) {
        self::$Log = $Log;
    }


    public static function create() {
    
    }
    
    public static function destroy($name) {
    
    }
    
    public static function duplicate($name,$newname='') {
    
    }
    
    public static function getOne($name) {
    
    }
    
    public static function getAll($criteria=array()) {
    
    }
    
    public static function rename($name,$newname) {
    
    }

}