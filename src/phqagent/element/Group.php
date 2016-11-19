<?php
namespace phqagent\element;
use protocol\Protocol;

class Group{

    const MEMBER = 0;
    const MANAGE = 1;
    const CREATE = 2;

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

    public function setCard(User $user, $card){ 
        if($this->getPermision() >= self::MANAGE){
            return Protocol::changeGroupCard($this, $user, $card);
        }
        return false;
    }

    public function banMember(User $user, $time){
        if($this->getPermission() >= self::MANAGE){
            return Protocol::banGroupMember($this, $user, $time);
        }
        return false;
    }

    public function getPermission(){
        if($this->permission == null){
            $perm = GroupList::getGroupPermission($this);
            switch($perm){
                case 'owner': $permission = 2; break;
                case 'manage': $permission = 1; break;
                default: $permission = 0; break;
            }
            $this->permission = $permission;
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