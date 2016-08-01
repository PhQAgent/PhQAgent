<?php
namespace element;
use login\SavedSession;
use utils\Curl;

class GroupList{

    public static $cache;
    private $map;
    private $uin_name;
    private $uin_gid;

    public function __construct(){
        if(isset(static::$cache)){
            foreach(static::$cache as $key => $value){
                $this->$key = $value;
            }
        }
        $this->generateData();
    }

    public function getGroupList(){
        $list = [];
        foreach($this->uin_name as $uin => $name){
            $list[] = new Group($uin);
        }
        return $list;
    }

    public function getGid(Group $group){
        if(!isset($this->uin_gid[$group->getUin()])){
            unset($this->map);
            $this->generateData();
        }
        return $this->uin_gid[$group->getUin()];
    }

    public function getName(Group $group){
        return $this->uin_name[$group->getUin()];
    }

    private function generateData(){
        if(!isset($this->map)){
            $json = (new Curl())->
            setUrl('http://s.web2.qq.com/api/get_group_name_list_mask2')->
            setReferer('http://d1.web2.qq.com/proxy.html?v=20130916001')->
            setPost([
                'r' => json_encode([
                    'vfwebqq' => SavedSession::$vfwebqq,
                    'hash' => SavedSession::$hash,
                ], JSON_FORCE_OBJECT)
            ])->
            setCookie(unserialize(SavedSession::$serialized))->
            returnHeader(false)->
            setTimeOut(5)->
            exec();
            $json = json_decode($json, true);
            $map = [];
            foreach($json['result']['gnamelist'] as $namelist){
                $map[$namelist['gid']] = [
                    'gid' => $namelist['code'],
                    'name' => $namelist['name'],
                ];
            }
            static::$cache['map'] = $map;
            $this->map = $map;
            
            $map = [];
            foreach($json['result']['gnamelist'] as $namelist){
                $map[$namelist['gid']] = $namelist['code'];
                Group::$cache[$namelist['gid']]['gid'] = $namelist['code'];
            }
            static::$cache['uin_gid'] = $map;
            $this->uin_gid = $map;

            $map = [];
            foreach($json['result']['gnamelist'] as $namelist){
                $map[$namelist['gid']] = $namelist['name'];
                Group::$cache[$namelist['gid']]['name'] = $namelist['name'];
            }
            static::$cache['uin_name'] = $map;
            $this->uin_name = $map;
        }
    }
}