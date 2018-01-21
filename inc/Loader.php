<?php

class Loader{
    public static $loader = NULL;

    private static $paths = Array("./", "./inc/");
    private static $extensions = Array(".php", ".class.php");

    public function __construct(){
        spl_autoload_register(Array($this, "load"));
    }

    public static function init(){
        if(self::$loader === NULL)
            self::$loader = new self();
        return self::$loader;
    }

    public function load($className){
        foreach(self::$paths as $path){
            foreach(self::$extensions as $extension){
                if(file_exists($path.$className.$extension)){
                    require $path.$className.$extension;
                    return true;
                }
            }
        }
        return false;
    }
}
