<?php
namespace phqagent\utils;

class YAML {

    const FILE = 1;
    const STREAM = 2;

    private $type = YAML::FILE;
    private $filename = '';
    private $yaml = [];

    public function __construct($yaml, $type = YAML::FILE){
        switch($type){
            case YAML::FILE:
                $this->type = YAML::FILE;
                $this->filename = $yaml;
                if($yaml = yaml_parse_file($yaml)){
                    $this->yaml = $yaml;
                }
                break;
            
            case YAML::STREAM:
                $this->type = YAML::STREAM;
                if($yaml = yaml_parse($yaml)){
                    $this->yaml = $yaml;
                }
                break;

            default:
                throw new \Exception('Unsupport YAML type');
        }
    }

    public function get($key, $default = null){
        if(isset($this->yaml[$key])){
            return $this->yaml[$key];
        }else{
            return $default;
        }
    }

    public function set($key, $value, $autosave = true){
        $this->yaml[$key] = $value;
        if($autosave){
            $this->save();
        }
    }

    public function getKey(){
        return array_keys($this->yaml);
    }

    public function getAll(){
        return $this->yaml;
    }

    public function save(){
        switch($this->type){
            case YAML::FILE:
                yaml_emit_file($this->filename, $this->yaml);
                break;

            case YAML::STREAM:
                return yaml_emit($this->yaml);
                break;

        }
    }

}
