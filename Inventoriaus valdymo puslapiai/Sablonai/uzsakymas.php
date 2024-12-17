<?php
session_start();
include('config.php'); // Include the database connection file

// Initialize cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$total = 0; // Total price calculation
$totalQuantity = 0; // Total quantity calculation

// Calculate the total price and quantity
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
    $totalQuantity += $item['quantity'];
}

$errorMessages = []; // Array to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postalCode = $_POST['postalCode'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $total = $_POST['price'];
    $weight = $_POST['weight'];
    $items = $_POST['items'];

    // Split full name into 'vardas' and 'pavarde'
    $nameParts = explode(' ', $fullName, 2); // Split by the first space
    $vardas = $nameParts[0];
    $pavarde = isset($nameParts[1]) ? $nameParts[1] : ''; // If there's no space, set empty string for 'pavarde'

    try {
        // Insert user data into 'naudotojas' table
        $stmt = $pdo->prepare("INSERT INTO naudotojas (vardas, pavarde, el_pastas, telefono_nr) VALUES (?, ?, ?, ?)");
        $stmt->execute([$vardas, $pavarde, $email, $phone]);
        $userId = $pdo->lastInsertId(); // Get the last inserted ID from 'naudotojas'
        
        // Check if userId is valid
        if (!$userId) {
            $errorMessages[] = "Nepavyko įrašyti naudotojo duomenų į 'naudotojas' lentelę.";
        }

        // Insert client data into 'klientas' table (fixed the foreign key reference)
        $stmt = $pdo->prepare("INSERT INTO klientas (adresas, id_Naudotojas) VALUES (?, ?)");
        $stmt->execute([$address, $userId]);  // Corrected the foreign key column name
        $clientId = $pdo->lastInsertId(); // Get the last inserted ID from 'klientas'

        // Check if clientId is valid
        if (!$clientId) {
            $errorMessages[] = "Nepavyko įrašyti kliento duomenų į 'klientas' lentelę.";
        }

        // Insert order data into 'uzsakymas' table
        $stmt = $pdo->prepare("INSERT INTO uzsakymas (data, visa_kaina, adresas, svoris, busena, pristatymo_budas, fk_Klientas_id_Naudotojas, fk_Apmokejimas_id_Apmokejimas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $orderDate = date('Y-m-d'); // Current date
        $shippingMethod = 1; // Set default shipping method, you can adjust this as needed
        $status = 2; // Set default status, adjust as needed
        $paymentMethod = 1; // Set a default payment method, adjust as needed
        
        $stmt->execute([$orderDate, $total, $address, $weight, $status, $shippingMethod, $clientId, $paymentMethod]);
        $stmt = $pdo->query("SELECT LAST_INSERT_ID()");
        $orderId = $stmt->fetchColumn();
        
        // Insert client data into 'prekes_kiekis' table 
        $stmt = $pdo->prepare("INSERT INTO prekes_kiekis (kiekis, fk_Uzsakymas_id_Uzsakymas, fk_Preke_id_Preke) VALUES (?, ?, ?)");
        $items = explode(';', $items);

        foreach($items as $item){
            if($item === "") {
                continue;
            }
            $itemdetail = explode(':', $item);
            $stmt->execute([$itemdetail[1], $orderId, $itemdetail[0]]);
        }

        // Redirect to payment page after successful order
        if (empty($errorMessages)) {
            header('Location: apmokejimas.php');
            exit;
        }

    } catch (PDOException $e) {
        $errorMessages[] = "Duomenų bazės klaida: " . $e->getMessage(); // Show database errors
    } catch (Exception $e) {
        $errorMessages[] = "Klaida: " . $e->getMessage(); // Show other errors
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Užsakymo Patvirtinimas</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar .order-icon {
            font-size: 1.5em;
            cursor: pointer;
        }
        .order-item img {
            max-width: 80px;
            height: auto;
        }
        .order-total {
            font-size: 1.2em;
            font-weight: bold;
        }
        .submit-btn {
            background-color: green;
            color: white;
            border: none;
        }
        .submit-btn:hover {
            background-color: darkgreen;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
        .total-info {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 10px;
        }
        .cart-items {
            margin-top: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Užsakymo Patvirtinimas</span>
        <a href="pagrindinis.php" class="btn btn-light mr-3">Pagrindinis</a>
        <div class="ml-auto d-flex align-items-center">
            <!-- Profile Icon with Logout -->
            <a href="redaguoti_paskyra.html" class="profile-icon">&#128100;</a>
            <a href="atsijungti.html" class="btn btn-danger ml-2">Atsijungti</a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Display Cart Items and Total Price -->
        <div class="total-info">
            <span>Bendra suma: €<span id="total-price">0.00</span></span> | 
            <span>Viso prekių: <span id="total-quantity">0</span> vnt.</span>
        </div>

        <div id="cart-items" class="cart-items">
            <!-- Cart items will be displayed here by JS -->
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errorMessages)): ?>
            <div class="error-message">
                <ul>
                    <?php foreach ($errorMessages as $message): ?>
                        <li><?php echo htmlspecialchars($message); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Delivery Information Form -->
        <h2 class="mt-4">Pristatymo Informacija</h2>
        <form method="POST" action="uzsakymas.php">
            <div class="form-group">
                <label for="fullName">Pilnas vardas</label>
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Įveskite savo vardą" required>
            </div>
            <div class="form-group">
                <label for="address">Adresas</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Įveskite savo adresą" required>
            </div>
            <div class="form-group">
                <label for="city">Miestas</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Įveskite miestą" required>
            </div>
            <div class="form-group">
                <label for="postalCode">Pašto kodas</label>
                <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Įveskite pašto kodą" required>
            </div>
            <div class="form-group">
                <label for="phone">Telefonas</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Įveskite telefono numerį" required>
            </div>
            <div class="form-group">
                <label for="email">El. paštas</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Įveskite el. paštą" required>
            </div>
            <input type="text" id="weight" name="weight" hidden>
            <input type="text" id="price" name="price" hidden>
            <input type="text" id="items" name="items" hidden>

            <!-- Submit Order Button -->
            <button type="submit" class="btn submit-btn mt-4">Patvirtinti Užsakymą</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="http://localhost/zvejybos_prekiu_parduotuve/js/cart.js"></script>

</body>
</html>
