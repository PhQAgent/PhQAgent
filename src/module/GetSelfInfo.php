<?php
namespace module;
use login\SavedSession;
use utils\Curl;

class GetSelfInfo{
    public function getInfo(){
        $json = (new Curl())->
	    setUrl('http://s.web2.qq.com/api/get_self_info2')->
	    setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
	    setCookie(SavedSession::$cookie)->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json;
    }
}