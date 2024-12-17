<?php
header('Content-Type: application/json');
require_once '../config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get all products with quantity below 50 and their suppliers' info
        $sql = "SELECT 
                p.id_Preke,
                p.pavadinimas as preke_pavadinimas,
                p.kiekis,
                t.pavadinimas as tiekejas_pavadinimas,
                n.el_pastas,
                n.vardas,
                n.pavarde
            FROM preke p
            JOIN tiekejas t ON p.fk_Tiekejas_id_Naudotojas = t.id_Naudotojas
            JOIN naudotojas n ON t.id_Naudotojas = n.id_Naudotojas
            WHERE p.kiekis < 50
            ORDER BY t.id_Naudotojas, p.pavadinimas";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group products by supplier
        $supplierProducts = [];
        foreach ($results as $row) {
            $supplierId = $row['el_pastas'];
            if (!isset($supplierProducts[$supplierId])) {
                $supplierProducts[$supplierId] = [
                    'tiekejas' => $row['tiekejas_pavadinimas'],
                    'vardas' => $row['vardas'],
                    'pavarde' => $row['pavarde'],
                    'el_pastas' => $row['el_pastas'],
                    'products' => []
                ];
            }
            $supplierProducts[$supplierId]['products'][] = [
                'id' => $row['id_Preke'],
                'pavadinimas' => $row['preke_pavadinimas'],
                'kiekis' => $row['kiekis']
            ];
        }

        // Configure PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'sarunasgerve83@gmail.com'; // Your Gmail address
        $mail->Password = 'slaptazodis123'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Send emails to each supplier
        foreach ($supplierProducts as $supplier) {
            try {
                $mail->clearAddresses(); // Clear previous recipients
                $mail->setFrom('sarunasgerve83@gmail.com', 'Žvejybos reikmenų parduotuvė');
                $mail->addAddress($supplier['el_pastas']);
                $mail->Subject = "Prekių papildymo užklausa";
                
                // Create email body
                $message = "Gerb. " . $supplier['vardas'] . " " . $supplier['pavarde'] . ",\n\n";
                $message .= "Prašome papildyti šių prekių kiekius:\n\n";
                
                foreach ($supplier['products'] as $product) {
                    $message .= "- " . $product['pavadinimas'] . " (Likutis: " . $product['kiekis'] . " vnt.)\n";
                }
                
                $message .= "\nPrašome kuo skubiau papildyti prekių kiekius.\n\n";
                $message .= "Pagarbiai,\nŽvejybos reikmenų parduotuvė";
                
                $mail->Body = $message;

                if ($mail->send()) {
                    $emailResults[] = [
                        'tiekejas' => $supplier['tiekejas'],
                        'success' => true
                    ];
                }
            } catch (Exception $e) {
                $emailResults[] = [
                    'tiekejas' => $supplier['tiekejas'],
                    'success' => false,
                    'error' => $mail->ErrorInfo
                ];
            }
        }

        // Return results
        echo json_encode([
            'success' => true,
            'message' => 'Išsiųsta ' . count($supplierProducts) . ' pranešimai tiekėjams',
            'details' => $emailResults
        ]);

    } catch (\PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => 'Įvyko klaida siunčiant pranešimus'
        ]);
    }
}
?>
