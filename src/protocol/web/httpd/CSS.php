<?php
namespace protocol\web\httpd;
class CSS{

    const BASE = 1;
    const PROJECT = 2;

    private $type;

    public static function init(){
        return 'pthread hack';
    }

    public function __construct($type){
        $this->type = $type;
    }

    public function getCSS(){
        switch($this->type){
            case 1:
                return file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'Base.css.php');
            case 2:
                return file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'Project.css.php');
        }
    }

    public function __toString(){
        $css = $this->getCSS();
        $pk = "HTTP/1.1 200 OK\r\n";
        $pk .= 'Content-Length: ' . strlen($css) . "\r\n";
        $pk .= "Server: PhQAgent\r\n";
        $pk .= "Content-Type: text/css\r\n";
        $pk .= "\r\n";
        $pk .= $css;
        return $pk;
    }

}