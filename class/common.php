<?php

 
class MPM_Common {
    
    
    private $_db;
    private static $_instance;

    public function __construct() {
        global $wpdb;

        $this->_db = $wpdb;
    }

    
     public static function getInstance() {
        if ( ! self::$_instance ) {
            self::$_instance = new MPM_Common();
        }

        return self::$_instance;
    }
    
 

}