<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelno ataskaita</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar {
            margin-bottom: 30px;
        }
        .content-container {
            margin-top: 30px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .centered-button {
            display: block;
            margin: 20px auto;
            text-align: center;
            background-color: #28a745;
            color: #fff;
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Pelno ataskaita</span>
        <div class="ml-auto d-flex align-items-center">
            <a href="analitika_main.html" class="btn btn-light ml-2">Atgal</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content-container">
        <h1 class="text-center">Pelno ataskaita</h1>
        <form method="GET" action="">
            <!-- Laikotarpio pasirinkimas -->
            <div class="form-group">
                <label for="startDate">Užakymo pradžios data</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">Užakymo pabaigos data</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">Generuoti ataskaitą</button>
        </form>
    </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    // Generuojame unikalų ataskaitos ID
    $ataskaitosID = uniqid("PEL_");
    $ataskaitosPavadinimas = "Pelno ataskaita nuo $startDate iki $endDate";

    // SQL užklausa
    $sql = "SELECT Uzsakymas.id_Uzsakymas, Uzsakymas.data, Uzsakymas.visa_kaina
            FROM Uzsakymas
            WHERE Uzsakymas.data BETWEEN '$startDate' AND '$endDate'";
    $result = $conn->query($sql);

    if (!$result) die("Klaida vykdant užklausą: " . $conn->error);

    // CSV nustatymai
    $csvDir = __DIR__ . '/ataskaitos';
    $csvFile = $csvDir . '/pelno_ataskaitos.csv';
    if (!file_exists($csvDir)) mkdir($csvDir, 0777, true);
    $fileHandle = fopen($csvFile, 'a');

    // Pridedame ataskaitos antraštes
    fputcsv($fileHandle, ['Ataskaitos ID', $ataskaitosID, 'Pavadinimas', $ataskaitosPavadinimas, 'Filtras nuo', $startDate, 'Filtras iki', $endDate]);
    fputcsv($fileHandle, ['Užsakymo ID', 'Data', 'Visa kaina']);

    // Įrašome duomenis
    while ($row = $result->fetch_assoc()) {
        fputcsv($fileHandle, [$row['id_Uzsakymas'], $row['data'], $row['visa_kaina']]);
    }
    fputcsv($fileHandle, []); // Tuščia eilutė atskyrimui

    fclose($fileHandle);

    echo "<h3 class='text-center'>Ataskaita sugeneruota ir įrašyta į CSV failą sėkmingai!</h3>";
    echo "<a href='pelno_ataskaitu_perziura.php' class='btn btn-success centered-button'>Peržiūrėti ataskaitas</a>";

    $conn->close();
}
?>







    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
