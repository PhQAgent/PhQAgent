<?php
namespace httpd;

class AodHTTPD extends \Thread{
    const NAME = "AodHTTPD";
    private $backend;
    private $sessionhandler;

    public function __construct($backend){
        $this->backend = $backend;
        $this->sessionhandler = new SessionHandler($this);
    }

    public function getBackend(){
        return $this->backend;
    }

}