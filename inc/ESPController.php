<?php

class ESPController extends Controller{
    public function __construct($address, $label, $description = ""){
        parent::__construct($address, ControllerType::ESP_LED_STRIP, $label, $description, Array(ControllerFeatures::POWER, ControllerFeatures::COLOR));
    }

    public function on(){
        //{ "status": "OK", "response": "State has been changed to: 1" }
        return (new Request())->sentRequest(sprintf("http://%s/?action=on", $this->address))->getOutput();
    }

    public function off(){
        //{ "status": "OK", "response": "State has been changed to: 0" }
        return (new Request())->sentRequest(sprintf("http://%s/?action=off", $this->address))->getOutput();
    }

    public function set($r, $g, $b){
        //{ "status": "OK", "response": "Color rgbw(255,255,0,0) has been set" }
        return (new Request())->sentRequest(sprintf("http://%s/?action=set&r=%d&g=%d&b=%d", $this->address, $r, $g, $b))->getOutput();
    }

    public function get(){
        //{ "status": "OK", "response": {"state": 1, "r": 255, "g": 34, "b": 26, "w": 0 } }
        return (new Request())->sentRequest(sprintf("http://%s/?action=get", $this->address))->getOutput();
    }
};
