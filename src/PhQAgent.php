<?php
include "utils/ClassLoader.php";
if(version_compare("7.0", PHP_VERSION) > 0){
	echo "请使用PHP7.0及以上运行本程序" . PHP_EOL;
	exit(1);
}
if(!extension_loaded("pthreads")){
	echo "PHP运行环境缺失必要的pthread扩展" . PHP_EOL;
	exit(1);
}
date_default_timezone_set('Asia/Shanghai');
$loader = (new utils\ClassLoader(__DIR__))->register();
$server = new Server();