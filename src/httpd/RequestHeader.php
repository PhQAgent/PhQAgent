<?php
namespace httpd;

class RequestHeader{
    private $packet;
    private $method;
    private $url;
    private $available = false;

    public function __construct($packet){
        $packet = explode("\n", $packet);
        $this->getRealRequest($packet);
    }

    private function getRealRequest(){
        if(is_array($packet)){
            if(isset($packet[0])){
                $request = explode(' ', $packet[0]);
                $this->method = $this->getRealMethod($request[0]);
                $this->url = $this->getMethod($request[1]);
            }
        }
    }

    public function getMethod(){
        return $this->method;
    }

    public function getUrl(){
        return $this->url;
    }

    private function getRealMethod($method){
        if(isset(static::$METHOD_MAP[$method])){
            return static::$METHOD_MAP[$method];
        }else{
            $this->setAvailabe(false);
        }
    }

    public function isAvailabe(){
        return $this->available;
    }

    private function setAvailabe($bool){
        $this->available = $bool;
    }

    static $METHOD_MAP=[
        'GET' => 'GET',
        'POST' => 'POST',

    ];

}