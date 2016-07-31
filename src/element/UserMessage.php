<?php
namespace element;

class UserMessage extends Message{

    public function __construct($msg){
        $this->type = parent::USER;
        $this->content = $msg;
    }

    public function setTarget($uin){
        $this->target = $uin;
        return $this;
    }

}