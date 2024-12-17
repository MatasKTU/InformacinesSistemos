<?php
// prekeController.php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'categories':
                try {
                    // Hardcoded categories
                    $categories = ['Meškerės', 'Ritės'];
                    echo json_encode($categories);
                    exit();
                } catch (\PDOException $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                    exit();
                }
                break;
            case 'manufacturers':
                try {
                    // Debug the query
                    $sql = 'SELECT id_Gamintojas, pavadinimas FROM gamintojas';
                    error_log("SQL Query: " . $sql);
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Debug the results
                    error_log("Manufacturers found: " . print_r($manufacturers, true));
                    
                    echo json_encode($manufacturers);
                    exit();
                } catch (\PDOException $e) {
                    error_log("Database Error: " . $e->getMessage());
                    echo json_encode(['error' => $e->getMessage()]);
                    exit();
                }
                break;
            case 'lankstumas':
                try {
                    $sql = 'SELECT id_Prekes_lankstumas, name FROM prekes_lankstumas';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $lankstumas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($lankstumas);
                    exit();
                } catch (\PDOException $e) {
                    error_log("Database Error: " . $e->getMessage());
                    echo json_encode(['error' => $e->getMessage()]);
                    exit();
                }
                break;
            case 'suppliers':
                try {
                    $stmt = $pdo->query('SELECT t.id_Naudotojas, n.vardas, n.pavarde, t.pavadinimas 
                                        FROM tiekejas t 
                                        JOIN naudotojas n ON t.id_Naudotojas = n.id_Naudotojas');
                    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($suppliers);
                    exit();
                } catch (\PDOException $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                    exit();
                }
                break;
        }
    } elseif (isset($_GET['id'])) {
        // Get specific product data
        try {
            $stmt = $pdo->prepare('
                SELECT p.*, pav.pavadinimas as paveikslas, k.pavadinimas as kategorija
                FROM preke p 
                LEFT JOIN paveikslas pav ON p.id_Preke = pav.fk_Preke_id_Preke 
                LEFT JOIN kategorija k ON p.id_Preke = k.fk_Preke_id_Preke
                WHERE p.id_Preke = ?
            ');
            $stmt->execute([$_GET['id']]);
            $preke = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($preke);
            exit();
        } catch (\PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    } else {
        // Get all products list
        try {
            $stmt = $pdo->query('SELECT id_Preke, pavadinimas, kiekis FROM preke');
            $prekes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($prekes);
            exit();
        } catch (\PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        try {
            $pdo->beginTransaction();
            
            // Get supplier ID either from form (if admin) or session (if supplier)
            $tiekejasId = null;
            session_start();
            
            if ($_SESSION['role'] === 'admin') {
                // Admin is creating the product, use selected supplier
                $tiekejasId = $_POST['fk_Tiekejas_id_Naudotojas'];
            } else {
                // Supplier is creating the product, use their own ID
                $tiekejasId = $_SESSION['user_id'];
            }
            
            if (!$tiekejasId) {
                throw new \Exception('No valid tiekejas ID found');
            }
            
            // Insert into preke table
            $sql = "INSERT INTO preke (
                pavadinimas, 
                kaina, 
                aprasymas, 
                ilgis, 
                daliu_sk, 
                medziaga, 
                ziedeliu_sk, 
                dydis, 
                guoliai, 
                perdavimas, 
                bugnelio_talpa, 
                svoris, 
                lankstumas, 
                kiekis, 
                fk_Gamintojas_id_Gamintojas,
                fk_Tiekejas_id_Naudotojas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['pavadinimas'],
                $_POST['kaina'],
                $_POST['aprasymas'],
                !empty($_POST['ilgis']) ? $_POST['ilgis'] : null,
                !empty($_POST['daliu_sk']) ? $_POST['daliu_sk'] : null,
                !empty($_POST['medziaga']) ? $_POST['medziaga'] : null,
                !empty($_POST['ziedeliu_sk']) ? $_POST['ziedeliu_sk'] : null,
                !empty($_POST['dydis']) ? $_POST['dydis'] : null,
                !empty($_POST['guoliai']) ? $_POST['guoliai'] : null,
                !empty($_POST['perdavimas']) ? $_POST['perdavimas'] : null,
                !empty($_POST['bugnelio_talpa']) ? $_POST['bugnelio_talpa'] : null,
                !empty($_POST['svoris']) ? $_POST['svoris'] : null,
                !empty($_POST['lankstumas']) ? $_POST['lankstumas'] : null,
                $_POST['kiekis'],
                $_POST['fk_Gamintojas_id_Gamintojas'],
                $tiekejasId
            ]);

            $prekeId = $pdo->lastInsertId();

            // Insert into kategorija table
            $stmt = $pdo->prepare("INSERT INTO kategorija (pavadinimas, fk_Preke_id_Preke) VALUES (?, ?)");
            $stmt->execute([$_POST['kategorija'], $prekeId]);

            // Insert into paveikslas table if URL provided
            if (!empty($_POST['paveikslas'])) {
                $stmt = $pdo->prepare("INSERT INTO paveikslas (pavadinimas, fk_Preke_id_Preke) VALUES (?, ?)");
                $stmt->execute([$_POST['paveikslas'], $prekeId]);
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
            exit();
            
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Error: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        try {
            $pdo->beginTransaction();
            
            session_start();
            
            // Validate required fields
            $requiredFields = ['id_Preke', 'pavadinimas', 'kaina', 'kiekis', 'fk_Gamintojas_id_Gamintojas'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || $_POST[$field] === '') {
                    throw new \Exception("Missing required field: $field");
                }
            }

            // Validate numeric fields
            $numericFields = ['kaina', 'kiekis', 'fk_Gamintojas_id_Gamintojas'];
            foreach ($numericFields as $field) {
                if (isset($_POST[$field]) && !is_numeric($_POST[$field])) {
                    throw new \Exception("Field $field must be numeric");
                }
            }

            // Add error checking for session
            if (!isset($_SESSION['role']) || !isset($_SESSION['id_Naudotojas'])) {
                throw new \Exception('Session expired or invalid');
            }

            // Determine the supplier ID based on user role
            if ($_SESSION['role'] === 'admin') {
                // Admin must provide a supplier ID
                if (!isset($_POST['fk_Tiekejas_id_Naudotojas'])) {
                    throw new \Exception('Supplier ID is required for admin');
                }
                $tiekejasId = $_POST['fk_Tiekejas_id_Naudotojas'];
            } else {
                // For tiekejas user, use their session ID
                $tiekejasId = $_SESSION['id_Naudotojas'];
            }

            // Verify the manufacturer exists
            $stmt = $pdo->prepare("SELECT id_Gamintojas FROM gamintojas WHERE id_Gamintojas = ?");
            $stmt->execute([$_POST['fk_Gamintojas_id_Gamintojas']]);
            if (!$stmt->fetch()) {
                throw new \Exception('Invalid manufacturer ID');
            }

            $sql = "UPDATE preke SET 
                    pavadinimas = ?,
                    kaina = ?,
                    aprasymas = ?,
                    ilgis = ?,
                    daliu_sk = ?,
                    medziaga = ?,
                    ziedeliu_sk = ?,
                    dydis = ?,
                    guoliai = ?,
                    perdavimas = ?,
                    bugnelio_talpa = ?,
                    svoris = ?,
                    lankstumas = ?,
                    kiekis = ?,
                    fk_Gamintojas_id_Gamintojas = ?,
                    fk_Tiekejas_id_Naudotojas = ?
                    WHERE id_Preke = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $_POST['pavadinimas'],
                $_POST['kaina'],
                $_POST['aprasymas'],
                !empty($_POST['ilgis']) ? $_POST['ilgis'] : null,
                !empty($_POST['daliu_sk']) ? $_POST['daliu_sk'] : null,
                !empty($_POST['medziaga']) ? $_POST['medziaga'] : null,
                !empty($_POST['ziedeliu_sk']) ? $_POST['ziedeliu_sk'] : null,
                !empty($_POST['dydis']) ? $_POST['dydis'] : null,
                !empty($_POST['guoliai']) ? $_POST['guoliai'] : null,
                !empty($_POST['perdavimas']) ? $_POST['perdavimas'] : null,
                !empty($_POST['bugnelio_talpa']) ? $_POST['bugnelio_talpa'] : null,
                !empty($_POST['svoris']) ? $_POST['svoris'] : null,
                !empty($_POST['lankstumas']) ? $_POST['lankstumas'] : null,
                $_POST['kiekis'],
                $_POST['fk_Gamintojas_id_Gamintojas'],
                $tiekejasId,  // Using the determined supplier ID
                $_POST['id_Preke']
            ]);

            if (!$result) {
                throw new \Exception('Failed to update product');
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
            exit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        } catch (\Error $e) {
            $pdo->rollBack();
            error_log("Critical error updating product: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            exit();
        }
    }
    // Update product data
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        unset($data['id']); // Remove ID from data array
        
        // Build update query dynamically based on provided fields
        $updateFields = [];
        $params = [];
        foreach ($data as $key => $value) {
            if ($key !== 'paveikslas') { // Handle paveikslas separately
                $updateFields[] = "`$key` = ?";
                $params[] = $value === '' ? null : $value;
            }
        }
        $params[] = $id; // Add ID for WHERE clause
        
        $sql = "UPDATE preke SET " . implode(', ', $updateFields) . " WHERE id_Preke = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Update paveikslas if provided
        if (isset($data['paveikslas'])) {
            $stmt = $pdo->prepare('UPDATE paveikslas SET pavadinimas = ? WHERE fk_Preke_id_Preke = ?');
            $stmt->execute([$data['paveikslas'], $id]);
        }
        
        echo json_encode(['success' => true]);
    } catch (\PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];

        // Start transaction
        $pdo->beginTransaction();

        // Delete from paveikslas table first (due to foreign key constraint)
        $stmt = $pdo->prepare('DELETE FROM paveikslas WHERE fk_Preke_id_Preke = ?');
        $stmt->execute([$id]);

        // Delete from kategorija table
        $stmt = $pdo->prepare('DELETE FROM kategorija WHERE fk_Preke_id_Preke = ?');
        $stmt->execute([$id]);

        // Delete from preke table
        $stmt = $pdo->prepare('DELETE FROM preke WHERE id_Preke = ?');
        $stmt->execute([$id]);

        // Commit transaction
        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (\PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
    }
}
