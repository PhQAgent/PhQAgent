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
        $templete = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'QRPage.html');
        $qrcode = base64_encode($this->qrcode);
        return str_replace('$BASE64QRCODE', $qrcode, $templete);
    }

    public function __toString(){
        return $this->getHTML();
    }

}