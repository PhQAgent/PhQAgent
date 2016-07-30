<?php
namespace element;

class Message{

    const GROUP = 1;
    const USER = 2;

    private $type = null;
    private $user = null;
    private $group = null;
    private $content = null;

    public function __construct($msg){

    }


}