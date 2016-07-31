<?php
namespace plugin;
use element\Message;
class PluginManager{

    private $server;
    private $plugins = [];
    private $ticks = [];

    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function doTick(){
        foreach($this->ticks as $plugin){
            $plugin->onTick();
        }
    }

    public function onMessageReceive(Message $message){
        foreach($this->plugins as $plugin){
            $plugin->onReceive($message);
        }
    }

    public function load(){
        $dir = \Server::PLUGIN_DIR;
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $dir_array = scandir($dir);
        foreach($dir_array as $file){
            $pre = explode('.', $file);
            if(isset($pre[1])){
                if($pre[1] == 'php'){
                    $this->server->getLogger()->info("尝试加载插件: {$pre[0]}");
                    include($this->server->getBaseDir().DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR."$file");
                    $plugin = new $pre[0]($this->server);
                    $this->plugins[$pre[0]] = $plugin;
                    $plugin->onLoad();
                }
            }
        }
    }

}