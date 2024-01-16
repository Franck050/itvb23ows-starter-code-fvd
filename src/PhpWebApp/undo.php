<?php

session_start();

include_once 'database.php';

$db = Database::getInstance();
$stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
$stmt->execute();
$result = $stmt->get_result()->fetch_array();
$_SESSION['last_move'] = $result[5];
$db->setState($result[6]);
header('Location: index.php');
