<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    // Join tiekejas and naudotojas tables to get all necessary information
    $sql = "SELECT 
            t.pavadinimas,
            t.adresas,
            n.vardas,
            n.pavarde,
            n.el_pastas,
            n.telefono_nr
        FROM tiekejas t
        JOIN naudotojas n ON t.id_Naudotojas = n.id_Naudotojas";
        
    $stmt = $pdo->query($sql);
    $tiekejai = $stmt->fetchAll();
    echo json_encode($tiekejai);
} catch (\PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
