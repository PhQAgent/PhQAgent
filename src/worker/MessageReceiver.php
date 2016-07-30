<?php
namespace worker;
use element\Message;
use utils\Curl;

class MessageReceiver extends \Thread{

    private $server;
    private $logger;

    public function __construct(\Server $server){
        $this->server = $server;
        $this->logger = $server->getLogger();
        new Message(null);
        new Curl();
    }

    public function run(){
        $curl = new Curl();
        while($this->server->isRunning()){
            $json = $curl->
            setUrl('http://d1.web2.qq.com/channel/poll2')->
            setReferer('http://d1.web2.qq.com/proxy.html?v=20151105001')->
            setPost([
                'r' => json_encode([
                    'ptwebqq' => $this->server->getSavedSession()->ptwebqq,
                    'clientid' => $this->server->getSavedSession()->clientid,
                    'psessionid' => $this->server->getSavedSession()->psessionid,
                ], JSON_FORCE_OBJECT)
            ])->
            setCookie($this->server->getSavedSession()->getCookie())->
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
                    case 'group_message':
                        $message = [
                            'type' => $json['result'][0]['poll_type'],
                            'from' => $json['result'][0]['value']['group_code'],
                            'send' => $json['result'][0]['value']['send_uin'],
                            'content' => $content,
                        ];
                        break;
                    case 'message':
                        $message = [
                            'type' => $json['result'][0]['poll_type'],
                            'from' => $json['result'][0]['value']['from_uin'],
                            'send' => $json['result'][0]['value']['from_uin'],
                            'content' => $content,
                        ];
                        break;
                }
                $this->logger->info($message['content']);
                //$this->server->getPluginManager()->onReceive(new Message($message));
            }
        }
    }

}