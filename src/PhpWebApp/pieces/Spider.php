<?php

namespace pieces;

use controllers\GameController;
use helpers\MoveHelper;

class Spider extends Piece
{
    #[\Override] public function findAvailableMoves(array $board, string $currentLocation): array
    {
        if ($this->beetleBlockCheck($board, $currentLocation)) {
            return [];
        }

        $visitedPositions = [$currentLocation => true];
        $tiles = [[$currentLocation, 0]];
        $possibleMoves = [];

        unset($board[$currentLocation]);

        while (!empty($tiles)) {
            [$currentTile, $tileDepth] = array_shift($tiles);
            $possibleMoves = $this->processCurrentTile($currentTile, $tileDepth, $tiles, $possibleMoves, $visitedPositions, $board);
        }

        return $possibleMoves;
    }

    private function processCurrentTile($currentTile, $tileDepth, &$tiles, $possibleMoves, &$visitedPositions, $board): array
    {
        if ($tileDepth == 3) {
            return $possibleMoves;
        }

        $currentCoordinates = explode(',', $currentTile);
        foreach (GameController::$offsets as $offset) {
            $newCoordinates = $this->calculateNextSpot($currentCoordinates, $offset);
            $newPosition = $this->convertToBoardCoordinates($newCoordinates[0], $newCoordinates[1]);

            if ($this->isValid($newPosition, $board, $visitedPositions)) {
                if ($tileDepth == 2) {
                    $possibleMoves[] = $newPosition;
                }
                $tiles[] = [$newPosition, $tileDepth + 1];
                $visitedPositions[$newPosition] = true;
            }
        }

        return $possibleMoves;
    }

    private function isValid($currentTile, $board, $visited): bool
    {
        return !isset($visited[$currentTile])
            && !isset($board[$currentTile])
            && MoveHelper::hasNeighbour($currentTile, $board);
    }
}
