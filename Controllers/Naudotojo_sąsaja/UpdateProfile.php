<?php
session_start(); // Įjungia sesijas
require_once 'config.php';

//Patikriname, ar naudotojas yra prisijungęs
if (!isset($_SESSION['id_Naudotojas'])) {
    die("Naudotojas neprisijungęs. Prašome prisijungti.");
}

// Gauname prisijungusio naudotojo ID
$id_Naudotojas = $_SESSION['id_Naudotojas'];

// Tikriname, ar forma buvo pateikta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gauname formos duomenis
    $vardas = $_POST['vardas'];
    $pavarde = $_POST['pavarde'];
    $el_pastas = $_POST['el_pastas'];
    $slaptazodis = $_POST['slaptazodis'];
    $telefono_nr = $_POST['telefono_nr'];

    // Tikriname, ar visi laukai užpildyti
    if (!empty($vardas) && !empty($pavarde) && !empty($el_pastas) && !empty($slaptazodis) && !empty($telefono_nr)) {
        try {
            // Paruošiame SQL užklausą
            $sql = "UPDATE naudotojas 
                    SET vardas = :vardas, pavarde = :pavarde, el_pastas = :el_pastas, 
                        slaptazodis = :slaptazodis, telefono_nr = :telefono_nr 
                    WHERE id_Naudotojas = :id_Naudotojas";

            // Paruošiame užklausą
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':vardas', $vardas);
            $stmt->bindParam(':pavarde', $pavarde);
            $stmt->bindParam(':el_pastas', $el_pastas);
            $stmt->bindParam(':slaptazodis', $slaptazodis);
            $stmt->bindParam(':telefono_nr', $telefono_nr);
            $stmt->bindParam(':id_Naudotojas', $id_Naudotojas);

            // Vykdome užklausą
            if ($stmt->execute()) {
                // Po sėkmingo atnaujinimo, išjungiame sesiją ir nukreipiame į prisijungimo puslapį
                session_destroy(); // Uždaryti sesiją
                header("Location: ../../Sablonai/prisijungti.html?success=profile_updated");
                exit;
            } else {
                echo "Nepavyko atnaujinti profilio. Bandykite dar kartą.";
            }
        } catch (PDOException $e) {
            echo "Klaida: " . $e->getMessage();
        }
    } 
} else {
    echo "Neteisingas užklausos metodas.";
}
?>
