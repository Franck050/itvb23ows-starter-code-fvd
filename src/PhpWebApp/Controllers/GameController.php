<?php

namespace Controllers;

use GameComponents\Board;
use GameComponents\Hand;
use GameComponents\Player;
use Helpers\MoveHelper;

require_once __DIR__ . '/../Helpers/util.php';


class GameController
{

    function __construct()
    {
    }

    public static function isGameStarted(): bool
    {
        return !isset($_SESSION['game_started']) || $_SESSION['game_started'] !== true;
    }

    public static function startGame()
    {
        $_SESSION['game_started'] = true;
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
        if (!self::validatePlay($piece, $to)) {
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
        $db = DatabaseController::getInstance();
        $lastMove = $db->getLastMove(self::getGameId());

        if ($lastMove[5] == null) {
            self::restart();
            return;
        }
        Player::setPlayer(1 - Player::getPlayer());
        $db->deleteMove($lastMove[0]);
        $previousMove = $db->getMove($lastMove[5]);

        if (!$previousMove) {
            self::restart();
            return;
        }
        $db->setState($previousMove[6]);
        self::setLastMove($lastMove[5]);
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

    public static function checkWin($player): bool
    {
        $opponent = abs($player - 1);
        foreach (Board::getBoard() as $pos => $tiles) {
            $topTile = end($tiles);
            if ($topTile[0] == $opponent && $topTile[1] == 'Q') {
                if (count(getNeighbours($pos)) == 6) {
                    return true;
                }
            }
        }
        return false;
    }
}
