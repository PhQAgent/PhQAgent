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

    public static function info($log, $color = TextFormat::WHITE){
        self::show('INFO', $log, $color);
    }

	public static function success($log, $color = TextFormat::GREEN){
        self::show('SUCC', $log, $color);
	}

    public static function fail($log, $color = TextFormat::RED){
        self::show('FAIL', $log, $color);
	}
    
	public static function warning($log, $color = TextFormat::YELLOW){
         self::show('WARN', $log, $color);
	}

    public static function alert($log, $color = TextFormat::RED){
        self::show('ALER', $log, $color);
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

    private static function show($pre, $log, $color){
        $class = @end(explode('\\', debug_backtrace()[2]['class']));
        if($class == ''){
            $class = "Thread";
        }
        $log = TextFormat::AQUA . date('[G:i:s]') . $color . "[$pre $class] $log" . TextFormat::RESET . PHP_EOL;
        echo $log;
        self::getInstance()->log[] = TextFormat::clean($log);
    }
    
    public function shutdown(){
        $this->shutdown = true;
    }

}