<?php
namespace module;
class GetSelfInfo extends Module{
    public function getInfo(){
        $json = $this->getCurl()->
	    setUrl('http://s.web2.qq.com/api/get_self_info2')->
	    setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
	    setCookie($this->getSession()->getCookie())->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json;
    }
}