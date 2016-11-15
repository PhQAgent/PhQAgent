<?php
namespace phqagent\element;
use protocol\Protocol;

class Group{

    private static $cache = [];
    private $uin;
    private $gid;
    private $number;
    private $name;
    private $permission;
    private $owner;
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

    public function banMember(User $user, $time){
        return Protocol::banGroupMember($this, $user, $time);
    }

    public function getPermission(){
        if($this->permission == null){
            $this->permission = GroupList::getGroupPermission($this);
            self::$cache[$this->uin]['permission'] = $this->permission;
        }
        return $this->permission;
    }

    public function getOwner(){
        if($this->owner == null){
            $this->owner = GroupList::getGroupOwner($this);
            self::$cache[$this->uin]['owner'] = $this->owner;
        }
        return $this->owner;
    }

    public function getNumber(){
        if($this->number == null){
            $this->number = GroupList::getGroupNumber($this);
            self::$cache[$this->uin]['number'] = $this->number;
        }
        return $this->number;
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