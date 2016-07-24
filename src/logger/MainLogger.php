<?php
namespace logger;

class MainLogger extends \Thread{

    private $server;
    private $file;
    private $log;

    public function __construct(\Server $server){
        $this->server = $server;
        $this->file = $server->getLogFile();
        $this->log = [];
    }

    public function run(){
        while($this->server->isRunning()){
            if(count($this->log) !== 0){
                $tmp = (array)$this->log;
                foreach($tmp as $key => $log){
                    echo $log;
                    $this->write($log);
                    unset($this->log[$key]);
                }
            }
            sleep(1);//or cpu thread used up....
        }
    }

    public function info($log){
        $log = $this->getTime()."[info] $log\n";
        $this->log[] = $log;
    }

    private function getTime(){
        return date('[G:i:s]');
    }

    private function write($log){
        file_put_contents($this->file, $log, FILE_APPEND);
    }

}