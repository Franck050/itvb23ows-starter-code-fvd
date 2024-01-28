<?php

class Board
{
    public function __construct()
    {
    }

    public static function getBoard()
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
}