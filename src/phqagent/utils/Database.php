<?php
namespace phqagent\utils;

class Database
{
    private $connection;
    
    public function __construct($address, $name, $username, $password)
    {
        $this->connection = mysqli_connect($address, $username, $password, $name);
    }

    public static function process($array)
    {
        $return = [];
        foreach ($array as $oneline) {
            $return[] = mysqli_real_escape_string($this->getConnection(), $oneline);
        }
        return $return;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function query($SQL)
    {
        $RESULT = [];
        mysqli_ping($this->getConnection());
        foreach ($SQL as $id => $query) {
            $sql_rs = mysqli_query($this->getConnection(), $query);
            if (!$sql_rs) {
                $RESULT[$id] = false;
            } elseif (!($sql_rs === true)) {
                while ($rs = mysqli_fetch_array($sql_rs)) {
                    $RESULT[$id][] = $rs;
                }
            }
        }
        return $RESULT;
    }
    
    public function __destruct()
    {
        mysqli_close($this->connection);
    }
}
