<?php
namespace phqagent\plugin;
use phqagent\Server;
use phqagent\message\Message;
class PluginBase{

    protected static $instance;
    protected $manager;

    public function __construct(PluginManager $server){
        $this->manager = $manager;
        self::$instance = $this;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function getServer(){
        return $this->manager->getServer();
    }

    public function getManager(){
        return $this->manager;
    }

    public function getDataDir(){
        return $this->getRealDataDir . DIRECTORY_SEPARATOR;
    }

    public function getRealDataDir(){
        $dir = $this->server->getPluginsDir() . DIRECTORY_SEPARATOR . __CLASS__;
        if(!file_exists($dir)){
            mkdir($dir);
        }
        return $dir;
    }

    public function registerTimeCallback($time){
        $this->manager->registerTimeCallback($time, $this);
    }

    public function unregisterTimeCallback($time){

    }

    public function registerCallback(){
        $this->manager->registerCallback($this);
    }

    public function unregisterCallback(){

    }

    public function onLoad(){

    }

    public function onMessageReceive(Message $message){

    }

    public function onCallback($type){

    }

}