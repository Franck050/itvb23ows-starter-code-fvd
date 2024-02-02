<?php

namespace Controllers;
class DatabaseController
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

    public static function getInstance(): ?DatabaseController
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseController();
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

    function newGame()
    {
        $stmt = $this->prepare('INSERT INTO games VALUES ()');
        $stmt->execute();
        $_SESSION['game_id'] = $this->getConnection()->insert_id;
    }

    function insertPassMove($gameId, $previousId)
    {
        $stmt = $this->prepare(
            'INSERT INTO moves ' .
            '(game_id, type, move_from, move_to, previous_id, state) ' .
            'VALUES (?, "pass", NULL, NULL, ?, ?)'
        );
        $state = $this->getState();
        $stmt->bind_param('iis', $gameId, $previousId, $state);
        $stmt->execute();
        return $this->connection->insert_id;
    }

    public function insertMove($gameId, $type, $from, $to, $lastMove) {
        $stmt = $this->prepare(
            'INSERT INTO moves ' .
            '(game_id, type, move_from, move_to, previous_id, state) ' .
            'VALUES (?, ?, ?, ?, ?, ?)'
        );
        $state = $this->getState();
        $stmt->bind_param('isssis', $gameId, $type, $from, $to, $lastMove, $state); // Updated to include $type
        $stmt->execute();
        return $this->connection->insert_id;
    }

    public function getLastMove(int $gameId): ?array
    {
        $stmt = $this->prepare('SELECT * from moves WHERE game_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->bind_param('i', $gameId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_array();
        return $result == null ? null : $result;
    }

    public function deleteMove(int $id)
    {
        $stmt = $this->prepare('DELETE FROM moves WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    public function getMove(int $id)
    {
        $stmt = $this->prepare('SELECT * FROM moves WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_array();
    }
}
