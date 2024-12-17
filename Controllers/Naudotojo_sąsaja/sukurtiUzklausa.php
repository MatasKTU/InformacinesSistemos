<?php
session_start();
require_once 'config.php';

// Patikriname, ar naudotojas yra prisijungęs
if (!isset($_SESSION['id_Naudotojas'])) {
    die("Naudotojas neprisijungęs. Prašome prisijungti.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pavadinimas = $_POST['pavadinimas'] ?? '';
    $aprasas = $_POST['aprasas'] ?? '';
    $data = date('Y-m-d');
    $busena = 2; // "Laukiama" būsenos ID pagal jūsų enum lentelę
    $naudotojasId = $_SESSION['id_Naudotojas'];
    $administratoriusId = 1; // Pakeiskite į tinkamą administratoriaus ID

    try {
        $stmt = $pdo->prepare("
            INSERT INTO pagalbos_uzklausa (pavadinimas, data, aprasas, busena, fk_Klientas_id_Naudotojas, fk_Administratorius_id_Naudotojas)
            VALUES (:pavadinimas, :data, :aprasas, :busena, :naudotojasId, :administratoriusId)
        ");
        $stmt->execute([
            'pavadinimas' => $pavadinimas,
            'data' => $data,
            'aprasas' => $aprasas,
            'busena' => $busena,
            'naudotojasId' => $naudotojasId,
            'administratoriusId' => $administratoriusId
        ]);

        header("Location: naudotojoPagalbosUzklausos.php");
        exit();
    } catch (PDOException $e) {
        echo "Klaida: " . $e->getMessage();
    }
} else {
    header("Location: ../../Controllers/Naudotojo_sąsaja/sukurtiUzklausa.php");
    exit();
}
?>