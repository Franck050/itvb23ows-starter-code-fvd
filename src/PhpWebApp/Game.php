<?php

include_once 'database.php';
include_once 'GameComponents/Hand.php';
include_once 'GameComponents/Player.php';
include_once 'GameComponents/Board.php';

class Game
{

    function __construct()
    {
    }

    public static function restart()
    {
        Board::setBoard([]);
        Hand::setHand(Hand::resetHand());
        Player::setPlayer(0);
        unset($_SESSION['last_move']);
        unset($_SESSION['error']);

        $db = Database::getInstance();
        $db->newGame();
    }

    public static function pass()
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO moves ' .
            '(game_id, type, move_from, move_to, previous_id, state) ' .
            'VALUES (?, "pass", NULL, NULL, ?, ?)'
        );
        $state = $db->getState();
        $currentGameId = $_SESSION['game_id'];
        $lastMoveId = $_SESSION['last_move'] ?? null;
        $stmt->bind_param('iis', $currentGameId, $lastMoveId, $state);
        $stmt->execute();
        $_SESSION['last_move'] = $db->getConnection()->insert_id;

        $currentPlayer = Player::getPlayer();
        Player::setPlayer(1 - $currentPlayer);

    }
}
