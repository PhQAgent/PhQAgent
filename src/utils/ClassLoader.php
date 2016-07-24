<?php
namespace utils;

class ClassLoader{
	private $root;
	
	public function __construct($root){
		$this->root = $root.DIRECTORY_SEPARATOR;
	}
	
	public function register(){
		spl_autoload_register(array($this, 'loadClass'));
	}
	
	public function loadClass($name){
		$name = str_replace('\\', '/', $name);
		include "$this->root$name.php";
	}
	
}