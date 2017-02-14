<?php
namespace phqagent\element;

use protocol\Protocol;

class User
{

    private static $cache = [];

    private $uin;
    private $account;
    private $nick;
    private $mark;
    private $categorie;
    private $defaultgroup;

    public function __construct($uin, $defaultgroup = null)
    {
        $this->uin = $uin;
        $this->defaultgroup = $defaultgroup;
        if (isset(self::$cache[$uin])) {
            foreach (self::$cache[$uin] as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function getUin()
    {
        return $this->uin;
    }

    public function getAccount()
    {
        if ($this->account == null) {
            $this->account = Protocol::getInstance()->getUserAccount($this);
            self::$cache[$this->uin]['account'] = $this->account;
        }
        return $this->account;
    }

    public function getNick()
    {
        if ($this->nick == null) {
            $this->nick = Protocol::getInstance()->getFriendNick($this);
            self::$cache[$this->uin]['nick'] = $this->nick;
        }
        return $this->nick;
    }

    public function getMark()
    {
        if ($this->mark == null) {
            $this->mark = FriendList::getFriendMark($this);
            self::$cache[$this->uin]['mark'] = $this->mark;
        }
        return $this->mark;
    }

    public function getName()
    {
        if ($this->defaultgroup != null) {
            return $this->getCard();
        } else {
            return $this->getMark();
        }
    }

    public function getCategorie()
    {
        if ($this->categorie === null) {
            $this->categorie = FriendList::getCategorie($this);
            self::$cache[$this->uin]['categorie'] = $this->categorie;
        }
        return $this->categorie;
    }

    public function getCard(Group $group = null)
    {
        if ($group == null) {
            $group = $this->defaultgroup;
        }
        if ($group == null) {
            return $this->getNick();
        }
        return $group->getCard($this);
    }
}
