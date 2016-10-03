<?php
namespace phqagent\element;
use protocol\Protocol;
class User{

    private static $cache = [];

    private $uin;
    private $account;
    private $nick;
    private $defaultgroup;

    public function __construct($uin, $defaultgroup = null){
        $this->uin = $uin;
        $this->defaultgroup = $defaultgroup;
        if(isset(self::$cache[$uin])){
            foreach(self::$cache[$uin] as $key => $value){
                $this->$key = $value;
            }
        }
    }

    public function getUin(){
        return $this->uin;
    }

    public function getAccount(){
        if($this->account == null){
            $this->account = Protocol::getInstance()->getUserAccount($this);
            self::$cache[$this->uin]['account'] = $this->account;
        }
        return $this->account;
    }

    public function getNick(){
        if($this->nick == null){
            $this->nick = Protocol::getInstance()->getFriendNick($this);
            self::$cache[$this->uin]['nick'] = $this->nick;
        }
        return $this->nick;
    }

    public function getCard(Group $group = null){
        if($group == null){
            $group = $this->defaultgroup;
        }
        return $group->getCard($this);
    }

}