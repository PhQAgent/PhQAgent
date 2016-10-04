<?php
namespace phqagent;
use phqagent\console\MainLogger;
use phqagent\message\MessageQueue;
use phqagent\plugin\PluginManager;
use protocol\ProtocolHandler;
class Server{

    const PLUGIN_DIR = 'plugins';

    private $shutdown;
    private $logger;
    private $protocol;
    private $queue;

    public function __construct(MainLogger $logger){
        $start = time();
        $this->shutdown = false;
        $this->logger = $logger;
        $this->queue = new MessageQueue();
        MainLogger::info('正在加载插件...');
        $this->plugin = new PluginManager($this);
        $this->plugin->load();
        ProtocolHandler::use(ProtocolHandler::WebQQ);
        $this->protocol = new \protocol\Protocol();
        $this->protocol->login();
        $final = time();
        $starttime = $final - $start;
        MainLogger::info("PhQAgent系统完成加载! 耗时 $starttime 秒");
        $this->main();
    }

    public function main(){
        while(!$this->shutdown){
            $this->plugin->doTick();
            usleep(100);
        }
    }

    public function getConfig($key){
        return $this->config['key'];
    }

    public function getPluginDir(){
        return \phqagent\BASE_DIR . DIRECTORY_SEPARATOR . self::PLUGIN_DIR;
    }

    public function getProtocol(){
        return $this->protocol;
    }

}