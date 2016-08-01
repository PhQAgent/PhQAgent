<?php
namespace utils;
class MainLogger extends \Thread{

    private $server;
    private $file;
    private $log;

    public function __construct(\Server $server){
        $this->server = $server;
        $this->file = $server->getLogFile();
        $this->log = [];
        new TextFormat();//pthread hack
    }

    public function run(){
        while($this->server->isRunning()){
            if(count($this->log) !== 0){
                $tmp = (array)$this->log;
                foreach($tmp as $key => $log){
                    echo $log;
                    file_put_contents($this->file, TextFormat::clean($log), FILE_APPEND);
                    unset($this->log[$key]);
                }
            }
            usleep(100);
        }
    }

    public function info($log){
        $class = debug_backtrace()[1]['class'];
        $log = TextFormat::AQUA . $this->getTime() . TextFormat::WHITE . "[INFO $class] $log" . PHP_EOL;
        $this->log[] = $log;
    }

	public function success($log){
        $class = debug_backtrace()[1]['class'];
        $log = TextFormat::AQUA . $this->getTime() . TextFormat::GREEN . "[INFO $class] $log" . PHP_EOL;
        $this->log[] = $log;
	}

    private function getTime(){
        return date('[G:i:s]');
    }

}