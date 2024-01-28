<?php

session_start();

include_once 'database.php';

$_SESSION['board'] = [];
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
$_SESSION['player'] = 0;
$_SESSION['last_move'] = null;

$db = Database::getInstance();
$db->prepare('INSERT INTO games VALUES ()')->execute();
$_SESSION['game_id'] = $db->getConnection()->insert_id;

header('Location: index.php');
