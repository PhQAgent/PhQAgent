<?php
namespace module;
use login\SavedSession;
use utils\Curl;

class GetRecentList{
    public function getRecentList(){

        $json = (new Curl())->
	    setUrl('http://d1.web2.qq.com/channel/get_recent_list2')->
	    setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setPost([
            'r' => json_encode([
                'vfwebqq' => SavedSession::$vfwebqq,
                'clientid' => SavedSession::$clientid,
                'psessionid' => SavedSession::$psessionid,
            ], JSON_FORCE_OBJECT)
        ])->
	    setCookie(SavedSession::$cookie)->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json;
    }
}