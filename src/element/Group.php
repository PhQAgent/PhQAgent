<?php
namespace element;
use login\SavedSession;
use utils\Curl;

class Group{

    private $uin;
    private $gid;
    private $member;
    private $name;

    public static $cache;
    
    public function __construct($uin){
        $this->uin = $uin;
        if(isset(static::$cache[$uin])){
            foreach(static::$cache[$uin] as $key => $value){
                $this->$key = $value;
            }
        }
    }

    public function getGid(){
        if(!isset($this->gid)){
            $gid = (new GroupList())->getGid($this);
            static::$cache[$this->uin]['gid'] = $gid;
            $this->gid = $gid;
        }
        return $this->gid;
    }

    public function getMember(){

    }

    public function getCard(User $user){

    }

    public function getName(){
        if(!isset($this->name)){
            $name = (new GroupList())->getName($this);
            static::$cache[$this->uin]['name'] = $name;
            $this->name = $name;
        }
        return $this->name;
    }

}