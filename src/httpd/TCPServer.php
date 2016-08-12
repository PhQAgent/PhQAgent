<?php
namespace httpd;
use utils\MainLogger;
use login\LoginHandler;
class TCPServer extends \Thread{

    private $lhandler;
    private $port;
    private $addr;

    public function __construct(LoginHandler $lhandler, $port = '8023', $addr = '0.0.0.0'){
        $this->lhandler = $lhandler;
        $this->port = $port;
        $this->addr = $addr;
        $this->logger = MainLogger::getInstance();
        new QRPage(null);
        $this->start();
    }

    public function run(){
        date_default_timezone_set('Asia/Shanghai');
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        if(@socket_bind($socket, $this->addr, $this->port)){
            socket_listen($socket);
            $this->logger->success("扫码页绑定于 {$this->addr}:{$this->port}");
        }else{
            $this->logger->warning("TCP绑定失败");
        }
        while($this->lhandler->isRunning()){
            $client = socket_accept($socket);
            socket_write($client, (string)new QRPage($this->lhandler));
            socket_close($client);
        }
        socket_close($socket);
    }

}