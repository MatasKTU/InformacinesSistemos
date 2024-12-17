<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naudotojų elgsenos ataskaita</title>
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
        <span class="navbar-brand text-white">Naudotojų elgsenos ataskaita</span>
        <div class="ml-auto d-flex align-items-center">
            <a href="analitika_main.html" class="btn btn-light ml-2">Atgal</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container content-container">
        <h1 class="text-center">Naudotojų elgsenos ataskaita</h1>
        <form method="GET" action="">
            <!-- Laikotarpio pasirinkimas -->
            <div class="form-group">
                <label for="startDate">Veiklos pradžios data</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">Veiklos pabaigos data</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>

            <!-- Atsiliepimų filtravimas pagal vertinimą -->
            <div class="form-group">
                <label for="reviewRating">Atsiliepimų vertinimas</label>
                <select class="form-control" id="reviewRating" name="reviewRating">
                    <option value="">Visi vertinimai</option>
                    <option value="5">5 žvaigždutės</option>
                    <option value="4">4 žvaigždutės arba geresni</option>
                    <option value="3">3 žvaigždutės arba geresni</option>
                    <option value="2">2 žvaigždutės arba geresni</option>
                    <option value="1">1 žvaigždutė arba geresni</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-block">Generuoti ataskaitą</button>
        </form>
    </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $reviewRating = $_GET['reviewRating'] ?? '';

    $ataskaitosID = uniqid("NAU_");
    $ataskaitosPavadinimas = "Naudotojų elgsenos ataskaita nuo $startDate iki $endDate";

    $sql = "SELECT Naudotojas.el_pastas, Naudotojas.id_Naudotojas, Atsiliepimas.vertinimas, 
                   Pagalbos_uzklausa.pavadinimas AS pagalbos_uzklausa, Uzklausos_busena.name AS uzklausos_busena
            FROM Naudotojas
            LEFT JOIN Atsiliepimas ON Naudotojas.id_Naudotojas = Atsiliepimas.fk_Klientas_id_Naudotojas
            LEFT JOIN Pagalbos_uzklausa ON Naudotojas.id_Naudotojas = Pagalbos_uzklausa.fk_Klientas_id_Naudotojas
            LEFT JOIN Uzklausos_busena ON Pagalbos_uzklausa.busena = Uzklausos_busena.id_Uzklausos_busena
            WHERE (Atsiliepimas.data BETWEEN '$startDate' AND '$endDate' 
                   OR Pagalbos_uzklausa.data BETWEEN '$startDate' AND '$endDate')";

    if (!empty($reviewRating)) {
        $sql .= " AND Atsiliepimas.vertinimas >= '$reviewRating'";
    }

    $result = $conn->query($sql);

    // CSV išsaugojimas
    $csvDir = __DIR__ . '/ataskaitos';
    $csvFile = $csvDir . '/naudotoju_ataskaitos.csv';
    if (!file_exists($csvDir)) mkdir($csvDir, 0777, true);
    $fileHandle = fopen($csvFile, 'a');

    fputcsv($fileHandle, ['Ataskaitos ID', $ataskaitosID, 'Pavadinimas', $ataskaitosPavadinimas, 'Filtras nuo', $startDate, 'Filtras iki', $endDate]);
    fputcsv($fileHandle, ['El. paštas', 'Naudotojo ID', 'Vertinimas', 'Pagalbos užklausa', 'Užklausos būsena']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($fileHandle, [$row['el_pastas'], $row['id_Naudotojas'], $row['vertinimas'], $row['pagalbos_uzklausa'], $row['uzklausos_busena']]);
    }
    fputcsv($fileHandle, []); // Tuščia eilutė

    fclose($fileHandle);

    echo "<h3 class='text-center'>Ataskaita sugeneruota sėkmingai!</h3>";
    echo "<a href='naudotoju_elgsenos_ataskaitu_perziura.php' class='btn btn-success centered-button'>Peržiūrėti ataskaitas</a>";
    $conn->close();
}
?>


    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
