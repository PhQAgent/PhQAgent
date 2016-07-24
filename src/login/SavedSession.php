<?php
namespace login;
class SavedSession{

    private $file;

    public $p_uin;
    public $uin;
    public $skey;
    public $p_skey;
    public $ptwebqq;
    public $vfwebqq;
    public $psessionid;
    public $hash;
    public $clientid = 53999199;

    public function __construct(\Server $server){
        $this->file = $server->getBaseDir().DIRECTORY_SEPARATOR.'session.json';
    }

    public function process($info){
        $this->p_uin = $info['uin'];
        $this->uin = $info['uin'];
        $this->skey = $info['skey'];
        $this->p_skey = $info['p_skey'];
        $this->ptwebqq = $info['ptwebqq'];
        $this->vfwebqq = $info['vfwebqq'];
        $this->psessionid = $info['psessionid'];
        $this->hash = $info['hash'];
    }

    public function save(){
        file_put_contents($this->file, json_encode([
            'uin' => $this->uin,
            'skey' => $this->skey,
            'p_skey' => $this->p_skey,
            'ptwebqq' => $this->ptwebqq,
            'vfwebqq' => $this->vfwebqq,
            'psessionid' => $this->psessionid,
            'hash' => $this->hash,
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

    public function getCookie(){
        return [
	            'p_uin' => $this->p_uin,
                'uin' => $this->uin,
                'skey' => $this->skey,
                'p_skey' => $this->p_skey,
                'ptwebqq' => $this->ptwebqq,
                'vfwebqq' => $this->vfwebqq,
                'psessionid' => $this->psessionid,
            ];
    }

}