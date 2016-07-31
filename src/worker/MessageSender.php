<?php
namespace worker;
use element\Message;
use login\SavedSession;
use utils\Curl;

class MessageSender{
    private $messageid;

    public function __construct($message){
        $this->messageid = mt_rand(101, 999) * 100000;
        if($message instanceof Message){
            $type = $message->getType();
            $msg = $message->getContent();
            $div = 250;
            $lenth = mb_strlen($msg);
            $payload = [];
            $count = (int)($lenth / $div);
            for($i = 0; $i <= $count; $i++){
                $oneline = mb_substr($msg, 0, $div);
                $msg = str_replace($oneline, '', $msg);
                $payload[] = $oneline;
            }
            
            foreach($payload as $oneline){
                if($type == Message::USER){
                    $this->sendUser($message->getTarget(), $oneline);
                }elseif($type == Message::GROUP){
                    $this->sendGroup($message->getTarget(), $oneline);
                }
            }
        }
    }

    private function sendUser($uin, $content){
        $this->messageid++;
        (new Curl())->
        setUrl('http://d1.web2.qq.com/channel/send_buddy_msg2')->
        setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setPost([
            'r' => json_encode([
                'to' => $uin,
                'content' => '["'.$content.'",["font",{"name":"宋体","size":10,"style":[0,0,0],"color":"000000"}]]',
                'face' => 603,
                'clientid' => SavedSession::$clientid,
                'msg_id' => $this->messageid,
                'psessionid' => SavedSession::$psessionid,
            ], JSON_FORCE_OBJECT)
        ])->
        setCookie(unserialize(SavedSession::$serialized))->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
        return true;
    }

    private function sendGroup($uin, $content){
        $this->messageid++;
        (new Curl())->
        setUrl('http://d1.web2.qq.com/channel/send_qun_msg2')->
        setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
        setPost([
            'r' => json_encode([
                'group_uin' => $uin,
                'content' => '["'.$content.'",["font",{"name":"宋体","size":10,"style":[0,0,0],"color":"000000"}]]',
                'face' => 603,
                'clientid' => SavedSession::$clientid,
                'msg_id' => $this->messageid,
                'psessionid' => SavedSession::$psessionid,
            ], JSON_FORCE_OBJECT)
        ])->
        setCookie(unserialize(SavedSession::$serialized))->
        returnHeader(false)->
        setTimeOut(5)->
        exec();
    }

}