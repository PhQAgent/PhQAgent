<?php
namespace element;
class ReplyMessage extends Message{

    public function __construct($msg){
        $this->type = $msg->getType();
        $this->target = $msg->getFrom();
    }

    public function setContent($content){
        $this->content = $content;
        return $this;
    }

}