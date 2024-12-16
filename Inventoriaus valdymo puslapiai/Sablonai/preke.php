<?php
// Include the database configuration
require_once 'config.php';

// Retrieve the product ID from the URL
if (isset($_GET['id_Preke'])) {
    $id_Preke = intval($_GET['id_Preke']);

    try {
        // Prepare the query to fetch the product details
        $stmt = $pdo->prepare("SELECT * FROM preke WHERE id_Preke = :id_Preke");
        $stmt->bindParam(':id_Preke', $id_Preke, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the product details
        if ($stmt->rowCount() > 0) {
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            die("Product not found.");
        }
    } catch (PDOException $e) {
        die("Error retrieving product: " . $e->getMessage());
    }
} else {
    die("No product ID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventoriaus valdymas - Produktų peržiūra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Inventoriaus valdymas</span>
        <a href="pagrindinis.php" class="btn btn-light mr-3">Pagrindinis</a>
        <div class="ml-auto d-flex align-items-center">
            <a href="krepselis.php" class="cart-icon mr-2">&#128722; Krepšelis</a>
            <!-- Profile Icon with Logout -->
            <a href="redaguoti_paskyra.html" class="profile-icon">&#128100;</a>
            <a href="atsijungti.html" class="btn btn-danger ml-2">Atsijungti</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <!-- Placeholder Image -->
                <img src="https://via.placeholder.com/500x600" alt="Product Image" class="product-image mb-3">
            </div>
            <div class="col-md-6">
                <h2>Produkto peržiūra</h2>
                <div id="productDetails">
                    <h4>Pavadinimas: <?php echo htmlspecialchars($product['pavadinimas']); ?></h4>
                    <p><strong>Kaina:</strong> €<?php echo number_format($product['kaina'], 2); ?></p>
                    <p><strong>Aprašymas:</strong> <?php echo htmlspecialchars($product['aprasymas']); ?></p>
                    <p><strong>Gamintojas:</strong> <?php echo htmlspecialchars($product['fk_Gamintojas_id_Gamintojas']); ?></p>
                    <p><strong>Ilgis:</strong> <?php echo htmlspecialchars($product['ilgis']); ?> m</p>
                    <p><strong>Dalių skaičius:</strong> <?php echo htmlspecialchars($product['daliu_sk']); ?></p>
                    <p><strong>Lankstumas:</strong> <?php echo htmlspecialchars($product['lankstumas']); ?>/10</p>
                    <p><strong>Medžiaga:</strong> <?php echo htmlspecialchars($product['medziaga']); ?></p>
                    <p><strong>Žiedelių skaičius:</strong> <?php echo htmlspecialchars($product['ziedeliu_sk']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
