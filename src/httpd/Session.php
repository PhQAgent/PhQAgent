<?php
namespace httpd;

class Session extends \Thread{

    private $handler;
    private $session;

    public function __construct($handler){
        $this->handler = $handler;
    }

    public function run(){
        $buffer = socket_read($this->session, 8192);
        $request = new RequestHeader($buffer);
        if(!$request->isAvailable()){
            return $this->onErrorPacket();
        }
        var_dump($request);
        $this->handler->onFinish($this);
    }

    private function onErrorPacket(){
        $this->getSocket()->reply($this->socket, (string)(new ResponseHeader()));
    }

    public function onFinish($session){
        $this->getSocket()->reply($this->socket, $session->getResponse());
    }

}