<?php
namespace plugin;

use element\Message;

abstract class PluginBase{

    protected $server;

    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function reply(Message $message){
        $this->getServer()->getMessageSender()->send($message);
    }

    public function getMessageSender(){
        return $this->server->getMessageSender();
    }

    public function getServer(){
        return $this->server;
    }

    public function onReceive(Message $message){

    }
    
    public function onLoad(){

    }

}