<?php
include('config.php'); // Include the database connection file

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from 'preke' table
$sql = "SELECT id_Preke, pavadinimas, kaina, aprasymas, svoris FROM preke";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prekių asortimentas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar .profile-icon {
            font-size: 1.5em;
            cursor: pointer;
        }
        .btn-logout {
            background-color: red;
            color: white;
            border: none;
        }
        .btn-logout:hover {
            background-color: darkred;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .product-name {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Žvejybos prekės</span>
        <a href="pagrindinis.php" class="btn btn-light mr-3">Pagrindinis</a>
        <div class="ml-auto d-flex align-items-center">
            <a href="krepselis.php" class="cart-icon mr-2">&#128722; Krepšelis</a>
            <!-- Profile Icon with Logout -->
            <a href="redaguoti_paskyra.html" class="profile-icon">&#128100;</a>
            <a href="atsijungti.html" class="btn btn-danger ml-2">Atsijungti</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Prekių asortimentas</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="product-card">
                            <div class="product-name"><?= htmlspecialchars($row["pavadinimas"]) ?></div>
                            <p><strong>Kaina:</strong> €<?= number_format($row["kaina"], 2) ?></p>
                            <p><strong>Aprašymas:</strong> <?= htmlspecialchars($row["aprasymas"]) ?></p>
                            <button class="btn btn-primary btn-block mt-2 add-to-cart"
                                    data-id="<?= htmlspecialchars($row["id_Preke"]) ?>"
                                    data-name="<?= htmlspecialchars($row["pavadinimas"]) ?>"
                                    data-price="<?= number_format($row["kaina"], 2) ?>"
                                    data-weight="<?= $row["svoris"] ?>"
                                    >Pridėti į krepšelį</button>
                                    <a href="preke.php?id_Preke=<?= urlencode($row["id_Preke"]) ?>" class="btn btn-secondary btn-block mt-2">Peržiūrėti</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Nėra prekių.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                // Getting product data (from data attributes)
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = parseFloat(this.getAttribute('data-price'));
                const productWeight = parseFloat(this.getAttribute('data-weight'));

                // Retrieve cart from sessionStorage, or create a new one if empty
                let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

                // Check if product already exists in the cart
                const existingProductIndex = cart.findIndex(item => item.id === productId);
                if (existingProductIndex !== -1) {
                    // If product already in cart, update quantity
                    cart[existingProductIndex].quantity += 1;
                } else {
                    // If new product, add to cart
                    cart.push({ id: productId, name: productName, price: productPrice, weight: productWeight, quantity: 1 });
                }

                // Save updated cart back to sessionStorage
                sessionStorage.setItem('cart', JSON.stringify(cart));

                // Debugging: Log cart content
                console.log('Cart:', JSON.parse(sessionStorage.getItem('cart')));
            });
        });
    </script>

</body>
</html>

<?php
$conn->close();
?>
