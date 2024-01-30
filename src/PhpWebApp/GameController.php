<?php

include_once 'Controllers/DatabaseController.php';
include_once 'GameComponents/Hand.php';
include_once 'GameComponents/Player.php';
include_once 'GameComponents/Board.php';
include_once 'Helpers/MoveHelper.php';
include_once 'Helpers/util.php';

class GameController
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

        $db = DatabaseController::getInstance();
        $db->newGame();
    }

    public static function getGameId()
    {
        return $_SESSION['game_id'] ?? null;
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
        $db = DatabaseController::getInstance();
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
            self::setError("Player does not have tile");
        } elseif (isset($board[$to])) {
            self::setError('Board position is not empty');
        } elseif (count($board) && !hasNeighbour($to, $board)) {
            self::setError("Board position has no neighbour");
        } elseif (array_sum($hand) < 11 && !neighboursAreSameColor($player, $to, $board)) {
            self::setError("Board position has opposing neighbour");
        } elseif (playerMustPlayQueen($piece, $hand)) {
            self::setError('Must play queen bee');
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

        $db = DatabaseController::getInstance();
        $newMoveId = $db->insertMove(self::getGameId(), 'play', $piece, $to, self::getLastMove());
        self::setLastMove($newMoveId);
    }

    public static function undo()
    {
        $lastMove = self::getLastMove();
        $db = DatabaseController::getInstance();
        if (!isset($lastMove) || $lastMove == null) {
            header('Location: index.php');
            exit(0);
        }
        $previousMoveId = $db->undoLastMove($lastMove);
        if ($previousMoveId === false) {
            self::setError('Undo failed. Invalid move or database error');
            return;
        }
        self::setLastMove($previousMoveId);
    }

    public static function move($from, $to)
    {
        $player = Player::getPlayer();
        $board = Board::getBoard();
        unset($_SESSION['error']);

        $validMove = MoveHelper::validateMove($from, $to);

        if ($validMove) {
            $tile = array_pop($board[$from]);
            unset($board[$from]);
            $board[$to] = [$tile];
            Board::setBoard($board);
            Player::setPlayer(1 - $player);

            $db = DatabaseController::getInstance();
            $newMoveId = $db->insertMove(self::getGameId(), 'move', $from, $to, self::getLastMove());
            self::setLastMove($newMoveId);
        }
    }

    public static function setError($message)
    {
        $_SESSION['error'] = $message;
    }
}
