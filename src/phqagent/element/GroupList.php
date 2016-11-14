<?php
namespace phqagent\element;
use protocol\Protocol;

abstract class GroupList{
    private static $cache;

    public static function getGroupList(){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        if(self::$cache === false){
            return false;
        }
        foreach(self::$cache as $uin => $group){
            $return[] = new Group($uin);
        }
        return $return;
    }

    public static function getGroupName(Group $group){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        return self::$cache[$group->getUin()]['name'];
    }

    public static function getGid(Group $group){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        return self::$cache[$group->getUin()]['gid'];
    }

    public static function getGroupPermission(Group $group){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        return self::$cache[$group->getUin()]['permission'];
    }

    public static function getGroupNumber(Group $group){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        return self::$cache[$group->getUin()]['number'];
    }

    public static function getGroupOwner(Group $group){
        if(!isset(self::$cache)){
            self::$cache = Protocol::getInstance()->getGroupList();
        }
        return self::$cache[$group->getUin()]['owner'];
    }

}