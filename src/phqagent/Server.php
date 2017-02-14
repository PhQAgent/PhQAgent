<?php
namespace phqagent;

use phqagent\console\MainLogger;
use phqagent\message\MessageQueue;
use phqagent\plugin\PluginManager;
use protocol\Protocol;
use phqagent\console\CommandManager;

class Server
{

    const PLUGIN_DIR = 'plugins';

    private $shutdown;
    private $logger;
    private $protocol;
    private $queue;
    private $console;

    public function __construct(MainLogger $logger)
    {
        $start = time();
        $this->shutdown = false;
        $this->logger = $logger;
        $this->console = new CommandManager($this);
        $this->queue = new MessageQueue();
        MainLogger::info('正在加载插件...');
        $this->plugin = new PluginManager($this);
        $this->plugin->load();
        MainLogger::info('正在初始化QQ协议...');
        $this->protocol = new Protocol();
        $this->protocol->login();
        if ($this->protocol->isError()) {
            $this->shutdown();
        }
        $final = time();
        $starttime = $final - $start;
        MainLogger::info("PhQAgent系统完成加载! 耗时 $starttime 秒");
        $this->main();
    }

    public function main()
    {
        while (!$this->shutdown) {
            $this->plugin->doTick();
            $this->console->doTick();
            usleep(50000);
        }
    }

    public function shutdown()
    {
        MainLogger::warning("服务器即将关闭");
        try {
            $this->shutdown = true;
            $this->plugin->shutdown();
            $this->protocol->shutdown();
            $this->console->shutdown();
            MainLogger::getInstance()->shutdown();
        } catch (\Error $e) {
        }
        exit(0);
    }

    public function getPluginManager()
    {
        return $this->plugin;
    }

    public function getConfig($key)
    {
        return $this->config['key'];
    }

    public function getPluginDir()
    {
        return \phqagent\BASE_DIR . DIRECTORY_SEPARATOR . self::PLUGIN_DIR;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }
}
