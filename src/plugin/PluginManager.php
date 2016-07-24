<?php
namespace plugin;

class PluginManager{

    private $server;
    private $plugins = [];

    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function tick($message){
        foreach($this->plugins as $plugin){
            $plugin->__process__($message);
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
            if($pre[1] == 'php'){
                $this->server->getLogger()->info("尝试加载插件: {$pre[0]}");
                include($this->server->getBaseDir().DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR."$file");
                $plugin = new $pre[0]($this->server);
                $this->plugins[$pre[0]] = $plugin;
                $plugin->onLoad();
            }
        }
        /*
        foreach($this->plugins as $plugin){
            $plugin->start();
        }
        */
    }

}