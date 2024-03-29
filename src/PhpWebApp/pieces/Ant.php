<?php

namespace pieces;

use controllers\GameController;
use helpers\MoveHelper;

class Ant extends Piece
{
    #[\Override] public function findAvailableMoves(array $board, string $currentLocation): array
    {
        if ($this->beetleBlockCheck($board, $currentLocation)) {
            return [];
        }
        unset($board[$currentLocation]);

        $visited = [$currentLocation => true];
        $tiles = [$currentLocation];
        $possibleMoves = [];

        while (!empty($tiles)) {
            $currentTile = array_shift($tiles);
            $newPositions = $this->getNewPositions($currentTile, $visited);

            foreach ($newPositions as $newPosition) {
                if (!isset($board[$newPosition]) && MoveHelper::hasNeighbour($newPosition, $board)) {
                    $possibleMoves[$newPosition] = true;
                    $tiles[] = $newPosition;
                    $visited[$newPosition] = true;
                }
            }
        }
        return array_keys($possibleMoves);
    }

    private function getNewPositions(string $currentTile, array $visited): array
    {
        $newPositions = [];
        $currentCoordinates = explode(',', $currentTile);

        foreach (GameController::$offsets as $offset) {
            $newCoordinates = $this->calculateNextSpot($currentCoordinates, $offset);
            $newPosition = $this->convertToBoardCoordinates($newCoordinates[0], $newCoordinates[1]);

            if (!isset($visited[$newPosition])) {
                $newPositions[] = $newPosition;
            }
        }
        return $newPositions;
    }
}
