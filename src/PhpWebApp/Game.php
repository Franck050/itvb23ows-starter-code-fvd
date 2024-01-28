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
}