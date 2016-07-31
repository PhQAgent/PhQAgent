<?php
namespace element;

class GroupMessage extends Message{

    public function __construct($msg){
        $this->type = parent::GROUP;
        $this->content = $msg;
    }

    public function setTarget($uin){
        $this->target = $uin;
        return $this;
    }

}