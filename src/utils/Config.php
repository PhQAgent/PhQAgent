<?php
namespace utils;
use login\LoginHandler;
class Config{

    public static function loadConf($file){
        $conf = file_get_contents($file);
        preg_match_all('/(.*)=(.*)$/m', $conf, $conf);
        foreach($conf[1] as $key => $value){
            $config[trim($value)] = trim($conf[2][$key]);
        }
        LoginHandler::$httpdport = isset($config['server-port']) ? (int)$config['server-port'] : 8023;
        LoginHandler::$httpdip = isset($config['server-ip']) ? $config['server-ip'] : '0.0.0.0';
    }

    public static function createConf($file){
        $conf .= 'server-port=8023' . PHP_EOL;
        $conf .= 'server-ip=0.0.0.0' . PHP_EOL;
        file_put_contents($file, $conf);
    }

}