<?php
namespace login;
class SavedSession{

    private $file;

    public static $p_uin;
    public static $uin;
    public static $skey;
    public static $p_skey;
    public static $ptwebqq;
    public static $vfwebqq;
    public static $psessionid;
    public static $hash;
    public static $clientid = 53999199;
    public static $cookie;
    public static $serialized;

    public function __construct(\Server $server){
        $this->file = $server->getBaseDir().DIRECTORY_SEPARATOR.'session.json';
    }

    public function process($info){
        self::$p_uin = $info['uin'];
        self::$uin = $info['uin'];
        self::$skey = $info['skey'];
        self::$p_skey = $info['p_skey'];
        self::$ptwebqq = $info['ptwebqq'];
        self::$vfwebqq = $info['vfwebqq'];
        self::$psessionid = $info['psessionid'];
        self::$hash = $info['hash'];
        $cookie = [
	            'p_uin' => $info['uin'],
                'uin' => $info['uin'],
                'skey' => $info['skey'],
                'p_skey' => $info['p_skey'],
                'ptwebqq' => $info['ptwebqq'],
                'vfwebqq' => $info['vfwebqq'],
                'psessionid' => $info['psessionid'],
            ];
        self::$cookie = $cookie;
        self::$serialized = json_encode($cookie);
    }

    public function save(){
        file_put_contents($this->file, json_encode([
            'uin' => self::$uin,
            'skey' => self::$skey,
            'p_skey' => self::$p_skey,
            'ptwebqq' => self::$ptwebqq,
            'vfwebqq' => self::$vfwebqq,
            'psessionid' => self::$psessionid,
            'hash' => self::$hash,
        ]));
    }

    public function load(){
        if(file_exists($this->file)){
            $info = json_decode(file_get_contents($this->file), true);
            if(is_array($info)){
                $this->process($info);
                return true;
            }
        }
        return false;
    }

}