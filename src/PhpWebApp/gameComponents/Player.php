<?php

namespace gameComponents;

class Player
{
    public function __construct()
    {
    }

    public static function getPlayer()
    {
        if (!isset($_SESSION['player'])) {
            $_SESSION['player'] = 0;
            return $_SESSION['player'];
        }
        return $_SESSION['player'];
    }

    public static function setPlayer(int $player): void
    {
        $_SESSION['player'] = $player;
    }

    public static function playerPositions($board, $player, $from): bool
    {
        return $board[$from][count($board[$from]) - 1][0] == $player;
    }
}
