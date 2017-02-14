<?php

namespace phqagent{
    
    const VERSION = '2.3.4.1';
    const PROJECT = '青 Aoi';

    if (version_compare("7.0", PHP_VERSION) > 0) {
        echo "请使用PHP7.0及以上解释器运行本程序" . PHP_EOL;
        exit(1);
    }
    if (!extension_loaded("pthreads")) {
        echo "PHP运行环境缺失必要的pthread扩展" . PHP_EOL;
        exit(1);
    }
    if (!extension_loaded("sockets")) {
        echo "PHP运行环境缺失必要的sockets扩展" . PHP_EOL;
        exit(1);
    }
    if (!extension_loaded("curl")) {
        echo "PHP运行环境缺失必要的curl扩展" . PHP_EOL;
        exit(1);
    }
    
    date_default_timezone_set('Asia/Shanghai');
    include "ClassLoader.php";
    $loader = new ClassLoader();
    $loader->addpath(dirname(__DIR__));
    $loader->register();
    define('phqagent\\BASE_DIR', str_replace('phar://', '', dirname(dirname(__DIR__))));
    $logger = new console\MainLogger('server.log');
    $logger->start();
    console\MainLogger::info('PhQAgent Codename: [' . PROJECT . '] Version: ' . VERSION);
    $server = new Server($logger, \phqagent\BASE_DIR);
}
