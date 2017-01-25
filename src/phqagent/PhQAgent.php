<?php

namespace phqagent;

use phqagent\message\MessageQueue;
use protocol\Protocol;
use sf\console\Logger;
use sf\module\Module;

class PhQAgent extends Module{

	const VERSION = '2.3.3.2';
	const PROJECT = '青 Aoi';

    const PLUGIN_DIR = 'plugins';

    private $shutdown;
    /** @var Protocol */
    private $protocol;
    private $queue;

    public function load(){
    	define('phqagent\BASE_DIR', $this->getDataFolder());
    	@mkdir($this->getDataFolder());
		Logger::info('PhQAgent Codename: [' . self::PROJECT . '] Version: ' . self::VERSION);
        $start = time();
        $this->shutdown = false;
        $this->queue = new MessageQueue();
        Logger::info('正在初始化QQ协议...');
        $this->protocol = new Protocol();
        $this->protocol->login();
        if($this->protocol->isError()){
            $this->unload();
        }
        $final = time();
        $starttime = $final - $start;
        Logger::info("PhQAgent系统完成加载! 耗时 $starttime 秒");
    }

    public function unload(){
        Logger::warning("服务器即将关闭");
        try{
            $this->shutdown = true;
            $this->protocol->shutdown();
        }catch(\Error $e){
            
        }
    }

    public function getProtocol(){
        return $this->protocol;
    }

}