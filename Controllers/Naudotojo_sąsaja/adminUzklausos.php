<?php
require_once 'AdminHelp.php';

// Gauname pagalbos užklausas
$stmt = gautiPagalbosUzklausas($pdo);

// Įtraukiame HTML failą
include '../../Sablonai/Admin_uzklausos.html';
?>