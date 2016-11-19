<?php
namespace protocol\io;

use protocol\SavedSession;
use phqagent\message\MessageQueue;
use phqagent\utils\Curl;

class MessageReceiver extends \Thread{

    private $inbox;
    private $cookie;
    private $shutdown;

    public function __construct(){
        $this->inbox = MessageQueue::getInstance()->getInbox();
        $this->shutdown = false;
        $this->cookie = SavedSession::$cookie;
        Curl::init();
        SavedSession::init();
    }

    public function run(){
        $curl = new Curl();
        while(!$this->shutdown){
            $json = $curl->
            setUrl('http://d1.web2.qq.com/channel/poll2')->
            setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
            setPost([
                'r' => json_encode([
                    'ptwebqq' => SavedSession::$ptwebqq,
                    'clientid' => SavedSession::$clientid,
                    'psessionid' => SavedSession::$psessionid,
                ], JSON_FORCE_OBJECT)
            ])->
            setCookie($this->cookie)->
            returnHeader(false)->
            setTimeOut(5)->
            exec();
            $json = json_decode($json, true);
            if(isset($json['result'])){
                $content = '';
                unset($json['result'][0]['value']['content'][0]);
                foreach($json['result'][0]['value']['content'] as $cont){
                    if(!is_string($cont)){
                        continue;
                    }
                    $content .= $cont;
                }
                switch($json['result'][0]['poll_type']){
                    case 'message':
                        $message = [
                            'type' => 1,
                            'from' => $json['result'][0]['value']['from_uin'],
                            'send' => $json['result'][0]['value']['from_uin'],
                            'content' => $content,
                        ];
                        break;
                        
                    case 'group_message':
                        $message = [
                            'type' => 2,
                            'from' => $json['result'][0]['value']['group_code'],
                            'send' => $json['result'][0]['value']['send_uin'],
                            'content' => $content,
                        ];
                        break;
                }
                $this->inbox[] = serialize($message);
            }
        }
    }

    public function shutdown(){
        $this->shutdown = true;
    }

}
