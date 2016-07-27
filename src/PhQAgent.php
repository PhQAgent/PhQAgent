<?php
include "utils/ClassLoader.php";
$loader = (new utils\ClassLoader(__DIR__))->register();
$server = new Server();