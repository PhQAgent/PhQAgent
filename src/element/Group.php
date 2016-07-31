<?php
namespace element;

class Group{

    private $uin;
    private $gid;
    private $users;
    private $name;

    private static $cache;
    
    public function __construct($uin){
        $this->uin = $uin;
    }

}