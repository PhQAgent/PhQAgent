<?php
namespace phqagent\element;

abstract class FriendList{

    private static $cache;

    public static function getFriendList(){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getFriendList();
        }
        foreach($cache as $uin => $user){
            $return[] = new User($uin);
        }
        return $return;
    }

}