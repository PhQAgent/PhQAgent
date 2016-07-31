<?php
namespace plugin;

use element\Message;
use worker\MessageSender;

abstract class PluginBase{

    protected $server;
    protected $sender;

    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function getServer(){
        return $this->server;
    }

    public function onReceive(Message $message){

    }
    
    public function onLoad(){

    }

}