<?php
$DB_HOST = 'localhost';
$DB_NAME = 'vape_store';
$DB_USER = 'root';
$DB_PASS = '';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}
?>
