<?php include "session_checker.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Provera</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style_form.css">
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
        <div class="container text-center">
            <?php
                if (isset($_POST['submit'])) {
                    
                    include "share_price.php";

                    $symbol = $_POST['symbol'];
                    if (empty($symbol)) {
                        echo "<div class='alert alert-danger'>Unesite simbol</div>";
                        goto END;
                    }

                    $price = getSharePrice($symbol);
                    if (!isset($price)) {
                        echo "<div class='alert alert-danger'>Doslo je do greske. Proverite ispravnost simbola.</div>";
                        goto END;
                    }
                    $name = getCompanyName($symbol);

                    echo "<div class='alert alert-success'>Cena deonice <b>$symbol</b> ($name) je <b>\$$price</b></div>";
                }
                END:
            ?>
            <form action="" method="post">
                <div class="mb-3">
                    <input autocomplete="off" autofocus="" class="form-control mx-auto w-auto" name="symbol" placeholder="Simbol" type="text">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Proveri" name="submit">
                </div>
            </form>
        </div>
    </div>
</body>
</html>