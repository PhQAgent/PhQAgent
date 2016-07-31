<?php
namespace module;
class GroupInfo extends Module{
    
    public function getNickNameList($gid){
        if(!isset($this->getServer()->groupinfo['nick'][$gid])){
            $this->getServer()->groupinfo['nick'][$gid] = $this->getRealGroupInfo($gid);
        }
        return isset($this->getServer()->groupinfo['nick'][$gid]) ? $this->getServer()->groupinfo['nick'][$gid] : [];
    }

    public function getGid($gid){
        if(!isset($this->getServer()->groupinfo['map'][$gid])){
            $this->getServer()->groupinfo['map'] = $this->getRealGroupMap();
        }
        return isset($this->getServer()->groupinfo['map'][$gid]) ? $this->getServer()->groupinfo['map'][$gid] : [];
    }



    private function getRealGroupInfo($gid){
        $json = $this->getCurl()->
	    setUrl('http://s.web2.qq.com/api/get_group_info_ext2')->
	    setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setGet([
            'gcode' => $gid,
            'vfwebqq' => $this->getSession()->vfwebqq,
            't' => 1469030147542
        ])->
	    setCookie($this->getSession()->getCookie())->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        $user = $json['result']['minfo'];
        $nick = [];
        foreach($json['result']['minfo'] as $member){
            $nick[$member['uin']] = $member['nick'];
        }
        foreach($json['result']['cards'] as $member){
            $nick[$member['muin']] = $member['card'];
        }
        return $nick;
    }

}