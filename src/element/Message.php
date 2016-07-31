<?php
namespace element;
use login\SavedSession;
use utils\Curl;

class Message{

    const USER = 1;
    const GROUP = 2;

    protected $type = null;
    protected $user = null;
    protected $group = null;
    protected $from = null;
    protected $target = null;
    protected $content = null;

    public function __construct($msg){
        if($msg !== null){
            if($msg['type'] == 'group_message'){
                $this->type = self::GROUP;
                $this->from = $msg['from'];
                $this->group = new Group($msg['from']);
                $this->user = new User($msg['send']);
                $this->content = $msg['content'];
            }else{
                $this->type = self::USER;
                $this->from = $msg['from'];
                $this->user = new User($msg['send']);
                $this->content = $msg['content'];
            }
        }
    }

    public function getType(){
        return $this->type;
    }

    public function getFrom(){
        return $this->from;
    }

    public function getTarget(){
        return $this->target;
    } 

    public function getUser(){
        return $this->user;
    }

    public function getGroup(){
        return $this->group;
    }

    public function getContent(){
        return $this->content;
    }

}