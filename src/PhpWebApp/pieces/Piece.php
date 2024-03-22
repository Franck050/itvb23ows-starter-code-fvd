<?php

namespace pieces;

use controllers\GameController;
use gameComponents\Board;
use gameComponents\Player;

abstract class Piece
{
    public abstract function findAvailableMoves(array $board, string $currentLocation): array;

    public function beetleBlockCheck($board, $from): bool
    {
        return isset($board[$from]) &&
            count($board[$from]) > 1 &&
            $board[$from][count($board[$from]) - 1][0] != Player::getPlayer();
    }

    public function calculateNextSpot(array $coordinates, array $direction): array
    {
        $x = $coordinates[0] + $direction[0];
        $y = $coordinates[1] + $direction[1];
        return [$x, $y];
    }

    public function convertToBoardCoordinates(int $x, int $y): string
    {
        return $x . ',' . $y;
    }
}