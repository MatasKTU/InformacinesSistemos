<?php
require_once 'config.php';

function gautiPagalbosUzklausas($pdo) {
    $sql = "SELECT p.pavadinimas, p.data, p.aprasas, u.name AS busena, p.id_Pagalbos_uzklausa 
            FROM pagalbos_uzklausa p
            JOIN uzklausos_busena u ON p.busena = u.id_Uzklausos_busena";
    return $pdo->query($sql);
}
function gautiPagalbosUzklausaPagalId($pdo, $id) {
    $sql = "SELECT * FROM pagalbos_uzklausa WHERE id_Pagalbos_uzklausa = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function atnaujintiPagalbosUzklausa($pdo, $id, $pavadinimas, $aprasas, $busena) {
    $sql = "UPDATE pagalbos_uzklausa SET pavadinimas = ?, aprasas = ?, busena = ? WHERE id_Pagalbos_uzklausa = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$pavadinimas, $aprasas, $busena, $id]);
}
function gautiKlientoInformacija($pdo, $id) {
    $sql = "SELECT vardas, pavarde, el_pastas, telefono_nr FROM naudotojas WHERE id_Naudotojas = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
