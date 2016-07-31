<?php
namespace element;
use login\SavedSession;
use utils\Curl;

class Message{

    const USER = 1;
    const GROUP = 2;

    private $type = null;
    private $user = null;
    private $group = null;
    private $content = null;

    public function __construct($msg){
        if($msg !== null){
            if($msg['type'] == 'group_message'){
                $this->type = self::GROUP;
                $this->user = new User($msg['send']);
                $this->group = new Group($msg['from']);
                $this->content = $msg['content'];
            }else{
                $this->type = self::USER;
                $this->user = new User($msg['send']);
                $this->content = $msg['content'];
            }
        }
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

    public function __toString(){
        return $this->getContent();
    }

}