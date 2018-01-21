<?php

class HUEController extends Controller{
    public function __construct($address, $type, $label, $description = ""){
        $features = Array(ControllerFeatures::POWER);

        switch($type){
            case ControllerType::HUE_LED_STRIP:
                $features[] = ControllerFeatures::COLOR;
                $features[] = ControllerFeatures::BRIGHTNESS;
                break;
            case ControllerType::HUE_WHITE:
                $features[] = ControllerFeatures::BRIGHTNESS;
                break;
            default:
                break;
        }

        parent::__construct($address, $type, $label, $description, $features);
    }

    public function on(){
        $request = (new Request())
            ->addOption("CURLOPT_CUSTOMREQUEST", "PUT")
            ->addOption("CURLOPT_POSTFIELDS", '{ "on": true }')
            ->sentRequest(sprintf("http://%s/state", $this->address));

        return '{ "status": "OK", "response": "State has been changed to: 1" }';
    }

    public function off(){
        $request = (new Request())
            ->addOption("CURLOPT_CUSTOMREQUEST", "PUT")
            ->addOption("CURLOPT_POSTFIELDS", '{ "on": false }')
            ->sentRequest(sprintf("http://%s/state", $this->address));

        return '{ "status": "OK", "response": "State has been changed to: 0" }';
    }

    public function set($r, $g, $b){
        /*
        var_dump("test");
        $request = (new Request())
        ->addOption("CURLOPT_CUSTOMREQUEST", "GET")
        ->addOption("CURLOPT_POSTFIELDS", '{ "on": true }')
        ->sentRequest(sprintf("http://%s", $this->address))
        ->getOutput();
        return $request;
        */
    }

    public function get(){
        $data = json_decode((new Request())->sentRequest(sprintf("http://%s", $this->address))->getOutput(), TRUE);

        if($this->getType() == ControllerType::HUE_LED_STRIP){
            list($x, $y) = $data['state']['xy'];
            $rgb = $this->XY2RGB($x, $y, $data['state']['bri']);
        }
        else
            $rgb = Array('r' => 0, 'g' => 0, 'b' => 0);

        return '{ "status": "OK", "response": {"state": '.($data['state']['on']? "1" : "0").', "r": '.$rgb['r'].', "g": '.$rgb['g'].', "b": '.$rgb['b'].', "w": 0 } }';
    }

    //https://developers.meethue.com/documentation/color-conversions-rgb-xy
    private function XY2RGB($x, $y, $brightness){
        $z = 1.0 - $x - $y;
        $Y = $brightness;
        $X = ($Y / $y) * $x;
        $Z = ($Y / $y) * $z;

        $r = $X * 1.656492 - $Y * 0.354851 - $Z * 0.255038;
        $g = -$X * 0.707196 + $Y * 1.655397 + $Z * 0.036152;
        $b = $X * 0.051713 - $Y * 0.121364 + $Z * 1.011530;

        $r = ($r <= 0.0031308) ? 12.92 * $r : (1.0 + 0.055) * pow($r, (1.0 / 2.4)) - 0.055;
        $g = ($g <= 0.0031308) ? 12.92 * $g : (1.0 + 0.055) * pow($g, (1.0 / 2.4)) - 0.055;
        $b = ($b <= 0.0031308) ? 12.92 * $b : (1.0 + 0.055) * pow($b, (1.0 / 2.4)) - 0.055;

        $max = ($r > $g)? $r : $g;
        $max = ($max > $b)? $max : $b;

        $r /= $max;
        $g /= $max;
        $b /= $max;

        $r *= 255;
        $g *= 255;
        $b *= 255;

        if($r < 0) $r = 255;
        if($g < 0) $g = 255;
        if($b < 0) $b = 255;

        return Array('r' => round($r), 'g' => round($g), 'b' => round($b));
    }
};
