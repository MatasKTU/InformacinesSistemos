<?php
// Include the database connection
include('config.php'); // Ensure config.php is included for database connection

// Start the session
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $cardName = $_POST['cardName'];
    $cardNumber = str_replace(' ', '', $_POST['cardNumber']); // Remove spaces
    $expiryDate = $_POST['expiryDate'];
    $amount = $_POST['amount'];

    // Split the card owner's name into first and last name
    $nameParts = explode(' ', $cardName);
    $firstName = $nameParts[0]; // First name
    $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Last name (if available)

    // Convert expiry date from MM/YY to YYYY-MM-DD format
    $expiryParts = explode('/', $expiryDate);
    $expiryMonth = str_pad($expiryParts[0], 2, '0', STR_PAD_LEFT); // Ensure two digits for month
    $expiryYear = '20' . $expiryParts[1]; // Add "20" prefix for year
    $formattedExpiryDate = "$expiryYear-$expiryMonth-01"; // Format as YYYY-MM-DD

    try {
        // Insert data into the database using PDO
        $sql = "INSERT INTO apmokejimas (korteles_savininko_vardas, korteles_savininko_pavarde, korteles_numeris, korteles_galiojimo_data, suma) 
                VALUES (:firstName, :lastName, :cardNumber, :expiryDate, :amount)";
        
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':cardNumber', $cardNumber);
        $stmt->bindParam(':expiryDate', $formattedExpiryDate);
        $stmt->bindParam(':amount', $amount);

        // Execute the query
        $stmt->execute();

        // Redirect to the main page after successful payment
        header('Location: pagrindinis.php');
        exit;

    } catch (PDOException $e) {
        // Handle the exception
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apmokėjimas</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar .payment-icon {
            font-size: 1.5em;
            cursor: pointer;
        }
        .payment-section {
            max-width: 500px;
            margin: 0 auto;
        }
        .submit-btn {
            background-color: green;
            color: white;
            border: none;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Apmokėjimas</span>
        <div class="ml-auto d-flex align-items-center">
            <!-- Profile Icon with Logout -->
            <a href="redaguoti_paskyra.html" class="profile-icon">&#128100;</a>
            <a href="atsijungti.html" class="btn btn-danger ml-2">Atsijungti</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="payment-section">
            <h2 class="text-center mb-4">Įveskite Kortelės Duomenis</h2>

            <!-- Payment Form -->
            <form method="POST" action="apmokejimas.php">
                <div class="form-group">
                    <label for="cardName">Kortelės Savininko Vardas</label>
                    <input type="text" class="form-control" id="cardName" name="cardName" placeholder="Įveskite vardą ant kortelės" required>
                </div>
                <div class="form-group">
                    <label for="cardNumber">Kortelės Numeris</label>
                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="1234 5678 9123 4567" maxlength="19" oninput="formatCardNumber(this)" required>
                </div>
                <div class="form-group">
                    <label for="expiryDate">Galiojimo Data</label>
                    <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/YY" maxlength="5" oninput="formatExpiryDate(this)" required>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="password" class="form-control" id="cvv" name="cvv" placeholder="***" maxlength="3" required>
                </div>
                <div class="form-group">
                    <label for="amount">Suma</label>
                    <input type="text" class="form-control" id="amount" name="amount" value="" readonly>
                </div>

                <!-- Submit Payment Button -->
                <button type="submit" class="btn submit-btn mt-4">Apmokėti</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript function to format the card number with spaces
        function formatCardNumber(input) {
            input.value = input.value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 ').trim();
        }

        // Corrected JavaScript function to format the expiry date as MM/YY
        function formatExpiryDate(input) {
            input.value = input.value.replace(/[^\d]/g, '').slice(0, 4); // Remove non-numeric chars
            if (input.value.length > 2) {
                input.value = input.value.slice(0, 2) + '/' + input.value.slice(2, 4); // Format as MM/YY
            }
        }

        // Update the amount field based on cart total
        document.addEventListener('DOMContentLoaded', () => {
            let cart = JSON.parse(sessionStorage.getItem('cart')) || [];
            let total = 0;

            // Calculate total price
            cart.forEach(item => {
                total += item.price * item.quantity;
            });

            // Set the amount field
            document.getElementById('amount').value = total.toFixed(2);
        });
    </script>
</body>
</html>
