<?php
namespace protocol\method;

use protocol\SavedSession;
use phqagent\utils\Curl;

abstract class Method{

    private static $error = false;

    public static function isError(){
        return self::$error;
    }

    public static function getSelfInfo(){
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

    public static function getRecentList(){
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

    public static function getOnlineBuddies(){
        $json = (new Curl())->
        setUrl('http://d1.web2.qq.com/channel/get_online_buddies2')->
        setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setGet([
            'vfwebqq' => SavedSession::$vfwebqq,
            'clientid' => SavedSession::$clientid,
            'psessionid' => SavedSession::$psessionid,
        ])->
	    setCookie(SavedSession::$cookie)->
	    returnHeader(false)->
	    setTimeOut(5)->
	    exec();
	    $json = json_decode($json, true);
        return $json;
    }

    public static function uin2acc($uin){
        $json = (new Curl())->
        setUrl('http://s.web2.qq.com/api/get_friend_uin2')->
        setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setGet([
            'tuin' => $uin,
            'vfwebqq' => SavedSession::$vfwebqq,
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $json = json_decode($json, true);
        return $json['result']['account'];
    }

    public static function getFriendNick($uin){
        $json = (new Curl())->
        setUrl('http://s.web2.qq.com/api/get_friend_info2')->
        setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setGet([
            'tuin' => $uin,
            'vfwebqq' => SavedSession::$vfwebqq,
            'clientid' => SavedSession::$clientid,
            'psessionid' => SavedSession::$psessionid,
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $json = json_decode($json, true);
        if(isset($json['result']['nick'])){
            return $json['result']['nick'];
        }else{
            return false;
        }
    }

    public static function banGroupMember($gc, $member, $time){
        $json = (new Curl())->
        setUrl('http://qinfo.clt.qq.com/cgi-bin/qun_info/set_group_shutup')->
        setReferer('http://qinfo.clt.qq.com/qinfo_v3/member.html')->
        setPost([
            'gc' => $gc,
            'shutup_list' => "[{\"t\":$time,\"uin\":$member}]",
            'bkn' => SavedSession::$bkn
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $data = json_decode($json, true);
        if(isset($data['ec']) && $data['ec'] == 0){
            return true;
        }
        return false;
    }

    public static function changeGroupCard($gc, $member, $card){
        $json = (new Curl())->
        setUrl('http://qun.qq.com/cgi-bin/qun_mgr/set_group_card')->
        setReferer('http://qun.qq.com/member.html')->
        setPost([
            'gc' => $gc,
            'u' => $member,
            'name' => $card,
            'bkn' => SavedSession::$bkn
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $data = json_decode($json, true);
        if(isset($data['ec']) && $data['ec'] == 0){
            return true;
        }
        return false;
    }

    public static function getGroupList(){
        $list1 = self::getGroupListbyWebQQ();
        if($list1 === false){
            return false;
        }
        $list2 = self::getGroupListbyWebQun();
        foreach($list1 as $key => $group){
            if(isset($list2[$list1[$key]['name']])){
                $list1[$key] = array_merge($list1[$key], $list2[$list1[$key]['name']]);
            }
        }
        return $list1;
    }

    private static function getGroupListbyWebQQ(){
        $json = (new Curl())->
        setUrl('http://s.web2.qq.com/api/get_group_name_list_mask2')->
        setReferer('http://d1.web2.qq.com/proxy.html?v=20130916001')->
        setPost([
            'r' => json_encode([
                'vfwebqq' => SavedSession::$vfwebqq,
                'hash' => SavedSession::$hash,
            ], JSON_FORCE_OBJECT)
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $json = json_decode($json, true);
        $map = [];
        if(!isset($json['result']['gnamelist']) || !is_array($json['result']['gnamelist'])){
            self::$error = true;
            return false;
        }
        foreach($json['result']['gnamelist'] as $namelist){
            $map[$namelist['gid']] = [
                'gid' => $namelist['code'],
                'name' => str_replace("\xc2\xa0", ' ', $namelist['name']),
            ];
        }
        return $map;
    }

    private static function getGroupListbyWebQun(){
        $json = (new Curl())->
        setUrl('http://qun.qq.com/cgi-bin/qun_mgr/get_group_list')->
        setReferer('http://qun.qq.com/member.html')->
        setPost([
            'bkn' => SavedSession::$bkn
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $data = json_decode($json, true);
        $return = [];
        foreach($data as $permission => $list){
            if(is_array($list)){
                foreach($list as $group){
                    $return[html_entity_decode(str_replace('&nbsp;', ' ', $group['gn']))] = [
                        'permission' => $permission,
                        'number' => $group['gc'],
                        'owner' => $group['owner']
                    ];
                }
            }
        }
        return $return;
    }

    public static function getGroupMemberList($gid){
        $json = (new Curl())->
        setUrl('http://s.web2.qq.com/api/get_group_info_ext2')->
        setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setGet([
            'gcode' => $gid,
            'vfwebqq' => SavedSession::$vfwebqq
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $json = json_decode($json, true);
        if(!is_array($json['result']['minfo'])){
            return false;
        }
        $nick = [];
        foreach($json['result']['minfo'] as $member){
            $nick[$member['uin']] = str_replace("\xE2\x80\xAE", '', $member['nick']);
        }
        
        if(isset($json['result']['cards'])){
            foreach($json['result']['cards'] as $member){
                $nick[$member['muin']] = str_replace("\xE2\x80\xAE", '', $member['card']);
            }
        }
        return $nick;
    }
    
    public static function getFriendList(){
        $json = (new Curl())->
        setUrl('http://s.web2.qq.com/api/get_user_friends2')->
        setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setPost([
            'r' => json_encode([
                'vfwebqq' => SavedSession::$vfwebqq,
                'hash' => SavedSession::$hash
            ])
        ])->
        setCookie(SavedSession::$cookie)->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        $data = json_decode($json, true);
        $rs = [];
        if(isset($data['result']['friends'])){
            foreach($data['result']['friends'] as $key => $d){
                $rs[$d['uin']] = [
                    'nick' => $data['result']['info'][$key]['nick'],
                    'mark' => $data['result']['info'][$key]['nick'],
                    'categorie' => $d['categories'] == 0 ? 'default' : $data['result']['categories'][$d['categories'] - 1]['name'],
                    'flag' => $d['flag'],
                ];
            }
            foreach($data['result']['marknames'] as $m){
                $rs[$m['uin']]['mark'] = $m['markname'];
            }
        }
        return $rs;
    }

}