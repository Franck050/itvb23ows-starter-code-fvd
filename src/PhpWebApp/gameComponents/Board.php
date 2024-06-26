<?php

namespace gameComponents;

class Board
{
    public static function getBoard(): array
    {
        if (!isset($_SESSION['board'])) {
            $_SESSION['board'] = [];
            return $_SESSION['board'];
        }
        return $_SESSION['board'];
    }

    public static function setBoard(array $board): void
    {
        $_SESSION['board'] = $board;
    }

    public static function setPiece($to, $piece): void
    {
        if (!isset($_SESSION['board'])) {
            $_SESSION['board'] = [];
        }
        $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
    }
}
