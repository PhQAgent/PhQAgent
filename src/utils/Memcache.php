<?php
namespace utils;

class Memcache{
	private $memcached;

	public function __construct($host, $port){
		$this->memcached = new \Memcache();
		$this->memcached->pconnect($host, $port);
	}
	
	public function get($key, $default = null){
		$data = $this->memcached->get($key);
		if($data == null){
			return $default;
		}else{
			return $data;
		}
	}

	public function del($key){
		$this->memcached->delete($key);
	}

	public function set($key, $value){
		$data = $this->memcached->set($key, $value);
	}
	
}
