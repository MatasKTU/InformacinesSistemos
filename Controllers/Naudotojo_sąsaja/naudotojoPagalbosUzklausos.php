<?php
session_start();
require_once 'config.php';

// Patikriname, ar naudotojas yra prisijungęs
if (!isset($_SESSION['id_Naudotojas'])) {
    die("Naudotojas neprisijungęs. Prašome prisijungti.");
}

$naudotojasId = $_SESSION['id_Naudotojas'];

// Gauname prisijungusio kliento pagalbos užklausas
$stmt = $pdo->prepare("
    SELECT p.pavadinimas, p.data, p.aprasas, b.name AS busena
    FROM pagalbos_uzklausa p
    JOIN uzklausos_busena b ON p.busena = b.id_Uzklausos_busena
    WHERE p.fk_Klientas_id_Naudotojas = ?
");
$stmt->execute([$naudotojasId]);
$uzklausos = $stmt->fetchAll(PDO::FETCH_ASSOC);
include '../../Sablonai/Naud_pagalbos_uzklausa.html';
?>