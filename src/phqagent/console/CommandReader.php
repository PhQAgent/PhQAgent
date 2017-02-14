<?php
namespace phqagent\console;

class CommandReader extends \Thread
{

    private static $instance;
    public $buffer;
    private $shutdown;
    public $stdin;

    public function __construct()
    {
        self::$instance = $this;
        $this->stdin = fopen("php://stdin", "r");
        $this->buffer = new \Threaded;
        $this->shutdown = false;
        $this->start();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function shutdown()
    {
        $this->shutdown = true;
    }

    public function run()
    {
        stream_set_blocking($this->stdin, 1);
        while (!$this->shutdown) {
            $cmd = fgets($this->stdin);
            if (trim($cmd) !== '') {
                $this->buffer[] = str_replace(PHP_EOL, '', $cmd);
            }
        }
    }
}
