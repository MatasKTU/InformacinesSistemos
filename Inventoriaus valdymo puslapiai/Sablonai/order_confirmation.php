<?php
// Connect to the database
include('config.php');

// Check if the required order data is passed
if (isset($_GET['order_date'], $_GET['total_price'], $_GET['weight'], $_GET['payment_id'], $_GET['delivery_method'])) {
    // Get form data from the GET request
    $order_date = $_GET['order_date'];
    $total_price = $_GET['total_price'];
    $address = $_GET['address']; // Address might not be passed in GET, handle accordingly
    $weight = $_GET['weight'];
    $payment_id = $_GET['payment_id'];
    $delivery_method = $_GET['delivery_method'];

    // Insert order details into the database
    $sql = "INSERT INTO uzsakymas (data, visa_kaina, adresas, svoris, fk_Apmokejimas_id_Apmokejimas, fk_Pristatymo_budas_id_Pristatymo_budas) 
            VALUES ('$order_date', '$total_price', '$address', '$weight', '$payment_id', '$delivery_method')";

    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn); // Get the last inserted order ID

        // Insert ordered products into prekes_kiekis table
        foreach ($_POST['products'] as $product_id => $quantity) {
            $insert_product = "INSERT INTO prekes_kiekis (kiekis, fk_Uzsakymas_id_Uzsakymas, fk_Preke_id_Preke) 
                               VALUES ($quantity, $order_id, $product_id)";
            mysqli_query($conn, $insert_product);
        }

        echo "Order placed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
