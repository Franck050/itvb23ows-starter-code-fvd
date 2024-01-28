<?php

session_start();

include_once 'database.php';

$db = Database::getInstance();

// Controleer of er een laatste zet is.
if (!isset($_SESSION['last_move']) || $_SESSION['last_move'] == null) {
    // Als er geen laatste zet is, doe niets of geef een bericht.
    header('Location: index.php'); // Terug naar de hoofdpagina.
    exit(0);
}

$stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
$stmt->execute();
$result = $stmt->get_result()->fetch_array();

// Update de sessie met de informatie van de laatste zet.
$_SESSION['last_move'] = $result[5];
$db->setState($result[6]);

header('Location: index.php');
