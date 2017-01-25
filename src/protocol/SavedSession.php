<?php
namespace protocol;

abstract class SavedSession{

    public static $p_uin;
    public static $uin;
    public static $skey;
    public static $p_skey;
    public static $ptwebqq;
    public static $vfwebqq;
    public static $psessionid;
    public static $hash;
    public static $bkn;
    public static $clientid;
    public static $cookie;

    public static function init(){
        return 'pthread hack';
    }

    public static function process($info){
        self::$p_uin = $info['uin'];
        self::$uin = $info['uin'];
        self::$skey = $info['skey'];
        self::$p_skey = $info['p_skey'];
        self::$ptwebqq = $info['ptwebqq'];
        self::$vfwebqq = $info['vfwebqq'];
        self::$psessionid = $info['psessionid'];
        self::$hash = $info['hash'];
        self::$bkn = $info['bkn'];
        self::$clientid = 53999199;
        self::$cookie = [
	        'p_uin' => $info['uin'],
            'uin' => $info['uin'],
            'skey' => $info['skey'],
            'p_skey' => $info['p_skey'],
            'ptwebqq' => $info['ptwebqq'],
            'vfwebqq' => $info['vfwebqq'],
            'psessionid' => $info['psessionid'],
        ];
    }

    public static function save(){
        $file = \phqagent\BASE_DIR . 'session.json';
        file_put_contents($file, json_encode([
            'uin' => self::$uin,
            'skey' => self::$skey,
            'p_skey' => self::$p_skey,
            'ptwebqq' => self::$ptwebqq,
            'vfwebqq' => self::$vfwebqq,
            'psessionid' => self::$psessionid,
            'hash' => self::$hash,
            'bkn' => self::$bkn,
        ]));
    }

    public static function load(){
        $file = \phqagent\BASE_DIR . 'session.json';
        if(file_exists($file)){
            $info = json_decode(file_get_contents($file), true);
            if(is_array($info)){
                self::process($info);
                return true;
            }
        }
        return false;
    }

}