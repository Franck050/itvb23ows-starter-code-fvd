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

    public static function move($from, $to)
    {
        $player = Player::getPlayer();
        $board = Board::getBoard();
        $hand = Hand::getHand($player);
        unset($_SESSION['error']);

        if (!isset($board[$from])) {
            $_SESSION['error'] = 'Board position is empty';
        } elseif ($board[$from][count($board[$from])-1][0] != $player) {
            $_SESSION['error'] = "Tile is not owned by player";
        } elseif ($hand['Q']) {
            $_SESSION['error'] = "Queen bee is not played";
        } else {
            $tile = array_pop($board[$from]);
            if (empty($board[$from])) {
                unset($board[$from]);
            }
            if (!hasNeighbour($to, $board)) {
                $_SESSION['error'] = "Move would split hive";
            } else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($GLOBALS['OFFSETS'] as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($from == $to) {
                        $_SESSION['error'] = 'Tile must move';
                    }
                    elseif (isset($board[$to]) && $tile[1] != "B") {
                        $_SESSION['error'] = 'Tile not empty';
                    }
                    elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!slide($board, $from, $to)) {
                            $_SESSION['error'] = 'Tile must slide';
                        }
                    }
                }
            }
            if (isset($_SESSION['error'])) {
                if (isset($board[$from])) {
                    $board[$from][] = $tile;
                } else {
                    $board[$from] = [$tile];
                }
            } else {
                if (isset($board[$to])) {
                    $board[$to][] = $tile;
                } else {
                    $board[$to] = [$tile];
                }
                Player::setPlayer(1 - $player);
                $db = Database::getInstance();
                $newMoveId = $db->insertMove(self::getGameId(), 'move', $from, $to, self::getLastMove());
                self::setLastMove($newMoveId);
            }
            Board::setBoard($board);
        }
    }
}
