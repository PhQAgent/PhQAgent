<?php
namespace module;
class getFriendInfo extends Module{

    public function getNick($uin){
        if(!isset($this->getServer()->friendinfo[$uin])){
            $this->getServer()->friendinfo[$uin] = $this->getRealNick($uin);
        }
        return $this->getServer()->friendinfo[$uin];
    }

    private function getRealNick($uin){
        $json = $this->getCurl()->
	    setUrl('http://s.web2.qq.com/api/get_friend_info2')->
	    setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
	    setGet([
            'tuin' => $uin,
            'vfwebqq' => $this->getSession()->vfwebqq,
            'clientid' => $this->getSession()->clientid,
            'psessionid' => $this->getSession()->psessionid,
	    ])->
	    setCookie($this->getSession()->getCookie())->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json['result']['nick'];
    }

}