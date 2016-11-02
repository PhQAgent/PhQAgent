<?php
namespace protocol;
use phqagent\console\MainLogger;
use phqagent\element\User;
use phqagent\element\Group;
use phqagent\element\FriendList;
use phqagent\element\GroupList;
use protocol\web\method\Method;
use protocol\web\method\WebQQLogin;
use protocol\web\io\MessageReceiver;
use protocol\web\io\MessageSender;
use protocol\web\SavedSession;
class Protocol{

    const NAME = 'WebQQ';

    private static $instance;
    private $error;
    private $queue;
    private $sender;
    private $receiver;

    public function __construct(){
        self::$instance = $this;
    }

    public function login(){
        try{
            if(SavedSession::load()){
                MainLogger::info('尝试通过保存的Session登录...');
                $verify = Method::getSelfInfo();
                if(!isset($verify['retcode'])){
                    throw new \Exception('SavedSessionLogin');
                }
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
        }catch(\Exception $e){
            MainLogger::alert('登录出现问题，请检查网络连接!');
            MainLogger::alert('Exception thrown at: ' . $e->getMessage());
            $this->error = true;
            return ;
        }
        Method::getSelfInfo();
        Method::getRecentList();
        Method::getOnlineBuddies();
        if(GroupList::getGroupList() === false){
            MainLogger::alert('无法获取群相关信息');
        }
        if(FriendList::getFriendList() === false){
            MainLogger::alert('无法获取好友列表相关信息');
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

    public function shutdown(){
        $this->sender->shutdown();
        $this->receiver->shutdown();
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
        return Method::getFriendList();
    }

    public static function getGroupMemberList(Group $group){
        return Method::getGroupMemberList($group->getGid());
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function isOnline(){
        if($this->sender != null){
            return !$this->sender->outdated;
        }
        return true;
    }

    public function isError(){
        return $this->error;
    }

    public function __call($method, $args){
        throw new \Exception("$method not supported in " . self::NAME . " Protocol");
    }

}