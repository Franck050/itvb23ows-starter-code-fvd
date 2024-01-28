<?php

session_start();

include_once 'database.php';

$db = Database::getInstance();

if (!isset($_SESSION['last_move']) || $_SESSION['last_move'] == null) {
    header('Location: index.php');
    exit(0);
}

$stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
$stmt->execute();
$result = $stmt->get_result()->fetch_array();

$_SESSION['last_move'] = $result[5];
$db->setState($result[6]);

header('Location: index.php');
