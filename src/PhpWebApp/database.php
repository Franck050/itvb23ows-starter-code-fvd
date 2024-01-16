<?php

//namespace Itvb23owsStarterCodeFvd\PhpWebApp;

class Database
{
    private static $instance = null;
    private $connection;

    public function __construct()
    {
        $this->connection = new \mysqli('db', 'root', 'root', 'hive');
        if($this->connection->connect_error) {
            echo 'connection failed' . $this->connection->connect_error;
        }
        return $this->connection;
    }

    public static function getInstance(): ?Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function prepare($query) {
        return $this->connection->prepare($query);
    }

    function getConnection()
    {
        return $this->connection;
    }

    function getState()
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    function setState($state)
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }
}
