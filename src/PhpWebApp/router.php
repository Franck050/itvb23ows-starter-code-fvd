<?php

session_start();

include_once 'GameController.php';

$actionMappings = [
    'Restart' => function() { GameController::restart(); },
    'Pass' => function() { GameController::pass(); },
    'Play' => function() { if (isset($_POST['piece'], $_POST['to'])) GameController::play($_POST['piece'], $_POST['to']); },
    'Undo' => function() { GameController::undo(); },
    'Move' => function() { GameController::move($_POST['from'], $_POST['to']);}
];

if (isset($_POST['action']) && array_key_exists($_POST['action'], $actionMappings)) {
    $actionMappings[$_POST['action']]();

    header('Location: index.php');
    exit;
}

echo 'ERROR';
