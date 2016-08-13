<?php
namespace login;
use element\GroupList;
use module\GetSelfInfo;
use module\GetRecentList;
use httpd\TCPServer;
class LoginHandler{

    private $server;
    private $curl;
    private $savedsession;
    private $isRunning;
    private $thread;

    public function __construct(\Server $server){
        $this->isRunning = true;
        $this->server = $server;
        $this->savedsession = new SavedSession($server);
    }

    public function login(){
        if($this->savedsession->load()){
            $this->getLogger()->info('尝试通过保存的Session登录...');
            $verify = (new GetSelfInfo())->getInfo();
            if($verify['retcode'] == 100000){
                $this->getLogger()->warning('尝试通过保存的Session登录失败，开始扫码登录...');
                $this->reallogin();
            }
        }else{
            $this->getLogger()->info('开始扫码登录...');
            $this->realLogin();
        }
        (new GetSelfInfo($this->server))->getInfo();
        (new GetRecentList($this->server))->getRecentList();
        new GroupList();
        $account = str_replace(['o0','o'], '', SavedSession::$uin);
        $this->getLogger()->success('账户 ' . $account . ' 登录成功!');
        //new FriendList();
        return $this->savedsession;
    }

    private function setRunning($bool){
        $this->isRunning = $bool;
    }

    public function isRunning(){
        return $this->isRunning;
    }

    private function realLogin(){
        $this->getLogger()->info('初始化登录线程...');
        $this->setRunning(true);
        $this->thread = new Login($this);
        do{
            usleep(10);
            $status = $this->thread->getStatus();
            if($ostatus !== $status){
                switch($status){
                    case 0:
                        $this->getLogger()->info('正在进行统一平台认证...');
                        break;
                    case 1:
                        $this->getLogger()->info('请扫描二维码登录...');
                        $httpd = new TCPServer($this);
                        break;
                    case 2:
                        $this->getLogger()->info('扫码成功，请在手机QQ上确认...');
                        break;
                    case 3:
                        $this->getLogger()->info('二维码过期，请重新启动服务端!');
                        $this->server->shutdown();
                        break;
                    case 4:
                        $this->getLogger()->info('二维码认证通过!');
                        break;
                }
            }
            $ostatus = $status;
        }while(!($status == 5));
        $this->savedsession->process($this->thread->createSavedSessionInfo());
        $this->savedsession->save();
        $this->setRunning(false);
        while($this->thread->isRunning() or $httpd->isRunning()){
            usleep(10);
        }
        unset($httpd);
        unset($this->thread);
    }

    private function getLogger(){
        return $this->server->getLogger();
    }

    public function getAuthThread(){
        return $this->thread;
    }

}