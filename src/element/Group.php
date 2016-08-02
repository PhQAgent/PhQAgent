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

    public function getUin(){
        return $this->uin;
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
        $this->generateData();
        $list = [];
        foreach($this->member as $uin => $card){
            $list[] = new User($uin);
        }
        return $list;
    }

    public function getCard(User $user){
        if(!isset($this->member[$user->getUin()])){
            unset($this->member);
        }
        $this->generateData();
        return $this->member[$user->getUin()];
    }

    public function getName(){
        if(!isset($this->name)){
            $name = (new GroupList())->getName($this);
            static::$cache[$this->uin]['name'] = $name;
            $this->name = $name;
        }
        return $this->name;
    }

    private function generateData(){
        if(!isset($this->member)){
            $json = (new Curl())->
            setUrl('http://s.web2.qq.com/api/get_group_info_ext2')->
            setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
            setGet([
                'gcode' => $this->getGid(),
                'vfwebqq' => SavedSession::$vfwebqq
            ])->
            setCookie(unserialize(SavedSession::$serialized))->
            returnHeader(false)->
            setTimeOut(5)->
            exec();
            $json = json_decode($json, true);
            if(!is_array($json['result']['minfo'])){
                return false;
            }
            $nick = [];
            foreach($json['result']['minfo'] as $member){
                $nick[$member['uin']] = $member['nick'];
                User::$cache[$member['uin']]['nick'] = $member['nick'];
                User::$cache[$member['uin']]['card'][$this->uin] = $member['nick'];
            }
            
            if(isset($json['result']['cards'])){
                foreach($json['result']['cards'] as $member){
                    $nick[$member['muin']] = $member['card'];
                    User::$cache[$member['muin']]['card'][$this->uin] = $member['card'];
                }
            }

            $this->member = $nick;
        }
    }

}