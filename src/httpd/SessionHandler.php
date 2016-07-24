<?php
namespace httpd;

class SessionHandler{

    private $server;
    private $socket;
    private $map;

    public function __construct($server){
        $this->server = $server;
        $this->socket = new TCPServer($this);
    }

    public function onReceive($socket){
        (new Session($socket))->start();
    }

    public function isRunning(){
        return true;
    }

    public function getRequestProcesser(){
        return $this->server->getBackend();
    }

}