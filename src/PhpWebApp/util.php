<?php

$GLOBALS['OFFSETS'] = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

function isNeighbour($a, $b): bool
{
    $a = explode(',', $a);
    $b = explode(',', $b);

    return ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) ||
           ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) ||
           ($a[0] + $a[1] == $b[0] + $b[1]);
}



function hasNeighbour($a, $board) : bool
{
    foreach (array_keys($board) as $b) {
        if (isNeighbour($a, $b)) {
            return true;
        }
    }
    return false;
}

function getNeighbours($tile): array
{
    $neighbours = [];
    list($x, $y) = explode(',', $tile);

    foreach ($GLOBALS['OFFSETS'] as $offset) {
        $neighbours[] = ($x + $offset[0]) . ',' . ($y + $offset[1]);
    }

    return $neighbours;
}

function neighboursAreSameColor($player, $a, $board): bool
{
    foreach ($board as $b => $st) {
        if (!$st) {
            continue;
        }
        $c = $st[count($st) - 1][0];
        if ($c != $player && isNeighbour($a, $b)) {
            return false;
        }
    }
    return true;
}

function len($tile): int
{
    return $tile ? count($tile) : 0;
}

function slide($board, $from, $to): bool
{
    if (!hasNeighbour($to, $board) || !isNeighbour($from, $to)) {
        $result = false;
    } else {
        $b = explode(',', $to);
        $common = [];
        foreach ($GLOBALS['OFFSETS'] as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if (isNeighbour($from, $p.",".$q)) {
                $common[] = $p.",".$q;
            }
        }
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]) {
            $result = false;
        } else {
            $minLength = min(len($board[$common[0]]), len($board[$common[1]]));
            $maxLength = max(len($board[$from]), len($board[$to]));

            $result = $minLength <= $maxLength;
        }
    }

    return $result;
}

function isValidPosition($position, $board, $player): bool
{
    if (!empty($board[$position])) {
        return false;
    }

    if (empty($board)) {
        return $position === '0,0';
    }

    return hasNeighbour($position, $board) &&
        (count($board) === 1 || neighboursAreSameColor($player, $position, $board));
}

function setStartingHand() {
    $_SESSION['hand'] = [
        0 => [
            "Q" => 1,
            "B" => 2,
            "S" => 2,
            "A" => 3,
            "G" => 3
        ],
        1 => [
            "Q" => 1,
            "B" => 2,
            "S" => 2,
            "A" => 3,
            "G" => 3
        ]
    ];
}

function getMoves($board, $player): array
{
    $boardCount = count($board);
    if ($boardCount === 0) {
        return ['0,0'];
    }

    if ($boardCount === 1) {
        return ['0,1', '1,0', '-1,0', '0,-1', '1,-1', '-1,1'];
    }

    $potentialMoves = [];

    foreach ($board as $pos => $tile) {
        if ($tile[0][0] !== $player) {
            continue;
        }

        foreach (getNeighbours($pos) as $neighbour) {
            if (!isset($board[$neighbour]) && neighboursAreSameColor($player, $neighbour, $board)) {
                $potentialMoves[$neighbour] = true;
            }
        }
    }

    return array_keys($potentialMoves);
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
