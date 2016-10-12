<?php
namespace phqagent\console\command;
use phqagent\Server;
use phqagent\console\MainLogger;
class Stop extends Command{

    protected static $name = 'stop';

    public static function onCall(Server $server, $arg){
        $server->shutdown();
    }

}