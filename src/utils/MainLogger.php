<?php
namespace utils;
class MainLogger extends \Thread{

    private $server;
    private $file;
    private $log;

    public static $instance;

    public function __construct(\Server $server){
        $this->server = $server;
        $this->file = $server->getLogFile();
        MainLogger::$instance = $this;
        new TextFormat();//pthread hack
    }

    public static function getInstance(){
        return MainLogger::$instance;
    }

    public function run(){
        while($this->server->isRunning()){
            if(strlen($this->log) > 0){
                $log = $this->log;
                file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
                $this->log = substr($this->log, strlen($log), strlen($this->log));
            }
            usleep(10);
        }
    }

    public function info($log){
        $class = debug_backtrace()[1]['class'];
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::WHITE . "[INFO $class] $log" . PHP_EOL;
        echo $log;
        $this->log .= $log;
    }

	public function success($log){
        $class = debug_backtrace()[1]['class'];
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::GREEN . "[INFO $class] $log" . PHP_EOL;
        echo $log;
        $this->log .= $log;
	}
    
	public function warning($log){
        $class = debug_backtrace()[1]['class'];
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::YELLOW . "[INFO $class] $log" . PHP_EOL;
        echo $log;
        $this->log .= $log;
	}

    public function alert($log){
        $class = debug_backtrace()[1]['class'];
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::RED . "[INFO $class] $log" . PHP_EOL;
        echo $log;
        $this->log .= $log;
	}
}