<?php
namespace plugin;
use phqagent\plugin\PluginBase;
use phqagent\message\Message;
use phqagent\console\MainLogger;

class Demo extends PluginBase{

    public function onLoad(){
        MainLogger::success('Demo插件已加载');
    }

    public function onMessageReceive(Message $msg){
        MainLogger::info($msg);
        if($msg->getContent() == "test"){
            new Message($msg, $msg->getUser()->getCard() . " hello world", true);
        }
    }

}
