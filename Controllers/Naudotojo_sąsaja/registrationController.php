<?php
session_start();
require_once 'config.php';

// Pagrindinė klaidos apdorojimo funkcija
function log_error($message) {
    file_put_contents('error_log.txt', $message . PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vardas = $_POST['vardas'] ?? '';
    $pavarde = $_POST['pavarde'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $telefonas = $_POST['telefonas'] ?? '';

    // Patikriname, ar visi laukai užpildyti
    if (empty($vardas) || empty($pavarde) || empty($email) || empty($password) || empty($telefonas)) {
        header("Location: ../../Sablonai/registracija.html?error=empty_fields");
        exit;
    }

    // Patikriname el. pašto formatą
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../Sablonai/registracija.html?error=invalid_email");
        exit;
    }

    // Patikriname telefono numerio formatą (priklauso nuo šalies)
    if (!preg_match("/^\+?(\d{1,3})?[\s.-]?\(?(\d{1,4})\)?[\s.-]?(\d{1,4})[\s.-]?(\d{1,4})$/", $telefonas)) {
        header("Location: ../../Sablonai/registracija.html?error=invalid_phone");
        exit;
    }

    try {
        // Patikriname, ar el. paštas jau egzistuoja
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Naudotojas WHERE el_pastas = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: ../../Sablonai/registracija.html?error=email_taken");
            exit;
        }

        // Įrašome naują naudotoją į 'Naudotojas' lentelę
        $stmt = $pdo->prepare("INSERT INTO Naudotojas (vardas, pavarde, el_pastas, slaptazodis, telefono_nr) 
                               VALUES (:vardas, :pavarde, :email, :password, :telefonas)");

        $stmt->execute([
            'vardas' => $vardas,
            'pavarde' => $pavarde,
            'email' => $email,
            'password' => $password, // Nenaudojame slaptažodžio šifravimo
            'telefonas' => $telefonas
        ]);

        // Įrašome naują įrašą į 'Klientas' lentelę, priskirdami rolę 'Klientas'
        $userId = $pdo->lastInsertId(); // Gauti paskutinį įrašytą ID (naudotojo ID)
        $stmt = $pdo->prepare("INSERT INTO Klientas (adresas, id_Naudotojas) VALUES (:adresas, :userId)");
        $stmt->execute(['adresas' => '', 'userId' => $userId]);

        // Sėkmingai užregistruotas, nukreipiame į prisijungimo puslapį
        header("Location: ../../Sablonai/prisijungti.html?success=registration_successful");
        exit;
    } catch (PDOException $e) {
        // Loguojame klaidą
        log_error("SQL klaida: " . $e->getMessage());
        header("Location: ../../Sablonai/registracija.html?error=db_error");
        exit;
    }
} else {
    // Jei nėra POST užklausos
    header("Location: ../../Sablonai/registracija.html");
    exit;
}
?>
