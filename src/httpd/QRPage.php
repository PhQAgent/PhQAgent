<?php
namespace httpd;
class QRPage{

    private $lhandler;
    private $qrcode;

    public function __construct($lhandler){
        if($lhandler == null) return;
        $this->lhandler = $lhandler;
        $this->qrcode = $lhandler->getAuthThread()->getQRCode();
    }

    public function getHTML(){
        $templete = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'QRPage.html.php');
        $qrcode = base64_encode($this->qrcode);
        return str_replace('$BASE64QRCODE', $qrcode, $templete);
    }

    public function __toString(){
        $html = $this->getHTML();
        $pk .= "HTTP/1.1 200 OK\r\n";
        $pk .= 'Content-Length: ' . strlen($html) . "\r\n";
        $pk .= "Server: PhQAgent\r\n";
        $pk .= "Content-Type: text/html\r\n";
        $pk .= "\r\n";
        $pk .= $html;
        return $pk;
    }

}