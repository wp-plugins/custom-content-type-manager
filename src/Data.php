<?php
/**
 * Super simple storage object needed so we handle properties that have not been set.
 *
 * @package CCTM
 */
namespace CCTM;
class Data {
    
    public $data = array();
    public $notset = array();

    /**
     * Our Getter. We track any "misses" in the notset array for debugging.
     */
    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        array_push($this->notset, $key);
        return null;
    }
    
    /**
     * Our setter
     */
    public function __set($key,$value) {
        $this->data[$key] = $value;
    }
}

/*EOF*/