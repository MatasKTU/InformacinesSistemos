<?php
require_once '../../Controllers/Naudotojo_sąsaja/AdminHelp.php';

// Patikriname, ar ID yra perduotas
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $uzklausa = gautiPagalbosUzklausaPagalId($pdo, $id);

    if (!$uzklausa) {
        die("Užklausa nerasta.");
    }
} else {
    die("Nenurodytas užklausos ID.");
}

// Atnaujiname duomenis, jei forma pateikta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pavadinimas = $_POST['pavadinimas'];
    $aprasas = $_POST['aprasas'];
    $busena = $_POST['busena'];

    if (atnaujintiPagalbosUzklausa($pdo, $id, $pavadinimas, $aprasas, $busena)) {
        header("Location: adminUzklausos.php");
        exit();
    } else {
        echo "Klaida atnaujinant užklausą.";
    }
}

// Įtraukiame HTML failą
include '../../Sablonai/Admin_Redaguoti_uzklausa.html';
?>