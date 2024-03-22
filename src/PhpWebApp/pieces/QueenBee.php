<?php

namespace pieces;

use controllers\GameController;
use helpers\MoveHelper;

class QueenBee extends Piece
{

    #[\Override] public function findAvailableMoves(array $board, string $currentLocation): array
    {
        $availableMoves = [];
        $offsets = GameController::$offsets;
        list($currentX, $currentY) = explode(',', $currentLocation);

        foreach ($offsets as $offset) {
            $newX = $currentX + $offset[0];
            $newY = $currentY + $offset[1];
            $newPosition = $newX . ',' . $newY;

            if ($this->validatePositionForQueenBee($newPosition, $board)) {
                $availableMoves[] = $newPosition;
            }
        }

        return $availableMoves;
    }

    private function validatePositionForQueenBee(string $pos, array $board): bool
    {
        if (isset($board[$pos]) || MoveHelper::checkForHiveSplit($board) || !MoveHelper::hasNeighbour($pos, $board)) {
            return false;
        }
        return true;
    }
}