<?php

namespace phqagent\utils;

class Properties
{
    private $file;
    private $data;

    public function __construct($file)
    {
        $this->file = $file;
        $this->data = [];
        if (file_exists($this->file)) {
            $this->load();
        }
    }

    private function load()
    {
        $content = file_get_contents($this->file);
        $match = [];
        preg_match_all('/^(.*)=(.*)$/mU', $content, $match);
        foreach ($match[1] as $k => $key) {
            $this->data[trim($key)][] = trim($match[2][$k]);
        }
    }

    public function save()
    {
        $string = '';
        foreach ($this->data as $key => $values) {
            if (is_array($values) && count($values) > 0) {
                foreach ($values as $v) {
                    $string .= "$key = $v" . PHP_EOL;
                }
            }
        }
        file_put_contents($this->file, $string);
    }

    public function get($key, $override = false)
    {
        if (isset($this->data[$key])) {
            if ($override) {
                return end($this->data[$key]);
            } else {
                return $this->data[$key];
            }
        }
        return false;
    }

    public function add($key, $value)
    {
        $this->data[$key][] = $value;
    }

    public function set($key, $value, $order = 0)
    {
        $this->data[$key][$order] = $value;
    }

    public function del($key, $order = 0)
    {
        unset($this->data[$key][$order]);
    }
}
