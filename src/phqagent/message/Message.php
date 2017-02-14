<?php
namespace phqagent\message;

use phqagent\element\Group;
use phqagent\element\User;

class Message
{
    
    const USER = 1;
    const GROUP = 2;

    private $type;
    private $from;
    private $send;
    private $target;
    private $content;

    public function __construct($target = null, $msg = null, $autosend = false)
    {
        if ($target !== null) {
            if ($target instanceof Message) {
                $this->target = $target->getFrom();
                $this->type = $target->getType();
            } elseif ($target instanceof User) {
                $this->type = self::USER;
                $this->target = $target;
            } elseif ($target instanceof Group) {
                $this->type = self::GROUP;
                $this->target = $target;
            } else {
                throw new \Exception("$target is not a valid target");
            }
        }
        $this->content = $msg;
        if ($autosend == true) {
            $this->send();
        }
    }

    public function receive($msg)
    {
        $msg = unserialize($msg);
        switch ($msg['type']) {
            case self::USER:
                $this->type = self::USER;
                $this->from = new User($msg['from']);
                $this->send = new User($msg['send']);
                break;
            case self::GROUP:
                $this->type = self::GROUP;
                $this->from = new Group($msg['from']);
                $this->send = new User($msg['send'], $this->from);
                break;
        }
        $this->content = $msg['content'];
        return $this;
    }

    public function send()
    {
        MessageQueue::getInstance()->sendMessage($this);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getSend()
    {
        return $this->send;
    }

    public function getUser()
    {
        return $this->send;
    }

    public function getGroup()
    {
        if ($this->type == self::GROUP) {
            return $this->from;
        }
        return false;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function __toString()
    {
        return $this->content;
    }
    
    public static function init()
    {
        return 'pthread hack';
    }
}
