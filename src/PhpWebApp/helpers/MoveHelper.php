<?php

namespace helpers;
use controllers\GameController;
use gameComponents\Board;
use gameComponents\Hand;
use gameComponents\Player;

class MoveHelper
{
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
        if (!count($to) && !count($board)) {
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

    private static function validatePosition($pos): bool
    {
        $board = Board::getBoard();
        $player = Player::getPlayer();
        $hand = Hand::getHand($player);
        $isValid = true;

        if (isset($board[$pos])) {
            $isValid = false;
        } elseif(count($board) && !self::hasNeighBour($pos, $board)){
            $isValid = false;
        } elseif(array_sum($hand) < 11 && !self::neighboursAreSameColor($player, $pos, $board) ) {
            $isValid = false;
        }
        return $isValid;
    }

    public static function validateMove($from, $to): bool
    {
        $board = Board::getBoard();
        $player = Player::getPlayer();
        $hand = Hand::getHand($player);

        if ($from == $to) {
            GameController::setError("Tile must move");
            $isValid = false;
        } elseif (!isset($board[$from])) {
            GameController::setError("Board position is empty");
            $isValid = false;
        } elseif (
            isset($board[$from][count($board[$from]) - 1]) &&
            $board[$from][count($board[$from]) - 1][0] != $player
        ) {
            GameController::setError("Tile is not owned by player");
            $isValid = false;
        } elseif ($hand['Q']) {
            GameController::setError("Queen bee is not played");
            $isValid = false;
        } else {
            $tile = array_pop($board[$from]);
            if (count($board[$from]) == 0) {
                unset($board[$from]);
            }

            if (isset($board[$to]) && !$tile[1] == ['B']) {
                GameController::setError("Tile is already taken");
                $isValid = false;
            } elseif (!self::hasNeighBour($to, $board) || self::checkForHiveSplit($board)) {
                GameController::setError("Move would split hive");
                $isValid = false;
            } elseif (self::slide($from, $to, $board)) {
                GameController::setError("Slide is not allowed");
                $isValid = false;
            } else {
                $isValid = match ($tile[1]) {
                    'A' => ValidateMoveInsect::validateAntMove($board, $from, $to),
                    'B' => ValidateMoveInsect::validateBeetleMove($board, $from, $to),
                    'G' => ValidateMoveInsect::validateGrasshopperMove($board, $from, $to),
                    'Q' => ValidateMoveInsect::validateQueenBeeMove($board, $from, $to),
                    'S' => ValidateMoveInsect::validateSpiderMove($board, $from, $to),
                    default => false,
                };
            }
        }

        return $isValid;
    }

    public static function slide($from, $to, $board): bool
    {
        if (!self::isNeighbour($from, $to) || !self::isMovePossible($to, $board)) {
            return false;
        }
        if (!self::isPositionAvailable($from, $to, $board)) {
            return false;
        }
        return self::doesNotSplitHive($from, $to, $board);
    }

    private static function isMovePossible($to, $board): bool
    {
        return !isset($board[$to]) && self::hasNeighbour($to, $board);
    }

    private static function doesNotSplitHive($from, $to, $board): bool
    {
        $tempBoard = $board;
        $tempBoard[$to] = $tempBoard[$from];
        unset($tempBoard[$from]);

        return !self::checkForHiveSplit($tempBoard);
    }

    public static function checkForHiveSplit($board): bool
    {
        if (empty($board)) {
            return false;
        }

        $visited = [];
        $positions = array_keys($board);
        $queue = self::initializeQueue($board);

        self::performBreadthFirstSearch($board, $queue, $visited);

        return self::isHiveSplit($positions, $visited);
    }

    private static function initializeQueue($board): array
    {
        $positions = array_keys($board);
        return [reset($positions)];
    }

    private static function performBreadthFirstSearch($board, &$queue, &$visited): void
    {
        while (!empty($queue)) {
            $current = array_shift($queue);
            $visited[$current] = true;

            $neighbors = self::getNeighbours($current);
            foreach ($neighbors as $neighbor) {
                if (isset($board[$neighbor]) && !isset($visited[$neighbor])) {
                    $queue[] = $neighbor;
                }
            }
        }
    }

    private static function isHiveSplit($allPositions, $visited): bool
    {
        foreach ($allPositions as $position) {
            if (!isset($visited[$position])) {
                return true;
            }
        }
        return false;
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
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && self::isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    public static function playerMustPlayQueen($piece, $hand): bool
    {
        return $piece != 'Q' && array_sum($hand) <= 8 && $hand['Q'];
    }

    public static function isPositionAvailable($from, $to, $board): bool
    {
        return isset($board[$from]) && $board[$from] || isset($board[$to]) && $board[$to];
    }
}
