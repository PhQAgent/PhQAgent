<?php
namespace phqagent\console\command;
use phqagent\Server;

abstract class Command{

    protected static $name = '';

    public static function getCommand(){
        return static::$name;
    }

    public static function onCommand(Server $server, $args){
        
    }

}