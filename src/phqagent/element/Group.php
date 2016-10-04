<?php
namespace phqagent\element;
use protocol\Protocol;

class Group{

    private static $cache = [];
    private $uin;
    private $gid;
    private $name;
    private $member;

    public function __construct($uin){
        $this->uin = $uin;
        if(isset(self::$cache[$uin])){
            foreach(self::$cache[$uin] as $key => $value){
                $this->$key = $value;
            }
        }
    }

    public function getUin(){
        return $this->uin;
    }

    public function getGid(){
        if($this->gid == null){
            $this->gid = GroupList::getGid($this);
            self::$cache[$this->uin]['gid'] = $this->gid;
        }
        return $this->gid;
    }

    public function getName(){
        if($this->name == null){
            $this->name = GroupList::getGroupName($this);
            self::$cache[$this->uin]['name'] = $this->name;
        }
        return $this->name;
    }

    public function getMember(){
        if($this->member == null){
            $this->refreshMember();
        }
        foreach($this->member as $uin => $name){
            $return[] = new User($uin, $this);
        }
        return $return;
    }

    public function refreshMember(){
        $this->member = Protocol::getGroupMemberList($this);
    }

    public function getCard(User $user){
        if($this->member == null){
            $this->member = Protocol::getGroupMemberList($this);
            self::$cache[$this->uin]['member'] = $this->member;
        }
        if(isset($this->member[$user->getUin()])){
            return $this->member[$user->getUin()];
        }else{
            return false;
        }
    }

}