<?php
namespace phqagent\utils;

class YAML {

    const FILE = 1;
    const STREAM = 2;
    const ARRAY = 3;

    const FILE_NOT_FOUND = 'File not found';
    const BROKEN  = 'Broken YAML';

    const EMPTY = false;

    private $type = YAML::FILE;
    private $filename = '';
    private $yaml = [];

    public function __construct($yaml, $type = YAML::FILE){
        switch($type){

            case YAML::FILE:
                $this->type = YAML::FILE;
                $this->filename = $yaml;
                if(file_exists($yaml)){
                    if($yaml = @yaml_parse_file($yaml)){
                        $this->yaml = $yaml;
                    }else{
                        throw new \Exception(YAML::BROKEN);
                    }
                }else{
                    throw new \Exception(YAML::FILE_NOT_FOUND);
                }
                break;
            
            case YAML::STREAM:
                $this->type = YAML::STREAM;
                if($yaml === YAML::EMPTY){
                    $this->yaml = [];
                }else{
                    if($yaml = @yaml_parse($yaml)){
                        $this->yaml = $yaml;
                    }else{
                        throw new \Exception(YAML::BROKEN);
                    }
                }
                break;

            case YAML::ARRAY:
                $this->type = YAML::ARRAY;
                if(!is_array($yaml)){
                    throw new \Exception(YAML::BROKEN);
                }else{
                    $this->yaml = $yaml;
                }
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

    public function del($key){
        if(isset($this->yaml[$key])){
            unset($this->yaml[$key]);
        }
    }

    public function getKey(){
        return array_keys($this->yaml);
    }

    public function getAll(){
        return $this->yaml;
    }

    public function save($savefile = false){
        switch($this->type){
            case YAML::FILE:
                yaml_emit_file($this->filename, $this->yaml);
                break;

            case YAML::STREAM:
            case YAML::ARRAY:
                if($savefile === false){
                    return yaml_emit($this->yaml);
                }else{
                    $this->filename = $savefile;
                    $this->type = YAML::FILE;
                    yaml_emit_file($savefile, $this->yaml);
                }
                break;

        }
    }

}
