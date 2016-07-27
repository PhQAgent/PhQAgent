<?php
use logger\MainLogger;
use utils\Curl;
use plugin\PluginBase;
use plugin\PluginManager;
use login\LoginHandler;
use module\MessageReceiver;
use module\MessageSender;
use module\GetSelfInfo;
use module\GetRecentList;
class Server{

    const PLUGIN_DIR = 'plugins';
    const LOG_FILENAME = 'server.log';

    public $session;

    private $basedir;
    private $pluginmanager;

    //Cache
    public $uin2acc;
    public $groupinfo;
    public $friendinfo;

    public function __construct(){
        $this->basedir = '.';
        $this->logger = new MainLogger($this);
        $this->logger->start();
        $this->logger->info("正在启动服务端...");
        $this->logger->info("正在尝试登录WebQQ...");
        $this->session = (new LoginHandler($this))->login();
        gc_collect_cycles();
        $this->logger->info("正在加载消息收发接口...");
        $this->messagesender = new MessageSender($this);
        $this->messagerecevier = new MessageReceiver($this);
        $this->logger->info("正在加载插件...");
        $this->pluginmanager = new PluginManager($this);
        $this->pluginmanager->load();
        $this->logger->info("服务端启动完成!");
        $this->run();
    }

    public function run(){
        while($this->isRunning()){
            $this->messagerecevier->receive();
        }
    }
    
    public function getLogger(){
        return $this->logger;
    }

    public function getLogFile(){
        return $this->getBaseDir().DIRECTORY_SEPARATOR.self::LOG_FILENAME;
    }

    public function getMessageSender(){
        return $this->messagesender;
    }

    public function isRunning(){
        return true;
    }

    public function getPluginManager(){
        return $this->pluginmanager;
    }

    public function getSavedSession(){
        return $this->session;
    }

    public function getCurl(){
        return $this->curl;
    }

    public function getBaseDir(){
        return $this->basedir;
    }

}