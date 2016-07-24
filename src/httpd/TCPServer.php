<?php
namespace httpd;

class TCPServer{
    private $handler;
    private $socket;

    public function __construct($handler, $port = 80, $address = '0.0.0.0'){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($this->socket);
        if(!socket_bind($this->socket, $address, $port)){
            echo "Address already in use\n";
            exit(1);
        }
        socket_listen($this->socket, 4);
    }

    public function receive(){
        while($this->handler->isRunning()){
            $socket = socket_accept($this->socket);
            if($socket){
                $this->handler->onReceive($socket);
            }
        }
        socket_shutdown($this->socket, 2);
        socket_close($this->socket);
    }

    public function reply($socket, $message){
        socket_write($socket, $message, strlen($message));
        socket_close($socket);
    }

}