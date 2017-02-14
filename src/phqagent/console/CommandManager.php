<?php
namespace phqagent\console;

use phqagent\Server;

use phqagent\console\command\Stop;

class CommandManager
{

    private static $instance;
    private static $command = [];
    private $server;
    private $reader;

    public function __construct(Server $server)
    {
        self::$instance = $this;
        $this->server = $server;
        $this->init();
        $this->reader = new CommandReader();
    }

    private function init()
    {
        $this->register(Stop::getCommand(), new Stop());
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function register($command, $class)
    {
        self::$command[$command] = $class;
    }

    public function doTick()
    {
        while (count($this->reader->buffer) > 0) {
            $command = $this->reader->buffer->shift();
            $args = preg_split("/[\s,]+/", $command);
            $name = $args[0];
            if (!isset(self::$command[$name])) {
                MainLogger::alert("命令 $name 不存在!");
                return ;
            }
            self::$command[$name]->onCommand($this->server, $args);
        }
    }

    public function shutdown()
    {
        $this->reader->shutdown();
        stream_set_blocking($this->reader->stdin, 0);
    }
}
