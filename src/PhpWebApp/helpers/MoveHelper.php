<?php

namespace helpers;
use controllers\GameController;
use gameComponents\Board;
use gameComponents\Hand;
use gameComponents\Player;
use pieces\Grasshopper;

class MoveHelper
{
    public function __construct()
    {
    }

    public static function getPositions(): array
    {
        $board = Board::getBoard();
        $to = [];
        foreach (GameController::$offsets as $pq) {
            foreach (array_keys($board) as $pos) {
                $pq2 = explode(',', $pos);
                $to[] = ($pq[0] + $pq2[0]).','.($pq[1] + $pq2[1]);

            }
        }
        $to = array_unique($to);
        if (!count($to) and !count($board)) {
            $to[] = '0,0';
        }
        return $to;
    }
    public static function getPossibleMoves(): array
    {
        $possible = [];
        foreach (self::getPositions() as $to){
            if  (self::validatePosition($to)) {
                $possible[] = $to;
            }
        }
        return $possible;
    }

    public static function getPlayerPositions(): array
    {
        $playerPositions = [];
        foreach (Board::getBoard() as $key => $value) {
            if (isset($value[0][0]) && $value[0][0] == Player::getPlayer()) {
                $playerPositions[] = $key;
            }
        }
        return $playerPositions;
    }

    private static function validatePosition($pos): bool
    {
        $board = Board::getBoard();
        $player = Player::getPlayer();
        $hand = Hand::getHand($player);

        if (isset($board[$pos])) {
            return false;
        } else if(count($board) && !self::hasNeighBour($pos, $board)){
            return false;
        } else if(array_sum($hand) < 11 && !self::neighboursAreSameColor($player, $pos, $board) ) {
            return false;
        }
        return true;
    }

    public static function validateMove($from, $to): bool
    {
        $board = Board::getBoard();
        $player = Player::getPlayer();
        $hand = Hand::getHand($player);
        if (!isset($board[$from])) {
            GameController::setError("Board position is empty");
        } elseif ($from == $to) {
            GameController::setError("Tile must move");
        } elseif (
            isset($board[$from][count($board[$from]) - 1]) &&
            $board[$from][count($board[$from]) - 1][0] != $player
        ) {
            GameController::setError("Tile is not owned by player");
        } elseif ($hand['Q']) {
            GameController::setError("Queen bee is not played");
        } else {
            $tile = array_pop($board[$from]);
            unset($board[$from]);

            if (!self::hasNeighbour($to, $board) || self::getSplitTiles($board)) {
                GameController::setError("Move would split hive");
            } elseif (isset($board[$to]) && $tile[1] != "B") {
                GameController::setError("Tile not empty");
            } elseif (($tile[1] == "Q" || $tile[1] == "B") && !self::slide($from, $to)) {
                GameController::setError("Tile must slide");
            } elseif ($tile[1] == 'G') {
                $gh = new Grasshopper();
                $validMoves = $gh->findAvailableMoves($board, $from);
                if (!in_array($to, $validMoves)) {
                    $_SESSION['error'] = "Grasshopper cannot jump";
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    private static function slide($from, $to): bool {
        $board = Board::getBoard();
        if (!self::hasValidMovement($board, $from, $to)) {
            return false;
        }

        $toCoordinates = explode(',', $to);
        $commonNeighbors = self::findCommonNeighbors($toCoordinates, $from);

        if (self::isMovementBlocked($board, $commonNeighbors, $from, $to)) {
            return false;
        }

        return self::canSlideBasedOnLength($board, $commonNeighbors, $from, $to);
    }

    private static function hasValidMovement($board, $from, $to): bool {
        return self::hasNeighbour($to, $board) && self::isNeighbour($from, $to);
    }

    private static function findCommonNeighbors($toCoordinates, $from): array {
        $commonNeighbors = [];
        foreach (GameController::$offsets as $offset) {
            $neighborCoordinates = self::getNeighborCoordinates($toCoordinates, $offset);
            if (self::isNeighbour($from, implode(",", $neighborCoordinates))) {
                $commonNeighbors[] = implode(",", $neighborCoordinates);
            }
        }
        return $commonNeighbors;
    }

    private static function getNeighborCoordinates($baseCoordinates, $offset): array {
        return [$baseCoordinates[0] + $offset[0], $baseCoordinates[1] + $offset[1]];
    }

    private static function isMovementBlocked($board, $commonNeighbors, $from, $to): bool {
        foreach ($commonNeighbors as $neighbor) {
            if (isset($board[$neighbor]) && $board[$neighbor]) {
                return false;
            }
        }

        return (!isset($board[$from]) || !$board[$from]) && (!isset($board[$to]) || !$board[$to]);
    }

    private static function canSlideBasedOnLength($board, $commonNeighbors, $from, $to): bool {
        $lengths = array_map(function ($position) use ($board) {
            $item = $board[$position] ?? '';
            if (is_string($item)) {
                return strlen($item);
            } else {
                return count($item);
            }
        }, array_merge($commonNeighbors, [$from, $to]));

        return min($lengths[0], $lengths[1]) <= max($lengths[2], $lengths[3]);
    }

    private static function getSplitTiles($board): array
    {
        $all = array_keys($board);
        $queue = [array_shift($all)];

        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach (GameController::$offsets as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];

                $position = $p . "," . $q;

                if (in_array($position, $all)) {
                    $queue[] = $position;
                    $all = array_diff($all, [$position]);
                }
            }
        }

        return $all;
    }

    public static function isNeighbour($a, $b): bool
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        if (
            $a[0] == $b[0] && abs($a[1] - $b[1]) == 1 ||
            $a[1] == $b[1] && abs($a[0] - $b[0]) == 1 ||
            $a[0] + $a[1] == $b[0] + $b[1]
        ) {
            return true;
        }

        return false;
    }

    public static function getNeighbours($a): array
    {
        $board = Board::getBoard();
        $neighbours = [];
        $b = explode(',', $a);
        foreach (GameController::$offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            $position = $p . "," . $q;
            if (
                isset($board[$position]) &&
                self::isNeighbour($a, $position)
            ) {
                $neighbours[] = $position;
            }
        }
        return $neighbours;
    }

    public static function hasNeighbour($a, $board) : bool
    {
        $b = explode(',', $a);

        foreach (GameController::$offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];

            $position = $p . "," . $q;

            if (isset($board[$position]) &&
                self::isNeighbour($a, $position)
            ) {
                return true;
            }
        }
        return false;
    }

    public static function neighboursAreSameColor($player, $a, $board): bool
    {
        foreach ($board as $b => $st) {
            if (!$st) continue;
            $c = $st[count($st) - 1][0];
            if ($c != $player && self::isNeighbour($a, $b)) return false;
        }
        return true;
    }

    public static function playerMustPlayQueen($piece, $hand): bool
    {
        return $piece != 'Q' && array_sum($hand) <= 8 && $hand['Q'];
    }
}
