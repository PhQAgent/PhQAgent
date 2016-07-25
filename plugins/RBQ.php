<?php
use plugin\PluginBase;
class RBQ extends PluginBase{

    public function onLoad(){
        $this->getServer()->getLogger()->info('随机RBQ插件已加载!');
    }

    private $type = [
            '女装',
            '抖M',
            '大JJ',
            '巨乳',
            '贫乳',
            '双马尾',
            '傲娇',
            '病娇',
            '变态',
            '智障',
            '发情期',
            '扶她',
            '名器级'
        ];

    private $map;

    private function writelog(){
        if($this->getType() == 'group_message'){
            if(!isset($this->map[$this->getFrom()])){
                $this->map[$this->getFrom()] = [];
            }
            foreach($this->map[$this->getFrom()] as $user){
                if($user == $this->getNickName()){
                    return true;
                }
            }
            if(count($this->map[$this->getFrom()]) < 10){
                $this->map[$this->getFrom()][] = $this->getNickName();
            }elseif(count($this->map[$this->getFrom()]) == 10){
                unset($this->map[$this->getFrom()][0]);
                $this->map[$this->getFrom()] = array_values($this->map[$this->getFrom()]);
            }
            if(count($this->map[$this->getFrom()]) > 10){
                unset($this->map[$this->getFrom()]);
            }
        }
    }

    private function getRBQType(){
        return $this->type[mt_rand(0, count($this->type) - 1)];
    }

    private function getRBQ(){
        $list = $this->map[$this->getFrom()];
        $rbq = $list[mt_rand(0, count($list) - 1)];
        if($rbq !== $this->getNickName()){
            return "{$this->getNickName()} 获得了一个 {$this->getRBQType()} 的 $rbq 作为RBQ";
        }else{
            return "{$this->getNickName()} 脸太黑，只能当别人的RBQ";
        }
        
    }

    public function onReceive(){
        $this->writelog();
        if(strstr($this->getMessage(), '!rbq')){
            $arg = explode('!rbq ', $this->getMessage());
            $arg = isset($arg[1]) ? $arg[1] : '';
            $this->reply($this->getRBQ());
        }
    }

}