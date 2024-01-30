<?php

class Board
{
    public function __construct()
    {
    }

    public static function getBoard() : array
    {
        if (!isset($_SESSION['board'])) {
            $_SESSION['board'] = [];
            return $_SESSION['board'];
        }
        return $_SESSION['board'];
    }

    public static function setBoard(array $board)
    {
        $_SESSION['board'] = $board;
    }

    public static function setPiece($to, $piece) {
        if (!isset($_SESSION['board'])) {
            $_SESSION['board'] = [];
        }
        $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
    }
}