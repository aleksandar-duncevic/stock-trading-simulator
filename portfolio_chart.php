<?php
include "session_checker.php";
include "database.php";
include "share_price.php";

$id = $_SESSION["id"];

// Dohvatanje deonica korisnika
$sql = "SELECT * FROM share WHERE user_id = :id";
$stm = $pdo->prepare($sql);
$stm->bindParam(":id", $id);
$stm->execute();
$shares = $stm->fetchAll();

// Priprema podataka za graf
$data = [];
foreach ($shares as $share) {
    $value = getSharePrice($share['symbol']);
    $shares = $share['shares'];
    $data[] = [
        "name" => getCompanyName($share['symbol']),
        "value" => $shares,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Grafik</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="d-grid gap-3">
        <div class="container-fluid mb-5">
            <nav class="navbar navbar-expand-sm navbar-light bg-light">
                <a href="#" class="navbar-brand mb-0 h1">
                    <img 
                        src="images/logo.png" 
                        class="d-line-block align-top" 
                        height="50px" width="40px" alt="logo">
                </a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a href="index.php" class="nav-link active">Portfolio</a>
                        </li>
                        <li class="nav-item active">
                            <a href="check_price.php" target="_blank" class="nav-link active">Proveri cenu</a>
                        </li>
                        <li class="nav-item active">
                            <a href="buy.php" target="_blank" class="nav-link active">Kupi</a>
                        </li>
                        <li class="nav-item active">
                            <a href="sell.php" target="_blank" class="nav-link active">Prodaj</a>
                        </li>
                        <li class="nav-item active">
                            <a href="transactions.php" target="_blank" class="nav-link active">Transakcije</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="navbar-nav active">
                        <li class="navitem">
                            <a href="logout.php" class="nav-link">Odjavi se</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="container">
            <h1 class="text-center">Grafički prikaz</h1>
            <div style="display: flex; justify-content: center; align-items: center; height: 70vh;">
            <canvas id="portfolioChart"></canvas>
            </div>
        </div>

        <script>
            // PHP podaci konvertovani u JSON za JavaScript
            const data = <?php echo json_encode($data); ?>;

            // Generisanje naziva i vrednosti za pie chart
            const labels = data.map(item => item.name);
            const values = data.map(item => item.value);

            // Kreiranje Pie Chart-a
            const ctx = document.getElementById('portfolioChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Portfolio Distribution',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 68, 68, 0.7)',    // Jarka crvena
                            'rgba(255, 153, 51, 0.7)',   // Narandžasta
                            'rgba(102, 204, 0, 0.7)',    // Jarko zelena
                            'rgba(51, 153, 255, 0.7)',   // Svetlo plava
                            'rgba(170, 0, 255, 0.7)',    // Ljubičasta
                            'rgba(255, 0, 128, 0.7)',    // Ružičasta
                            'rgba(0, 255, 204, 0.7)',    // Tirkizna
                            'rgba(255, 204, 0, 0.7)',    // Zlatno žuta
                            'rgba(0, 255, 102, 0.7)',    // Sveža zelena
                            'rgba(255, 0, 0, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 68, 68, 1)',    // Jarka crvena
                            'rgba(255, 153, 51, 1)',   // Narandžasta
                            'rgba(102, 204, 0, 1)',    // Jarko zelena
                            'rgba(51, 153, 255, 1)',   // Svetlo plava
                            'rgba(170, 0, 255, 1)',    // Ljubičasta
                            'rgba(255, 0, 128, 1)',    // Ružičasta
                            'rgba(0, 255, 204, 1)',    // Tirkizna
                            'rgba(255, 204, 0, 1)',    // Zlatno žuta
                            'rgba(0, 255, 102, 1)',    // Sveža zelena
                            'rgba(255, 0, 0, 1)'       // Čista crvena
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    },
                },
            });
        </script>
    </div>
</body>
</html>
