<?php
namespace element;
use utils\Curl;
use login\SavedSession;
class GroupList{
    private static $cache;

    public function __construct(){
        $this->getRealGroupMap();
    }

    private function getRealGroupMap(){
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
        return $map;
    }
}