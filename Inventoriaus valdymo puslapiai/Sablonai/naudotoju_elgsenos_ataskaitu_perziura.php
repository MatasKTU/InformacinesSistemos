<?php include_once 'config.php'; ?>
<?php
$csvDir = __DIR__ . '/ataskaitos';
$csvFile = $csvDir . '/naudotoju_ataskaitos.csv';

function loadAtaskaitos($csvFile) {
    $ataskaitos = [];
    $currentReport = null;
    $currentId = null;

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (isset($data[0]) && $data[0] === 'Ataskaitos ID') {
                if ($currentId) $ataskaitos[$currentId] = $currentReport;
                $currentId = $data[1];
                $currentReport = ['info' => $data, 'columns' => [], 'data' => []];
            } elseif (!empty($data) && $data[0] === 'El. paštas') {
                $currentReport['columns'] = $data;
            } elseif (!empty($data)) {
                $currentReport['data'][] = $data;
            }
        }
        if ($currentId) $ataskaitos[$currentId] = $currentReport;
        fclose($handle);
    }
    return $ataskaitos;
}

$ataskaitos = loadAtaskaitos($csvFile);
$selectedAtaskaita = $_POST['ataskaitosId'] ?? null;
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naudotojų elgsenos ataskaitų peržiūra</title>
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
        .table th {
            background-color: #28a745;
            color: #fff;
            text-align: center;
        }
        .table td {
            text-align: center;
        }
    </style>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Naudotojų elgsenos ataskaita</span>
        <div class="ml-auto d-flex align-items-center">
            <a href="analitika_main.html" class="btn btn-light ml-2">Atgal</a>
        </div>
    </nav>
<div class="container mt-5">
    <h1>Naudotojų elgsenos ataskaitų peržiūra</h1>
    <form method="POST">
        <ul class="list-group">
            <?php foreach ($ataskaitos as $id => $report): ?>
                <li class="list-group-item">
                    <button type="submit" name="ataskaitosId" value="<?php echo htmlspecialchars($id); ?>" class="btn btn-link">
                        <?php echo htmlspecialchars($report['info'][3]) . " (Filtrai nuo: {$report['info'][5]} iki: {$report['info'][7]})"; ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </form>

    <?php if ($selectedAtaskaita && isset($ataskaitos[$selectedAtaskaita])): ?>
        <?php $report = $ataskaitos[$selectedAtaskaita]; ?>
        <h3 class="mt-4"><?php echo htmlspecialchars($report['info'][3]); ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php foreach ($report['columns'] as $column): ?>
                        <th><?php echo htmlspecialchars($column); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['data'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?php echo htmlspecialchars($cell); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
