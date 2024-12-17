<?php
require_once 'editRoleController.php';

// Gauname visus naudotojus su jų rolemis
$naudotojai = gautiNaudotojusSuRolemis($pdo);

// Gauname visas roles
$roles = gautiRoles();

// Atnaujiname naudotojo rolę, jei forma pateikta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naudotojasId = $_POST['naudotojas_id'];
    $naujaRole = $_POST['role'];

    if (atnaujintiNaudotojoRole($pdo, $naudotojasId, $naujaRole)) {
        header("Location: roleEdit.php");
        exit();
    } else {
        echo "Klaida atnaujinant naudotojo rolę.";
    }
}

// Įtraukiame HTML failą
include '../../Sablonai/Redaguoti_role.html';
?>