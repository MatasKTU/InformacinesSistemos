<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in and has a role
if (isset($_SESSION['role'])) {
    echo json_encode(['role' => $_SESSION['role']]);
} else {
    echo json_encode(['error' => 'No role found', 'role' => null]);
}
?>
