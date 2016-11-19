<?php
namespace protocol\httpd;

class QRPage{

    private $qrcode;

    public static function init(){
        return 'pthread hack';
    }

    public function __construct($qrcode){
        $this->qrcode = $qrcode;
    }

    public function getHTML(){
        $templete = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'QRPage.html.php');
        $qrcode = base64_encode($this->qrcode);
        return str_replace('$BASE64QRCODE', $qrcode, $templete);
    }

    public function __toString(){
        $html = $this->getHTML();
        $pk = "HTTP/1.1 200 OK\r\n";
        $pk .= 'Content-Length: ' . strlen($html) . "\r\n";
        $pk .= "Server: PhQAgent\r\n";
        $pk .= "Content-Type: text/html\r\n";
        $pk .= "\r\n";
        $pk .= $html;
        return $pk;
    }

}