<?php
namespace protocol;
use phqagent\console\MainLogger;
use phqagent\element\User;
use phqagent\element\Group;
use protocol\web\method\Method;
use protocol\web\method\WebQQLogin;
use protocol\web\io\MessageReceiver;
use protocol\web\io\MessageSender;
use protocol\web\SavedSession;
class Protocol{

    const NAME = 'Web';

    private static $instance;
    private $queue;
    private $sender;
    private $receiver;

    public function __construct(){
        self::$instance = $this;
    }

    public function login(){
        if(SavedSession::load()){
            MainLogger::info('尝试通过保存的Session登录...');
            $verify = Method::getSelfInfo();
            if($verify['retcode'] == 100000){
                MainLogger::warning('Session登录失败，开始扫码登录...');
                $login = new WebQQLogin();
                $login->login();
                SavedSession::process($login->getLoginSession());
                SavedSession::save();
            }
        }else{
            MainLogger::info('开始扫码登录...');
            $login = new WebQQLogin();
            $login->login();
            SavedSession::process($login->getLoginSession());
            SavedSession::save();
        }
        Method::getSelfInfo();
        Method::getRecentList();
        if(!Method::getGroupList()){
            MainLogger::alert('您的账户受到封锁，无法获取群相关信息');
        }
        $account = str_replace(['o0','o'], '', SavedSession::$uin);
        MainLogger::success('账户 ' . $account . ' 登录成功!');
        MainLogger::info('正在初始化消息队列...');
        $this->sender = new MessageSender($this);
        $this->receiver = new MessageReceiver($this);
        $this->sender->start();
        $this->receiver->start();
        MainLogger::success('消息队列初始化成功');
    }

    public static function getUserAccount(User $user){
        return Method::uin2acc($user->getUin());
    }

    public static function getFriendNick(User $user){
        return Method::getFriendNick($user->getUin());
    }

    public function getMessageSender(){
        return $this->sender;
    }

    public function getMessageReceiver(){
        return $this->receiver;
    }

    public static function getGroupList(){
        return Method::getGroupList();
    }

    public static function getFriendList(){
        return Method::getGroupMemberList();
    }

    public static function getGroupMemberList(Group $group){
        return Method::getGroupMemberList($group->getGid());
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function isOnline(){
        return !$this->sender->outdated;
    }

    public function __call($method, $args){
        throw new \Exception("$method not supported in {self::NAME} Protocol");
    }

}