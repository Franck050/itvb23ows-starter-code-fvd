<?php

include_once 'database.php';
include_once 'util.php';
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

    public static function getGameId()
    {
        return $_SESSION['game_id'] ?? null;
    }

    public static function setGameId(int $gameId)
    {
        $_SESSION['game_id'] = $gameId;
    }

    public static function getLastMove()
    {
        return $_SESSION['last_move'] ?? null;
    }

    public static function setLastMove(int $lastMove)
    {
        $_SESSION['last_move'] = $lastMove;
    }

    public static function pass()
    {
        $db = Database::getInstance();
        $currentGameId = self::getGameId();
        $lastMoveId = self::getLastMove();
        $newMoveId = $db->insertPassMove($currentGameId, $lastMoveId);
        self::setLastMove($newMoveId);

        $currentPlayer = Player::getPlayer();
        Player::setPlayer(1 - $currentPlayer);
    }

    public static function validatePlay($piece, $to): bool
    {
        $player = Player::getPlayer();
        $hand = Hand::getHand($player);
        $board = Board::getBoard();

        if (!$hand[$piece]) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($board) && !hasNeighbour($to, $board)) {
            $_SESSION['error'] = "Board position has no neighbour";
        } elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (playerMustPlayQueen($piece, $hand)) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            return true;
        }
        return false;
    }

    public static function play($piece, $to)
    {
        if(!self::validatePlay($piece, $to)) {
            return;
        }
        $player = Player::getPlayer();
        Board::setPiece($to, $piece);
        Hand::updateHand($player, $piece);
        Player::setPlayer(1 - $player);

        $db = Database::getInstance();
        $newMoveId = $db->insertMove(self::getGameId(), 'play', $piece, $to, self::getLastMove());
        self::setLastMove($newMoveId);
    }

    public static function undo()
    {
        $db = Database::getInstance();
        if (!isset($_SESSION['last_move']) || $_SESSION['last_move'] == null) {
            header('Location: index.php');
            exit(0);
        }
        $previousMoveId = $db->undoLastMove($_SESSION['last_move']);
        if ($previousMoveId === false) {
            $_SESSION['error'] = 'Undo failed. Invalid move or database error';
            return;
        }
        $_SESSION['last_move'] = $previousMoveId;
    }
}
