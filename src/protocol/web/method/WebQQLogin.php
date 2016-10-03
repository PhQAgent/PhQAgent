<?php
namespace protocol\web\method;
use phqagent\utils\Curl;
use phqagent\console\MainLogger;

class WebQQLogin{

    private $hander;
    private $ptwebqq;
    private $webqq;
    private $psessionid;
    private $vfwebqq;
    private $hash;

    public function login(){
        $this->curl = new Curl();
        $this->doPtLogin();
        $this->doQRCode();
        $status = $this->checkQRCode();
        $old = 0;
        do{
            sleep(1);
            $status = $this->checkQRCode();
            if($status[0] == 65 && $old != 65){
                MainLogger::alert('验证码已过期');
            }
            if($status[0] == 66 && $old != 66){
                MainLogger::info('请扫描二维码');
                
            }
            if($status[0] == 67 && $old != 67){
                MainLogger::info('请在手机上确认');
            }
            $old = $status[0];
        }while(!($status[0] == 0));
        $this->doWebQQLogin($status[1]);
        $this->doPSessionId();
        $this->doVFWebqq();
        $this->doHash();
    }

    private function doPtLogin(){
        $this->curl->
        setUrl('https://ui.ptlogin2.qq.com/cgi-bin/login')->
        setGet([
            'appid' => 501004106,
            's_url' => 'http://w.qq.com/proxy.html',
        ])->exec();
        $this->ptlogin = (array)$this->curl->getCookie();
    }

    private function doQRCode(){
        $qrpacket =  $this->curl->
        setUrl('https://ssl.ptlogin2.qq.com/ptqrshow')->
        setGet([
            'appid' => 501004106,
            'e' => 0,
            'l' => 'M',
            's' => 5,
            'd' => 72,
            'v' => 4,
        ])->
        setCookie($this->ptlogin)->
        exec();
        $this->qrcookie = (array)$this->curl->getCookie();
        $this->curlrs = explode("\n", $qrpacket);
        $img = '';
        for($i = 11; $i < count($this->curlrs); $i++){
            $img .= "{$this->curlrs[$i]}\n";
        }
        $this->qrcode = $img;
        file_put_contents('QRCode.png', $img);
    }

    private function checkQRCode(){
        $cookie = $this->qrcookie + $this->ptlogin;
        $result = $this->curl->
        setUrl('https://ssl.ptlogin2.qq.com/ptqrlogin')->
        setGet([
            'webqq_type' => 10,
            'remember_uin' => 1,
            'login2qq' => 1,
            'aid' => 501004106,
            'u1' => 'http://w.qq.com/proxy.html?login2qq=1&webqq_type=10',
            'ptredirect' => 0,
            'ptlang' => 2052,
            'daid' => 164,
            'from_ui' => 1,
            'pttype' => 1,
            'dumy' => '',
            'fp' => 'loginerroralert',
            'action' => 0-0-4128,
            'mibao_css' => 'm_webqq',
            't' => 'undefined',
            'g' => 1,
            'js_type' => 0,
            'js_ver' => 10167,
            'login_sig' => '',
            'pt_randsalt' => 0,
        ])->
        setCookie($cookie)->
        exec();
        preg_match('/ptuiCB\(\'(.*)\',\'(.*)\',\'(.*)\'/iU', $result, $status);
        $this->ptwebqq = (array)$this->curl->getCookie();
        return [$status[1], $status[3]];
    }

    private function doWebQQLogin($url){
        $this->curl->
        setUrl($url)->
        exec();
        $this->webqq = (array)$this->curl->getCookie();
    }

    private function doPSessionId(){
        $json =  $this->curl->
        setUrl('http://d1.web2.qq.com/channel/login2')->
        setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setPost([
            'r' => json_encode([
                'ptwebqq' => $this->ptwebqq['ptwebqq'],
                'clientid' => 53999199,
                'psessionid' => '',
                'status' => 'online',
            ], JSON_FORCE_OBJECT)
        ])->
        setCookie($this->ptwebqq + $this->webqq)->
        returnHeader(false)->
        exec();
        $this->psessionid = (array)json_decode($json, true)['result'];
    }

    private function doVFWebqq(){
        $json = $this->curl->
        setUrl('http://s.web2.qq.com/api/getvfwebqq')->
        setReferer('http://s.web2.qq.com/proxy.html?v=20130916001')->
        setGet([
            'ptwebqq' => $this->ptwebqq['ptwebqq'],
            'clientid' => 53999199,
            'psessionid' => '',
        ])->
        setCookie($this->ptwebqq + $this->webqq)->
        returnHeader(false)->
        exec();
        $this->vfwebqq = (array)json_decode($json, true)['result'];
    }

    private function doHash(){
        $selfUin = trim($this->webqq['uin'], 'o');
        $ptwebqq = $this->ptwebqq['ptwebqq'];
        $n = [0, 0, 0, 0];
        for($t = 0;  $t < strlen($ptwebqq); $t++){
            $n[$t % 4] = $n[$t % 4] ^ ord($ptwebqq[$t]);
        }
        $u = ['EC', 'OK'];
        $v = [];
        $v[0]=$selfUin >> 24 & 255 ^ ord($u[0][0]);
        $v[1]=$selfUin >> 16 & 255 ^ ord($u[0][1]);
        $v[2]=$selfUin >> 8 & 255 ^ ord($u[1][0]);
        $v[3]=$selfUin & 255 ^ ord($u[1][1]);
        $ui = [];
        for($t = 0; $t < 8; $t++){
            $ui[$t]=($t % 2 == 0) ? $n[$t >> 1] : $v[$t >> 1];
        } 
        $hex=['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F'];
        $hash = '';
        for($t = 0;$t < count($ui); $t++){
            $hash .= $hex[$ui[$t] >> 4 & 15];
            $hash .= $hex[$ui[$t] & 15];
        }
        $this->hash = (array)['hash' => $hash];
    }

    public function getLoginSession(){
        $info = array_merge($this->ptwebqq, $this->webqq, $this->psessionid, $this->vfwebqq, $this->hash);
        $info['uin'] = $this->ptwebqq['uin'];
        return $info;
    }

    public function getQRCode(){
        return $this->qrcode;
    }

}