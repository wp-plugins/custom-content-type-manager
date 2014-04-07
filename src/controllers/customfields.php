<?php
namespace CCTM\Controllers;
use CCTM;
class Customfields extends Controller {

    public function getIndex() {
        self::$View->fields = self::$Customfields->getAll();
        print self::$View->make('list_customfields.php');
    }
    
}