<?php
$DB_HOST = 'localhost';
$DB_NAME = 'vape_store';
$DB_USER = 'root';
$DB_PASS = 'Fauzan@123';

// Create connection
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Set charset to prevent issues with special characters
if (!$mysqli->set_charset("utf8")) {
    die("Error loading charset utf8: " . $mysqli->error);
}

// Enable exception mode for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Optional: Set SQL mode
$mysqli->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");

?>
