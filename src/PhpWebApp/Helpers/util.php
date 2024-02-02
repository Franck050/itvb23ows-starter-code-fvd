<?php

$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

function isNeighbour($a, $b): bool
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

function getNeighbours($a): array
{
    $board = Board::getBoard();
    $neighbours = [];
    $b = explode(',', $a);
    foreach ($GLOBALS['OFFSETS'] as $pq) {
        $p = $b[0] + $pq[0];
        $q = $b[1] + $pq[1];
        $position = $p . "," . $q;
        if (
            isset($board[$position]) &&
            isNeighbour($a, $position)
        ) {
            $neighbours[] = $position;
        }
    }
    return $neighbours;
}

function hasNeighbour($a, $board) : bool
{
    $b = explode(',', $a);

    foreach ($GLOBALS['OFFSETS'] as $pq) {
        $p = $b[0] + $pq[0];
        $q = $b[1] + $pq[1];

        $position = $p . "," . $q;

        if (isset($board[$position]) &&
            isNeighbour($a, $position)
        ) {
            return true;
        }
    }
    return false;
}

function neighboursAreSameColor($player, $a, $board): bool
{
    foreach ($board as $b => $st) {
        if (!$st) continue;
        $c = $st[count($st) - 1][0];
        if ($c != $player && isNeighbour($a, $b)) return false;
    }
    return true;
}

function len($tile): int
{
    return $tile ? count($tile) : 0;
}

function playerMustPlayQueen($piece, $hand): bool
{
    return $piece != 'Q' && array_sum($hand) <= 8 && $hand['Q'];
}

function isValidGrasshopperMove($from, $to, $board): bool
{
    if (!$from || !isset($board[$from])) {
        return false; // Sprinkhaan moet van een bestaande positie komen.
    }

    $fromCoords = explode(',', $from);
    $toCoords = explode(',', $to);

    $dx = $toCoords[0] - $fromCoords[0];
    $dy = $toCoords[1] - $fromCoords[1];

    if (abs($dx) !== abs($dy) && $dx !== 0 && $dy !== 0) {
        return false; // Moet in een rechte lijn bewegen.
    }

    $stepX = $dx ? $dx / abs($dx) : 0;
    $stepY = $dy ? $dy / abs($dy) : 0;
    $x = $fromCoords[0] + $stepX;
    $y = $fromCoords[1] + $stepY;

    $jumpedOverPiece = false;

    while ($x != $toCoords[0] || $y != $toCoords[1]) {
        if (!isset($board["$x,$y"])) {
            return false; // Mag niet over lege velden springen.
        }
        $jumpedOverPiece = true;
        $x += $stepX;
        $y += $stepY;
    }

    return $jumpedOverPiece && !isset($board["$x,$y"]); // Moet over minimaal één steen springen en mag niet naar een bezet veld springen.
}