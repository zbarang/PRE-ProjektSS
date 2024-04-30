<?php
session_start();

// Überprüfen, ob der Benutzer nicht angemeldet ist
if (!isset($_SESSION["userid"]) && basename($_SERVER["PHP_SELF"]) !== 'login.php') {
    // Wenn der Benutzer nicht angemeldet ist und sich nicht auf der Login-Seite befindet, wird er zur Login-Seite weitergeleitet
    header("Location: login.php");
    exit;
}
?>