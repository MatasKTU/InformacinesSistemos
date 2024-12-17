<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pardavimų ataskaita</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar { margin-bottom: 30px; }
        .content-container { margin-top: 30px; }
        .form-group label { font-weight: bold; }
        .centered-button {
            display: block;
            margin: 20px auto;
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Pardavimų ataskaita</span>
        <div class="ml-auto d-flex align-items-center">
            <a href="analitika_main.html" class="btn btn-light ml-2">Atgal</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content-container">
        <h1 class="text-center">Pardavimų ataskaita</h1>
        <form method="GET" action="">
            <!-- Laikotarpio pasirinkimas -->
            <div class="form-group">
                <label for="startDate">Užsakymo pradžios data</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">Užsakymo pabaigos data</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>

            <!-- Produkto kategorijos pasirinkimas 
            <div class="form-group">
                <label for="productCategory">Produkto kategorija</label>
                <select class="form-control" id="productCategory" name="productCategory">
                    <option value="">Visos kategorijos</option>
                    <option value="rite">Ritės</option>
                    <option value="meskere">Meškerės</option>
                </select>
            </div> -->

            <!-- Prekės lankstumas -->
            <div class="form-group">
                <label for="flexibility">Prekės lankstumas</label>
                <select class="form-control" id="flexibility" name="flexibility">
                    <option value="">Visi</option>
                    <option value="1">Didelis</option>
                    <option value="2">Vidutiniškas</option>
                    <option value="3">Mažas</option>
                </select>
            </div>

            <!-- Užsakymo būsena -->
            <div class="form-group">
                <label for="orderStatus">Užsakymo būsena</label>
                <select class="form-control" id="orderStatus" name="orderStatus">
                    <option value="">Visos būsenos</option>
                    <option value="1">Pristatytas</option>
                    <option value="2">Ruošiamas</option>
                    <option value="3">Išsiųstas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-block">Generuoti ataskaitą</button>
        </form>
    </div>

    <?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $productCategory = $_GET['productCategory'] ?? '';
    $flexibility = $_GET['flexibility'] ?? '';
    $orderStatus = $_GET['orderStatus'] ?? '';

    $ataskaitosID = uniqid("SA_");
    $ataskaitosPavadinimas = "Pardavimų ataskaita nuo $startDate iki $endDate";

    $sql = "SELECT Uzsakymas.id_Uzsakymas, Uzsakymas.data, Preke.pavadinimas, Preke.kaina, 
            Prekes_lankstumas.name AS lankstumas, Uzsakymo_busena.name AS busena, 
            Prekes_kiekis.kiekis, Uzsakymas.visa_kaina
            FROM Uzsakymas
            JOIN Prekes_kiekis ON Uzsakymas.id_Uzsakymas = Prekes_kiekis.fk_Uzsakymas_id_Uzsakymas
            JOIN Preke ON Prekes_kiekis.fk_Preke_id_Preke = Preke.id_Preke
            JOIN Prekes_lankstumas ON Preke.lankstumas = Prekes_lankstumas.id_Prekes_lankstumas
            JOIN Uzsakymo_busena ON Uzsakymas.busena = Uzsakymo_busena.id_Uzsakymo_busena
            WHERE Uzsakymas.data BETWEEN '$startDate' AND '$endDate'";

    if (!empty($productCategory)) $sql .= " AND Preke.kategorija = '$productCategory'";
    if (!empty($flexibility)) $sql .= " AND Preke.lankstumas = '$flexibility'";
    if (!empty($orderStatus)) $sql .= " AND Uzsakymas.busena = '$orderStatus'";

    $result = $conn->query($sql);

    $csvDir = __DIR__ . '/ataskaitos';
    $csvFile = $csvDir . '/pardavimu_ataskaitos.csv';
    if (!file_exists($csvDir)) mkdir($csvDir, 0777, true);
    $fileHandle = fopen($csvFile, 'a');

    fputcsv($fileHandle, ['Ataskaitos ID', $ataskaitosID, 'Pavadinimas', $ataskaitosPavadinimas, 'Filtras nuo', $startDate, 'Filtras iki', $endDate]);
    fputcsv($fileHandle, ['Pardavimo ID', 'Data', 'Prekė', 'Kaina', 'Lankstumas', 'Būsena', 'Kiekis', 'Visa kaina']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($fileHandle, [
            $row['id_Uzsakymas'], $row['data'], $row['pavadinimas'], $row['kaina'], 
            $row['lankstumas'], $row['busena'], $row['kiekis'], $row['visa_kaina']
        ]);
    }
    fputcsv($fileHandle, []);
    fclose($fileHandle);

    echo "<h3 class='text-center'>Ataskaita sugeneruota sėkmingai!</h3>";
    echo "<a href='pardavimu_ataskaitu_perziura.php' class='btn btn-success centered-button'>Peržiūrėti ataskaitas</a>";
    $conn->close();
}
?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
