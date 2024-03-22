<?php

namespace pieces;

use controllers\GameController;

class Grasshopper extends Piece
{
    #[\Override] public function findAvailableMoves(array $board, string $currentLocation): array
    {
        if ($this->beetleBlockCheck($board, $currentLocation)) {
            return [];
        }

        $currentCoordinates = explode(',', $currentLocation);
        $validMoves = [];

        foreach (GameController::$offsets as $direction) {
            $newPosition = $this->locateNextFreeSpot($currentCoordinates, $direction, $board);
            if ($newPosition !== null) {
                $validMoves[] = $newPosition;
            }
        }
        return $validMoves;
    }

    private function locateNextFreeSpot(array $currentCoordinates, array $direction, array $board): ?string
    {
        list($x, $y) = $currentCoordinates;
        do {
            list($x, $y) = $this->calculateNextSpot([$x, $y], $direction);
            $nextSpot = $this->convertToBoardCoordinates($x, $y);
        } while (isset($board[$nextSpot]));
        return $nextSpot;
    }
}
