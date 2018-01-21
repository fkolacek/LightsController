<?php

class Request{

    private $output;
    private $options;
    private $data;

    public function __construct(Array $options = Array(), Array $data = Array()){
        $this->output = "";
        $this->options = $options;
        $this->data = $data;
        return $this;
    }

    public function sentRequest($request){
        $r = curl_init();

        curl_setopt($r, CURLOPT_URL, $request);
        curl_setopt($r, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($r, CURLOPT_TIMEOUT, 5);
        curl_setopt($r, CURLOPT_RETURNTRANSFER, 1);

        foreach($this->options as $key=>$val)
            curl_setopt($r, constant(strtoupper($key)), $val);

        if(count($this->data) > 0)
            curl_setopt($r, CURLOPT_POSTFIELDS, http_build_query($this->data));

        $this->output = curl_exec($r);

        curl_close($r);
        return $this;
    }

    public function addOption($key, $val){
        $this->options[$key] = $val;
        return $this;
    }

    public function addData($key, $val){
        $this->data[$key] = $val;
        return $this;
    }

    public function getOutput(){
        return $this->output;
    }
};
