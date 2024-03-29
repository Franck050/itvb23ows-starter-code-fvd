<?php

namespace pieces;

use controllers\GameController;
use helpers\MoveHelper;

class Beetle extends Piece
{

    #[\Override] public function findAvailableMoves(array $board, string $currentLocation): array
    {
        $availableMoves = [];
        list($currentX, $currentY) = explode(',', $currentLocation);

        foreach (GameController::$offsets as $offset) {
            $PosX = $currentX + $offset[0];
            $PosY = $currentY + $offset[1];
            $newPosition = "$PosX,$PosY";

            if ($this->isPositionAvailableForBeetle($newPosition, $board, $currentLocation)) {
                $availableMoves[] = $newPosition;
            }
        }
        return $availableMoves;
    }


    private function isPositionAvailableForBeetle(string $position, array $board, string $currentLocation): bool
    {
        if ($position !== $currentLocation && MoveHelper::isNeighbour($position, $currentLocation)) {
            if (!isset($board[$position]) || $this->canBeetleClimb($position, $board)) {
                return true;
            }
        }
        return false;
    }

    private function canBeetleClimb(string $position, array $board): bool
    {
        return isset($board[$position]);
    }
}
