<?php
use plugin\PluginBase;

class Roll extends PluginBase{

    public function onLoad(){
        $this->getServer()->getLogger()->info('roll骰子插件已加载!');
    }

    private function getRandom($arg){
        $min = 1;
        $max = is_numeric($arg) ? $arg < 1 ? 1 : $arg : 100;
        return mt_rand($min, $max);
    }

    public function onReceive(){
        if(strstr($this->getMessage(), '!roll')){
            $arg = explode('!roll ', $this->getMessage());
            $arg = isset($arg[1]) ? $arg[1] : '';
            $this->reply("@{$this->getNickName()} rolled {$this->getRandom($arg)} point(s)");
        }
    }

}