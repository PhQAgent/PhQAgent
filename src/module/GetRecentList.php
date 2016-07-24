<?php
namespace module;
class GetRecentList extends Module{
    public function getRecentList(){
        $json = $this->getCurl()->
	    setUrl('http://d1.web2.qq.com/channel/get_recent_list2')->
	    setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setPost([
            'r' => json_encode([
                'vfwebqq' => $this->getSession()->vfwebqq,
                'clientid' => $this->getSession()->clientid,
                'psessionid' => $this->getSession()->psessionid,
            ], JSON_FORCE_OBJECT)
        ])->
	    setCookie($this->getSession()->getCookie())->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json;
    }
}