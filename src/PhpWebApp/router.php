<?php

session_start();

include_once 'Game.php';

$actionMappings = [
    'Restart' => function() { Game::restart(); },
    'Pass' => function() { Game::pass(); },
    'Play' => function() { if (isset($_POST['piece'], $_POST['to'])) Game::play($_POST['piece'], $_POST['to']); },
    'Undo' => function() { Game::undo(); },
    'Move' => function() { Game::move($_POST['from'], $_POST['to']);}
];

if (isset($_POST['action']) && array_key_exists($_POST['action'], $actionMappings)) {
    $actionMappings[$_POST['action']]();

    header('Location: index.php');
    exit;
}

echo 'ERROR';
