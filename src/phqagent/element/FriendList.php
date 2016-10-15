<?php
namespace phqagent\element;
use protocol\Protocol;

abstract class FriendList{

    private static $cache;

    public static function getFriendList(){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getFriendList();
        }
        if(self::$cache === false){
            return false;
        }
        foreach(self::$cache as $uin => $user){
            $return[] = new User($uin);
        }
        return $return;
    }

    public static function getFriendMark(User $user){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getFriendList();
        }
        if(isset(self::$cache[$user->getUin()])){
            return self::$cache[$user->getUin()]['mark'];
        }
        return false;
    }

    public static function getFriendCategorie(User $user){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getFriendList();
        }
        if(isset(self::$cache[$user->getUin()])){
            return self::$cache[$user->getUin()]['categorie'];
        }
        return false;
    }

    public static function getUserbyMark($mark){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getFriendList();
        }
        foreach(self::$cache as $uin => $user){
            if($user['mark'] === $mark){
                return new User($uin);
            }
        }
    }

}