<?php include "session_checker.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Kupovina</title>
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

                    include "database.php";
                    include "share_price.php";

                    $id = $_SESSION['id'];
                    $cash = $_SESSION['cash'];
                    $symbol = $_POST['symbol']; 
                    $numOfShares = $_POST['shares'];
                    
                    // Validacija podataka
                    if (empty($symbol) || empty($numOfShares)) {
                        echo "<div class='alert alert-danger'>Sva polja su obavezna</div>";
                        goto END;
                    }

                    // Dopremanje cene deonice
                    $price = getSharePrice($symbol);
                    if (!isset($price)) {
                        echo "<div class='alert alert-danger'>Došlo je do greške. Proverite ispravnost simbola.</div>";
                        goto END;
                    }

                    // Izracunavanje ukupne cene
                    $totalPrice = $price * $numOfShares;
                    if ($cash < $totalPrice) {
                        echo "<div class='alert alert-danger'>Nemate dovoljno novca</div>";
                        goto END;
                    }

                    // *** Evidentiramo transakciju
                    $sql = "INSERT INTO transaction (symbol, shares, price, user_id) VALUES (:symbol, :shares, :price, :user_id)";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":symbol", $symbol);
                    $stm->bindParam(":shares", $numOfShares);
                    $stm->bindParam(":price", $price);
                    $stm->bindParam(":user_id", $id);
                    $stm->execute();
                    // ***
                        
                    // *** Evidentiramo deonicu 
                    $sql = "SELECT * FROM share WHERE symbol = :symbol AND user_id = :id";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":symbol", $symbol);
                    $stm->bindParam(":id", $id);
                    $stm->execute();
                    $result = $stm->fetch(PDO::FETCH_ASSOC);
                    if ($result){
                        // Ukoliko korisnik poseduje ovu vrstu deonica uvecavamo njihov broj 
                        $numOfShares += $result['shares'];
                        $sql = "UPDATE share SET shares = :shares WHERE symbol = :symbol AND user_id = :id";
                        $stm = $pdo->prepare($sql);
                        $stm->bindParam(":shares", $numOfShares);
                        $stm->bindParam(":symbol", $symbol);
                        $stm->bindParam(":id", $id);
                        $stm->execute();
                    } else {
                        // Ukoliko korisnik ne poseduje ovu vrstu deonica ubacujemo novi zapisu tabelu
                        $sql = "INSERT INTO share (symbol, shares, user_id) VALUES (:symbol, :shares, :user_id)";
                        $stm = $pdo->prepare($sql);
                        $stm->bindParam(":symbol", $symbol);
                        $stm->bindParam(":shares", $numOfShares);                        
                        $stm->bindParam(":user_id", $id);
                        $stm->execute();
                    }
                    // ***
                        
                    // *** Umanjujemo kes korisnika za cenu deonica
                    $cash -= $totalPrice;
                    $_SESSION['cash'] = $cash;
                    $sql = "UPDATE user SET cash = :cash WHERE id = :id";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":cash", $cash);
                    $stm->bindParam(":id", $id);
                    $stm->execute();
                    // ***

                    echo "<div class='alert alert-success'>Uspešna transakcija</div>";
                }
                END:
            ?>
            
            <form action="" method="post">
                <div class="mb-3">
                    <input name="symbol" list="symbols" autocomplete="off" autofocus placeholder="Simbol" class="form-control mx-auto w-auto">
                    <datalist id="symbols">
                        <option value="Najtrazeniji" disabled>
                        <option value="SOUN">SoundHound AI, Inc.</option>
                        <option value="AMD">Advanced Micro Devices, Inc.</option>
                        <option value="NIO">NIO Inc.</option>
                        <option value="TSLA">Tesla, Inc.</option>
                        <option value="NVDA">NVIDIA Corporation</option>
                        <option value="HLN">Haleon plc</option>
                        <option value="MARA">Marathon Digital Holdings, Inc.</option>
                        <option value="AAPL">APPLE Inc.</option>
                        <option value="SOFI">SoFI Technologies, Inc</option>
                        <option value="PLTR">Palantir Technologies Inc.</option>
                        <option value="BAC">Bank of America Corporation</option>
                        <option value="F">Ford Motor Company</option>
                        <option value="INTC">Intel Corporation</option>
                        <option value="GOOGL">Alphabet Inc.</option>
                        <option value="AMZN">Amazon.com, Inc</option>
                    </datalist>
                </div>
                <div class="mb-3">
                    <input autocomplete="off" class="form-control mx-auto w-auto" min="1" name="shares" placeholder="Kolicina" type="number">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Kupi" name="submit">
                </div>
            </form>
        </div>
    </div>
</body>
</html>