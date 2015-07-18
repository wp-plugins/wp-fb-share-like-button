<?php

class Licence {

    /**
     * The init path for autoload
     * @var string
     */
    static public $initPath;

    /**
     * Flag to check if is already intialized
     * @var boolean
     */
    static public $initialized = false;

    /**
     * Constructor
     *
     * set private to avoid directly instatiation to implement
     * but is not a Singleton Design Pattern
     * */
    private function __construct() {
        
    }

    /**
     * Configure autoloading using Padl.
     *
     * This is designed to play nicely with other autoloaders.
     *
     * @param string $initPath The init script to load when autoloading the first Padl class
     * 
     * @return void
     */
    public static function registerAutoload($initPath = null) {
        self::$initPath = $initPath;
        spl_autoload_register(array('Licence', 'autoload'));
    }

    /**
     * Internal autoloader for spl_autoload_register().
     *
     * @param string $class The class to load
     *
     * @return void
     */
    public static function autoload($class) {


        $path = dirname(__FILE__) . '/' . str_replace('\\', '/', $class) . '.php';
        if (!file_exists($path)) {
            return;
        }
        if (self::$initPath && !self::$initialized) {
            self::$initialized = true;
            require self::$initPath;
        }
        require_once $path;
    }

}
