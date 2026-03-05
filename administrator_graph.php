<?php
    include "database.php";
    include "session_checker.php";

    // Izvlacimo broj registracija svakog meseca
    $sql = "
        SELECT DATE_FORMAT(registered_at, '%Y-%m') AS month, COUNT(*) AS users_count
        FROM user
        GROUP BY month
        ORDER BY month ASC
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    $registrationData = $stm->fetchAll(PDO::FETCH_ASSOC);

    $months = [];
    $userCounts = [];
    foreach ($registrationData as $data) {
        $months[] = $data['month'];
        $userCounts[] = $data['users_count'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Administrator</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="container">
        <div class="container mt-5">
                    <h2>Registracija korisnika po mesecima</h2>
                    <canvas id="userChart"></canvas>
                </div>

                <script>
                    var ctx = document.getElementById('userChart').getContext('2d');
                    var userChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($months); ?>,
                            datasets: [{
                                label: 'Broj registrovanih korisnika',
                                data: <?php echo json_encode($userCounts); ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
    </div>
</body>
</html>