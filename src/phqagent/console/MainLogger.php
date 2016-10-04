<?php
namespace phqagent\console;

class MainLogger extends \Thread{

    private static $instance;
    private static $logfile;
    private $log;
    private $shutdown;

    public function __construct($logfile){
        $this->shutdown = false;
        self::$instance = $this;
        self::$logfile = $logfile;
        $this->log = new \Threaded;
    }

    public static function getInstance(){
        return self::$instance;
    }

    public static function info($log){
        $class = @end(explode('\\', debug_backtrace()[1]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::WHITE . "[INFO $class] $log" . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
    }

	public static function success($log){
        $class = @end(explode('\\', debug_backtrace()[1]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::GREEN . "[SUCC $class] $log" . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
	}

    public static function fail($log){
        $class = @end(explode('\\', debug_backtrace()[1]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::RED . "[FAIL $class] $log" . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
	}
    
	public static function warning($log){
        $class = @end(explode('\\', debug_backtrace()[1]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::YELLOW . "[WARN $class] $log" . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
	}

    public static function alert($log){
        $class = @end(explode('\\', debug_backtrace()[1]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . TextFormat::RED . "[ALER $class] $log" . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
	}

    public function run(){
        while(!$this->shutdown){
            while(count($this->log) > 0){
                $log = $this->log->shift();
                file_put_contents(self::$logfile, $log, FILE_APPEND);
            }
            sleep(1);
        }
    }

    public function shutdown(){
        self::$instance = null;
        $this->shutdown = true;
    }
}