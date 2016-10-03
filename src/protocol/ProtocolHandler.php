<?php
namespace protocol;

abstract class ProtocolHandler{
    const WebQQ = 0;
    const AndroidQQ = 1;

    public static function use($version){
        if($version == ProtocolHandler::WebQQ){
            include __DIR__ . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'Protocol.php';
        }
    }

}