<?php

session_start();

include_once 'Game.php';

if (isset($_POST['action']) && $_POST['action'] === 'Restart') {
    Game::restart();

    header('Location: index.php');
    exit;
}
if (isset($_POST['action']) && $_POST['action'] === 'Pass') {
    Game::pass();

    header('Location: index.php');
    exit;
}
if (isset($_POST['action']) && $_POST['action'] === 'Play') {
    Game::play($_POST['piece'], $_POST['to']);

    header('Location: index.php');
    exit;
}

echo 'FOUT';
