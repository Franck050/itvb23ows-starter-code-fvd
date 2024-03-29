<?php

namespace helpers;

use controllers\GameController;
use pieces\Ant;
use pieces\Beetle;
use pieces\Grasshopper;
use pieces\QueenBee;
use pieces\Spider;

class ValidateMoveInsect
{
    public static function validateGrasshopperMove($board, $from, $to): bool
    {
        $grasshopper = new Grasshopper();
        $validMoves = $grasshopper->findAvailableMoves($board, $from);
        if (!in_array($to, $validMoves)) {
            GameController::setError("Grasshopper cannot jump to this tile");
            return false;
        }
        return true;
    }

    public static function validateAntMove($board, $from, $to): bool
    {
        $ant = new Ant();
        $validMoves = $ant->findAvailableMoves($board, $from);
        if (!in_array($to, $validMoves)) {
            GameController::setError("Ant cannot move to this tile");
            return false;
        }
        return true;
    }

    public static function validateSpiderMove($board, $from, $to): bool
    {
        $spider = new Spider();
        $validMoves = $spider->findAvailableMoves($board, $from);
        if (!in_array($to, $validMoves)) {
            GameController::setError("Spider cannot move to this tile");
            return false;
        }
        return true;
    }

    public static function validateQueenBeeMove($board, $from, $to): bool
    {
        $ant = new QueenBee();
        $validMoves = $ant->findAvailableMoves($board, $from);
        if (!in_array($to, $validMoves)) {
            GameController::setError("Queen bee cannot move to this tile");
            return false;
        }
        return true;
    }

    public static function validateBeetleMove(array $board, $from, $to): bool
    {
        $beetle = new Beetle();
        $validMoves = $beetle->findAvailableMoves($board, $from);
        if (!in_array($to, $validMoves)) {
            GameController::setError("Beetle cannot move to this tile");
            return false;
        }
        return true;
    }
}
