<?php
namespace httpd;
use utils\MainLogger;
use login\LoginHandler;
class TCPServer extends \Thread{

    private $lhandler;
    private $port;
    private $addr;
    public $isclosed;

    public function __construct(LoginHandler $lhandler, $port = '8023', $addr = '0.0.0.0'){
        $this->isclosed = false;
        $this->lhandler = $lhandler;
        $this->port = $port;
        $this->addr = $addr;
        $this->logger = MainLogger::getInstance();
        new QRPage(null);
        new CSS(null);
        $this->start();
    }

    public function run(){
        date_default_timezone_set('Asia/Shanghai');
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        if(@socket_bind($socket, $this->addr, $this->port)){
            socket_listen($socket);
            socket_set_nonblock($socket);
            $this->logger->success("扫码页绑定于 {$this->addr}:{$this->port}");
        }else{
            $this->logger->warning("TCP绑定失败");
        }
        while(!$this->isclosed){
            if(($client = socket_accept($socket))){
                $header = socket_read($client, 8192);
                if(strstr($header, 'base.min.css')){
                    socket_write($client, (string)new CSS(CSS::BASE));
                }elseif(strstr($header, 'project.min.css')){
                    socket_write($client, (string)new CSS(CSS::PROJECT));
                }else{
                    socket_write($client, (string)new QRPage($this->lhandler));
                }
                socket_close($client);
            }
            usleep(10);
        }
        socket_close($socket);
    }

}