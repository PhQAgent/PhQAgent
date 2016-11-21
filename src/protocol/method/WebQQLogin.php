<?php
namespace protocol\method;

use phqagent\utils\Curl;
use phqagent\console\MainLogger;
use protocol\httpd\TCPServer;

class WebQQLogin{

    private $loadtime;
    private $curl;
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
        $this->httpd = new TCPServer($this->getQRCode());
        $status = $this->checkQRCode();
        $old = 0;
        do{
            sleep(2);
            $status = $this->checkQRCode();
            if($status[0] == 65 && $old != 65){
                MainLogger::alert('验证码已过期,重新生成');
                $this->doQRCode();
                $this->httpd->setQRCode($this->getQRCode());
            }
            if($status[0] == 66 && $old != 66){
                MainLogger::info('请扫描二维码');
            }
            if($status[0] == 67 && $old != 67){
                MainLogger::info('请在手机上确认');
            }
            $old = $status[0];
        }while(!($status[0] == 0));
        $this->httpd->shutdown();
        $this->doWebQQLogin($status[1]);
        $this->doPSessionId();
        $this->doVFWebqq();
        $this->doHash();
        $this->doBkn();
    }

    private function doPtLogin(){
        $this->curl->
        setUrl('https://ui.ptlogin2.qq.com/cgi-bin/login')->
        setGet([
            'appid' => 501004106,
            's_url' => 'http://w.qq.com/proxy.html',
        ])->exec();
        $cookie = $this->curl->getCookie();
        if(!isset($cookie['pt_user_id'])){
            throw new \Exception('doPtLogin');
            return ;
        }
        $this->ptlogin = $cookie;
    }

    private function doQRCode(){
        $this->loadtime = time();
        $qrpacket = $this->curl->
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
        $cookie = $this->curl->getCookie();
        if(!isset($cookie['qrsig'])){
            throw new \Exception('doQRCode');
            return ;
        }
        $this->qrcookie = $cookie;
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
        $get = [
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
            'action' => "0-0-" . (string)((time() - $this->loadtime) * 1000 + mt_rand(1, 99)),
            'mibao_css' => 'm_webqq',
            't' => 1,
            'g' => 1,
            'js_type' => 0,
            'js_ver' => 10179,
            'login_sig' => '',
            'pt_randsalt' => 2
        ];
        $result = $this->curl->
        setUrl('https://ssl.ptlogin2.qq.com/ptqrlogin')->
        setGet($get)->
        setCookie($cookie)->
        setTimeout(10)->
        exec();
        if(!preg_match('/ptuiCB\(\'(.*)\',\'(.*)\',\'(.*)\'/iU', $result, $status)){
            $this->httpd->shutdown();
            throw new \Exception('checkQRCode::preg_match');
            return ;
        }
        $cookie = $this->curl->getCookie();
        if($status[1] == 0 && !isset($cookie['ptwebqq'])){
            $this->httpd->shutdown();
            throw new \Exception('checkQRCode::getCookie');
            return ;
        }
        $this->ptwebqq = $cookie;
        return [$status[1], $status[3]];
    }

    private function doWebQQLogin($url){
        $this->curl->
        setUrl($url)->
        exec();
        $cookie = $this->curl->getCookie();
        if(!isset($cookie['p_skey'])){
            throw new \Exception('doWebQQLogin');
            return ;
        }
        $this->webqq = $cookie;
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
        $data = json_decode($json, true);
        if(!isset($data['result']['psessionid'])){
            throw new \Exception('doPSessionId');
            return ;
        }
        $this->psessionid = $data['result'];
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
        $data = json_decode($json, true);
        if(!isset($data['result']['vfwebqq'])){
            throw new \Exception('vfwebqq');
            return ;
        }
        $this->vfwebqq = $data['result'];
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
        $v[0] = $selfUin >> 24 & 255 ^ ord($u[0][0]);
        $v[1] = $selfUin >> 16 & 255 ^ ord($u[0][1]);
        $v[2] = $selfUin >> 8 & 255 ^ ord($u[1][0]);
        $v[3] = $selfUin & 255 ^ ord($u[1][1]);
        $ui = [];
        for($t = 0; $t < 8; $t++){
            $ui[$t] = ($t % 2 == 0) ? $n[$t >> 1] : $v[$t >> 1];
        } 
        $hex = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
        $hash = '';
        for($t = 0;$t < count($ui); $t++){
            $hash .= $hex[$ui[$t] >> 4 & 15];
            $hash .= $hex[$ui[$t] & 15];
        }
        $this->hash = ['hash' => $hash];
    }

    private function doBkn(){
        $skey = $this->webqq['skey'];
        $lenth = strlen($skey);
        $result = 5381;
        for($n = 0; $n < $lenth; $n++){
            $result += ($result << 5) + ord(substr($skey, $n, 1));
        }
        $bkn = 2147483647 & $result;
        $this->bkn = ['bkn' => $bkn];
    }

    public function getLoginSession(){
        $info = array_merge($this->ptwebqq, $this->webqq, $this->psessionid, $this->vfwebqq, $this->hash, $this->bkn);
        $info['uin'] = $this->ptwebqq['uin'];
        return $info;
    }

    public function getQRCode(){
        return $this->qrcode;
    }

}