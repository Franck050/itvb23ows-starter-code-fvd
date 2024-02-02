<?php

namespace pieces;

use controllers\GameController;

class Grasshopper
{
    public function findAvailableMoves(array $board, string $currentLocation): array
    {
        $currentCoordinates = explode(',', $currentLocation);
        $validMoves = [];
        $directions = GameController::$offsets;

        foreach ($directions as $direction) {
            $newPosition = $this->locateNextFreeSpot($currentCoordinates, $direction, $board);
            if ($newPosition) {
                $validMoves[] = $newPosition;
            }
        }

        return $validMoves;
    }

    private function locateNextFreeSpot(array $currentCoordinates, array $direction, array $board): ?string
    {
        list($nextX, $nextY) = $this->calculateNextSpot($currentCoordinates, $direction);

        $nextSpot = $this->convertToBoardCoordinates($nextX, $nextY);

        if (!isset($board[$nextSpot])) {
            return null;
        }

        while (isset($board[$nextSpot])) {
            list($nextX, $nextY) = $this->calculateNextSpot([$nextX, $nextY], $direction);
            $nextSpot = $this->convertToBoardCoordinates($nextX, $nextY);
        }

        return $nextSpot;
    }

    private function calculateNextSpot(array $coordinates, array $direction): array
    {
        $x = $coordinates[0] + $direction[0];
        $y = $coordinates[1] + $direction[1];
        return [$x, $y];
    }

    private function convertToBoardCoordinates(int $x, int $y): string
    {
        return $x . ',' . $y;
    }
}
