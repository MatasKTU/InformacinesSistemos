<?php
// Database configuration
$host = '127.0.0.1';
$port = '3306';
$db = 'zvejybos_prekiu_parduotuve';
$user = 'root';
$pass = '';
$sslMode = 'None';

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set MySQL session configurations if necessary
if (!$conn->set_charset("utf8")) {
    die("Error loading character set utf8: " . $conn->error);
}
?>
