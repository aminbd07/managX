<?php

/**
 * Handle all of ajax request 
 *
 * @author Nurul Amin
 */
class MANAGX_Ajax {

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new MANAGX_Ajax();
        }

        return self::$_instance;
    }

    public function __construct() {
        
    }

}
