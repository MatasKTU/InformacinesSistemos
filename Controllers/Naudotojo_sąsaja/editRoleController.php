<?php
require_once 'config.php';

function gautiNaudotojusSuRolemis($pdo) {
    $sql = "
        SELECT n.id_Naudotojas, n.vardas, n.pavarde, n.el_pastas,
            CASE
                WHEN a.id_Naudotojas IS NOT NULL THEN 'Administratorius'
                WHEN t.id_Naudotojas IS NOT NULL THEN 'Tiekejas'
                WHEN k.id_Naudotojas IS NOT NULL THEN 'Klientas'
                ELSE 'Nežinoma'
            END AS role
        FROM naudotojas n
        LEFT JOIN administratorius a ON n.id_Naudotojas = a.id_Naudotojas
        LEFT JOIN tiekejas t ON n.id_Naudotojas = t.id_Naudotojas
        LEFT JOIN klientas k ON n.id_Naudotojas = k.id_Naudotojas";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function atnaujintiNaudotojoRole($pdo, $naudotojasId, $naujaRole) {
    // Pirmiausia pašaliname naudotoją iš visų rolių lentelių
    try {
        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM administratorius WHERE id_Naudotojas = ?")->execute([$naudotojasId]);
        $pdo->prepare("DELETE FROM tiekejas WHERE id_Naudotojas = ?")->execute([$naudotojasId]);
        $pdo->prepare("DELETE FROM klientas WHERE id_Naudotojas = ?")->execute([$naudotojasId]);

        // Priskiriame naują rolę
        switch ($naujaRole) {
            case 'Administratorius':
                $sql = "INSERT INTO administratorius (id_Naudotojas) VALUES (?)";
                break;
            case 'Tiekejas':
                $sql = "INSERT INTO tiekejas (id_Naudotojas) VALUES (?)";
                break;
            case 'Klientas':
                $sql = "INSERT INTO klientas (id_Naudotojas) VALUES (?)";
                break;
            default:
                $pdo->rollBack();
                return false;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$naudotojasId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function gautiRoles() {
    return ['Administratorius', 'Tiekejas', 'Klientas'];
}
?>