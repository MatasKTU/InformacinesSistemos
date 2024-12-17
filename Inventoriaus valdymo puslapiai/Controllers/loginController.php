<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Tikriname, ar užpildyti visi privalomi laukai
    if (empty($email) || empty($password)) {
        header("Location: ../Sablonai/prisijungti.html?error=empty_fields");
        exit;
    }

    try {
        // Užklausa naudotojo duomenims gauti
        $stmt = $pdo->prepare("
            SELECT n.id_Naudotojas, n.vardas, n.pavarde, n.slaptazodis, n.el_pastas, n.telefono_nr,
                   CASE 
                       WHEN a.id_Naudotojas IS NOT NULL THEN 'admin'
                       WHEN t.id_Naudotojas IS NOT NULL THEN 'tiekejas'
                       WHEN k.id_Naudotojas IS NOT NULL THEN 'klientas'
                       ELSE NULL
                   END AS role
            FROM Naudotojas n
            LEFT JOIN Administratorius a ON n.id_Naudotojas = a.id_Naudotojas
            LEFT JOIN Tiekejas t ON n.id_Naudotojas = t.id_Naudotojas
            LEFT JOIN Klientas k ON n.id_Naudotojas = k.id_Naudotojas
            WHERE n.el_pastas = :email
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Slaptažodžio palyginimas (tiesioginis)
            if ($password === $user['slaptazodis']) {
                // Įrašome naudotojo informaciją į sesiją
                $_SESSION['id_Naudotojas'] = $user['id_Naudotojas'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['vardas'];
                $_SESSION['email'] = $user['el_pastas'];
                $_SESSION['telefono_nr'] = $user['telefono_nr'];

                // Get the tiekejas (supplier) ID after successful login
                $stmt = $pdo->prepare("SELECT id_Naudotojas FROM tiekejas WHERE id_Naudotojas = ?");
                $stmt->execute([$user['id_Naudotojas']]);
                $tiekejasId = $stmt->fetchColumn();

                if ($tiekejasId) {
                    $_SESSION['user_id'] = $tiekejasId;
                }

                // Pagal rolę nukreipiame į atitinkamą puslapį
                if ($user['role'] === 'admin') {
                    header("Location: ../Sablonai/administratoriaus_pagrindinis.html");
                } elseif ($user['role'] === 'tiekejas') {
                    header("Location: ../Sablonai/inv_pgr.html");
                } elseif ($user['role'] === 'klientas') {
                    header("Location: ../Sablonai/pagrindinis.php");
                } else {
                    header("Location: ../Sablonai/prisijungti.html?error=unknown_role");
                }
                exit;
            } else {
                header("Location: ../Sablonai/prisijungti.html?error=invalid_credentials");
                exit;
            }
        } else {
            header("Location: ../Sablonai/prisijungti.html?error=invalid_credentials");
            exit;
        }
    } catch (PDOException $e) {
        // Klaida su duomenų baze
        header("Location: ../Sablonai/prisijungti.html?error=db_error");
        exit;
    }
} else {
    // Jei užklausos metodas netinkamas
    header("Location: ../Sablonai/prisijungti.html");
    exit;
}
?>
