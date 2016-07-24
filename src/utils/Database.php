<?php
namespace utils;

class Database{
	private $address;
	private $dbname;
	private $username;
	private $password;
	private $connection;
	
	public function __construct($address, $username, $password, $name){
		$this->address = Config::DB_ADDRESS;
		$this->username = Config::DB_USERNAME;
		$this->password = Config::DB_PASSWORD;
		$this->dbname = Config::DB_NAME;
		$this->connection = mysqli_connect($this->address, $this->username, $this->password, $this->dbname);
	}

	public function process($array){
		foreach($array as $oneline){
			$return[]=mysqli_real_escape_string($this->getConnection(), $oneline);
		}
		return $return;
	}
	
	public function getConnection(){
		return $this->connection;
	}
	
	public function getServer(){
		return $this->server;
	}
	
	public function query($SQL){
		$RESULT = [];
		foreach($SQL as $id=>$query){
			$sql_rs = mysqli_query($this->getConnection(),$query);
			if(!$sql_rs){
				$RESULT[$id] = false;
			}elseif(!($sql_rs === true)){
				while($rs = mysqli_fetch_array($sql_rs)){
					$RESULT[$id][] = $rs;
				}
			}
		}
		return $RESULT;
	}
	
	public function __destruct(){
		mysqli_close($this->connection);
	}
	
}