<?php
namespace element;
use login\SavedSession;
use utils\Curl;

class User{

    private $uin;
    private $account;
    private $groupnick;
    private $nick;

    public static $cache;
    /*
    [
        [$uin]=>[
            $account,
            $name,
        ]
    ]
    */
    
    public function __construct($uin){
        $this->uin = $uin;
        if(isset(static::$cache[$uin])){
            foreach(static::$cache[$uin] as $key => $value){
                $this->$key = $value;
            }
        }
    }

    public function getGroupNick(){

    }

    public function getFriendNick(){
        if(!isset($this->nick)){
            $json = (new Curl())->
            setUrl('http://s.web2.qq.com/api/get_friend_info2')->
            setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
            setGet([
                'tuin' => $this->uin,
                'vfwebqq' => SavedSession::$vfwebqq,
                'clientid' => SavedSession::$clientid,
                'psessionid' => SavedSession::$psessionid,
            ])->
            setCookie(unserialize(SavedSession::$serialized))->
            returnHeader(false)->
            setTimeOut(5)->
            exec();
            $json = json_decode($json, true);
            if(isset($json['result']['nick'])){
                $nick = $json['result']['nick'];
                static::$cache[$this->uin]['nick'] = $nick;
                $this->nick = $nick;
            }else{
                return false;
            }
        }
        return $this->nick;
    }

    public function getAccount(){
        if(!isset($this->account)){
            $json = (new Curl())->
            setUrl('http://s.web2.qq.com/api/get_friend_uin2')->
            setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
            setGet([
                'tuin' => $this->uin,
                'vfwebqq' => SavedSession::$vfwebqq,
            ])->
            setCookie(unserialize(SavedSession::$serialized))->
            returnHeader(false)->
            setTimeOut(5)->
            exec();
            $json = json_decode($json, true);
            $account = $json['result']['account'];
            static::$cache[$this->uin]['account'] = $account;
            $this->account = $account;
        }
        return $this->account;
    }

}