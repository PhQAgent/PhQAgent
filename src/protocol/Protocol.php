<?php
namespace protocol;

use phqagent\element\User;
use phqagent\element\Group;
use phqagent\element\FriendList;
use phqagent\element\GroupList;
use protocol\method\Method;
use protocol\method\WebQQLogin;
use protocol\io\MessageReceiver;
use protocol\io\MessageSender;
use sf\console\Logger;
use sf\console\TextFormat;

class Protocol{

    private static $instance;
    private $error;
    /** @var MessageSender */
    private $sender;
    /** @var MessageReceiver */
    private $receiver;

    public function __construct(){
        self::$instance = $this;
    }

    public function login(){
        try{
            if(SavedSession::load()){
                Logger::info('尝试通过保存的Session登录...');
                $verify = Method::getSelfInfo();
                if(!isset($verify['retcode'])){
                    throw new \Exception('SavedSessionLogin');
                }
                if($verify['retcode'] == 100000){
                    Logger::warning('Session登录失败，开始扫码登录...');
                    $login = new WebQQLogin();
                    $login->login();
                    SavedSession::process($login->getLoginSession());
                    SavedSession::save();
                }
            }else{
                Logger::info('开始扫码登录...');
                $login = new WebQQLogin();
                $login->login();
                SavedSession::process($login->getLoginSession());
                SavedSession::save();
            }
        }catch(\Exception $e){
            Logger::alert('登录出现问题，请检查网络连接!');
            Logger::alert('Exception thrown at: ' . $e->getMessage());
            $this->error = true;
            return ;
        }
        Method::getSelfInfo();
        Method::getRecentList();
        Method::getOnlineBuddies();
        if(GroupList::getGroupList() === false){
            Logger::alert('无法获取群相关信息');
        }
        if(FriendList::getFriendList() === false){
            Logger::alert('无法获取好友列表相关信息');
        }
        $account = str_replace(['o0','o'], '', SavedSession::$uin);
        Logger::info(TextFormat::GREEN . '账户 ' . $account . ' 登录成功!');
        Logger::info('正在初始化消息队列...');
        $this->sender = new MessageSender($this);
        $this->receiver = new MessageReceiver($this);
        $this->sender->start();
        $this->receiver->start();
        Logger::info(TextFormat::GREEN . '消息队列初始化成功');
    }

    public function shutdown(){
        $this->sender->shutdown();
        $this->receiver->shutdown();
    }

    public static function changeGroupCard(Group $group, User $user, $name){
        return Method::changeGroupCard($group->getNumber(), $user->getAccount(), $name);
    }

    public static function banGroupMember(Group $group, User $user, $time){
        return Method::banGroupMember($group->getNumber(), $user->getAccount(), $time);
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

    public function isError(){
        return $this->error;
    }

}