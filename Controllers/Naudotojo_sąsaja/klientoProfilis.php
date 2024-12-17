<?php
require_once 'config.php';

// Patikriname, ar ID yra perduotas
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Gauname kliento informaciją iš Naudotojas lentelės
    $stmt = $pdo->prepare("SELECT vardas, pavarde, el_pastas, telefono_nr FROM naudotojas WHERE id_Naudotojas = ?");
    $stmt->execute([$id]);
    $klientas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$klientas) {
        die("Klientas nerastas.");
    }
} else {
    die("Nenurodytas kliento ID.");
}

// Įtraukiame HTML failą
include '../../Sablonai/kliento_profilis.html';
?>