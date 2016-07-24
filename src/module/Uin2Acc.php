<?php
namespace module;
class Uin2Acc extends Module{

    public function getAcc($uin){
        if(!isset($this->getServer()->uin2acc[$uin])){
            $this->getServer()->uin2acc[$uin] = $this->getRealUin($uin);
        }
        return $this->getServer()->uin2acc[$uin];
    }

    private function getRealUin($uin){
        $json = $this->getCurl()->
	    setUrl('http://s.web2.qq.com/api/get_friend_uin2')->
	    setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
	    setGet([
            'tuin' => $uin,
            'vfwebqq' => $this->getSession()->vfwebqq,
	    ])->
	    setCookie($this->getSession()->getCookie())->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json['result']['account'];
    }

}