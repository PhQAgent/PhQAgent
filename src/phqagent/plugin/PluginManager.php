<?php
namespace phqagent\plugin;
use phqagent\Server;
use phqagent\console\MainLogger;
use phqagent\message\MessageQueue;

class PluginManager{

    const CallBack = 1;
    const TimeCallBack = 2;
    private $server;
    private $plugins;
    private $callbacks;
    private $timecallbacks;

    public function __construct(Server $server){
        $this->server = $server;
        $this->plugins = [];
        $this->callbacks = [];
        $this->timecallbacks = [];
    }

    public function doTick(){
        foreach($this->timecallbacks as $key => $callback){
            if(time() >= $callback[0]){
                unset($this->timecallbacks[$key]);
                $callback[1]->onCallback(PluginManager::TimeCallBack);
            }
        }
        foreach($this->callbacks as $plugin){
            $plugin->onCallback(PluginManager::CallBack);
        }
        if($message = MessageQueue::getInstance()->getMessage()){
            foreach($this->plugins as $plugin){
                $plugin->onMessageReceive($message);
            }
        }
    }

    public function shutdown(){
        foreach($this->plugins as $plugin){
            $plugin->onShutdown();
        }
    }
    
    public function registerCallback($class){
        if(!in_array($class, $this->callbacks)){
            $this->callbacks[] = $class;
        }
    }

    public function unregisterCallback($class){
        foreach($this->callbacks as $key => $c){
            if($c === $class){
                unset($this->callbacks[$key]);
            }
        }
    }

    public function registerTimeCallback($time, $class){
        $this->timecallbacks[] = [$time, $class];
    }

    public function unregisterTimeCallback($time, $class){
        foreach($this->timecallbacks as $key => $cb){
            if($cb[0] === $time && $cb[1] === $class){
                unset($this->timecallbacks[$key]);
            }
        }
    }

    public function load(){
        $dir = $this->server->getPluginDir();
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $dir_array = scandir($dir);
        foreach($dir_array as $file){
            $pre = explode('.', $file);
            if(isset($pre[1])){
                if($pre[1] == 'php'){
                    MainLogger::info("尝试加载插件: {$pre[0]}");
                    include($dir . DIRECTORY_SEPARATOR . "$file");
                    $plg_class = "plugin\\{$pre[0]}";
                    $plugin = new $plg_class($this);
                    $this->plugins[$pre[0]] = $plugin;
                    $plugin->onLoad();
                }
            }
        }
    }

    public function getServer(){
        return $this->server;
    }

}