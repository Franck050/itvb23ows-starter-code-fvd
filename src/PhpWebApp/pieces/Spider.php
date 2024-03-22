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
        $depth = 0;
        $tiles = [[$currentLocation, 0]];
        $possibleMoves = [];

        unset($board[$currentLocation]);

        while (!empty($tiles)) {
            list($currentTile, $tileDepth) = array_shift($tiles);

            if ($tileDepth > $depth) {
                if ($tileDepth == 3) {
                    break;
                }
                $depth = $tileDepth;
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
        }
        return $possibleMoves;
    }

    private function isValid($currentTile, $board, &$visited): bool
    {
        return !isset($visited[$currentTile])
            && !isset($board[$currentTile])
            && MoveHelper::hasNeighbour($currentTile, $board);
    }
}