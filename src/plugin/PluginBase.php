<?php
namespace plugin;

use module\Uin2Acc;
use module\GroupInfo;
use module\GetFriendInfo;
use element\Message;

abstract class PluginBase{

    protected $server;
    protected $group;
    protected $message;
    protected $haveMessage = false;

    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function getNickNameList(){
        $gi = new GroupInfo($this->getServer());
        $gid = $gi->getGid($this->message['from']);
        return $gi->getNickNameList($gid);
    }

    public function getNickName(){
        if($this->getType() == 'group_message'){
            return $this->getNickNameList()[$this->message['send']];
        }elseif($this->getType() == 'message'){
            return (new GetFriendInfo($this->getServer()))->getNick($this->message['send']);
        }
    }

    public function getFrom(){
        return $this->message['from'];
    }

    public function getAccount(){
        return (new Uin2Acc($this->getServer()))->getAcc($this->message['send']);
    }

    public function getMessage(){
        return $this->message['content'];
    }

    public function getType(){
        return $this->message['type'];
    }

    public function reply($message){
        $this->getServer()->getMessageSender()->send($this->message, $message);
    }

    public function getServer(){
        return $this->server;
    }

    public function onReceive(Message $message){

    }
    
    public function onLoad(){

    }

}