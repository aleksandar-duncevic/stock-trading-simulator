<?php
    include "session_checker.php";
    include "database.php";
    include "share_price.php";

    $id = $_SESSION["id"];
    $cash = $_SESSION["cash"];

    // Dopremamo deonice korisnika
    $sql = "SELECT * FROM share WHERE user_id = :id";
    $stm = $pdo->prepare($sql);
    $stm->bindParam(":id", $id);
    $stm->execute();
    $shares = $stm->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Pocetna strana</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
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
        <div class="container text-end">
            <a href="portfolio_chart.php" class="btn btn-primary" target="_blank">Grafički prikaz</a>
        </div>
        <div class="container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-start">Simbol</th>
                        <th class="text-start">Ime</th>
                        <th class="text-end">Broj deonica</th>
                        <th class="text-end">Vrednost deonice</th>
                        <th class="text-end">Ukupna vrednost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // *** Stampanje deonica
                        $sum = 0.00; // ukupna vrednost svih deonica 
                        foreach ($shares as $share) {
                            $value = getSharePrice($share['symbol']); // vrednost jedne deonice
                            $name = getCompanyName($share['symbol']);
                            $total_value = $value * $share['shares']; // ukupna vrednost jedne vrste deonica (vrednost jedne deonice * kolicina)
                            $sum += $total_value;
                            echo "
                                <tr>
                                    <td class=text-start>$share[symbol]</td>
                                    <td class=text-start>$name</td>
                                    <td class=text-end>$share[shares]</td>
                                    <td class=text-end>\$$value</td>
                                    <td class=text-end>\$$total_value</td>
                                </tr>
                            ";
                        }
                        // ***
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>Keš</b></td>
                        <td class="text-end"> <?php echo "\$$cash"; ?> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><b>UKUPNO</b></td>
                        <td class="text-end"> <?php $total = $cash + $sum; echo "\$$total" ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>