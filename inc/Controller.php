<?php

abstract class ControllerType{
    const ESP_LED_STRIP = 0;
    const HUE_LED_STRIP = 1;
    const HUE_WHITE = 2;
};

abstract class ControllerFeatures{
    const POWER = 0;
    const COLOR = 1;
    const BRIGHTNESS = 2;
}

abstract class Controller{

    protected $address;
    protected $type;
    protected $label;
    protected $description;
    protected $features;

    public function __construct($address, $type, $label, $description = "", $features = Array()){
        $this->address = $address;
        $this->type = $type;
        $this->label = $label;
        $this->description = $description;
        $this->features = $features;
    }

    public function getAddress(){
        return $this->address;
    }

    public function getType(){
        return $this->type;
    }

    public function getLabel(){
        return $this->label;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getFeatures(){
        return $this->features;
    }

    public function getKey(){
        return sha1($this->address.$this->type.$this->label.$this->description);
    }

    abstract public function on();
    abstract public function off();
    abstract public function set($r, $g, $b);
    abstract public function get();
};
