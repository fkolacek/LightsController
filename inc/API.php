<?php

class API{

    private $args;
    private $controllers;

    public function __construct($args){
        $this->args = Array();
        $this->controllers = Array();

        foreach($args as $key=>$val){
            if(in_array($key, Array("action", "controller", "r", "g","b", "api")) && $val != "")
                $this->args[$key] = $val;
        }
    }

    public function addController(Controller $controller){
        $this->controllers[($controller->getKey())] = $controller;
    }

    public function processCall(){
        $output = "";

        if($this->isArg("controller") && $this->isArg("action") && $this->isController($this->getArg("controller"))){
            $controller = $this->getController($this->getArg("controller"));

            switch($this->getArg("action")){
                case "on":
                    $output = $controller->on();
                    break;
                case "off":
                    $output = $controller->off();
                    break;
                case "set":
                    if($this->isArg("r") && $this->isArg("g") && $this->isArg("b"))
                        $output = $controller->set($this->getArg("r"), $this->getArg("g"), $this->getArg("b"));
                    break;
                case "get":
                    $output = $controller->get();
                    break;
                default:
                    break;
            }
        }

        return $output;
    }

    public function getArgs(){
        return $this->args;
    }

    public function isArg($key){
        return array_key_exists($key, $this->args);
    }

    public function getArg($key){
        return ($this->isArg($key))? $this->args[$key] : FALSE;
    }

    public function getControllers(){
        return $this->controllers;
    }

    public function isController($id){
        return array_key_exists($id, $this->controllers);
    }

    public function getController($id){
        return ($this->isController($id))? $this->controllers[$id] : FALSE;
    }

    public function getControllerName($label){
        return strtolower();
    }
};
