<?php
namespace module;
use utils\Curl;
abstract class Module{
    protected $server;
    protected $curl = null;
    protected $cookie;
    
    public function __construct(\Server $server){
        $this->server = $server;
    }

    public function getServer(){
        return $this->server;
    }
    
    public function getCurl(){
        if($this->curl == null){
            $this->curl = new Curl();
        }
        return $this->curl;
    }

    public function getSession(){
        return $this->server->getSavedSession();
    }

}