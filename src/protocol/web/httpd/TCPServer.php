<?php
namespace protocol\web\httpd;
use phqagent\console\MainLogger;

class TCPServer extends \Thread{

    private $qrcode;
    private $port;
    private $addr;
    public $isclosed;

    public function __construct($qrcode, $port = '8023', $addr = '0.0.0.0'){
        $this->isclosed = false;
        $this->qrcode = $qrcode;
        $this->port = $port;
        $this->addr = $addr;
        QRPage::init();
        CSS::init();
        $this->start();
    }

    public function setQRCode($qrcode){
        $this->qrcode = $qrcode;
    }

    public function run(){
        date_default_timezone_set('Asia/Shanghai');
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        if(@socket_bind($socket, $this->addr, $this->port)){
            socket_listen($socket);
            MainLogger::success("扫码页绑定于 {$this->addr}:{$this->port}");
        }else{
            MainLogger::warning("TCP绑定失败");
        }
        while(!$this->isclosed){
            if($client = socket_accept($socket)){
                $header = socket_read($client, 8192);
                if(strstr($header, 'base.min.css')){
                    socket_write($client, (string)new CSS(CSS::BASE));
                }elseif(strstr($header, 'project.min.css')){
                    socket_write($client, (string)new CSS(CSS::PROJECT));
                }else{
                    socket_write($client, (string)new QRPage($this->qrcode));
                }
                socket_close($client);
            }
            usleep(10);
        }
        socket_close($socket);
    }

    public function shutdown(){
        MainLogger::warning('扫码页已关闭');
        $this->isclosed = true;
    }

}