<?php include "session_checker.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Prodaja</title>
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
                include "database.php";

                if (isset($_POST["submit"])) {
                    include "share_price.php";

                    $id = $_SESSION['id'];
                    $cash = $_SESSION['cash'];
                    $symbol = $_POST['symbol']; 
                    $numOfShares = $_POST['shares'];

                    if (empty($symbol) || empty($numOfShares)) {
                        echo "<div class='alert alert-danger'>Sva polja su obavezna</div>";
                        goto END;
                    }

                    $price = getSharePrice($symbol);
                    if (!isset($price)) {
                        echo "<div class='alert alert-danger'>Došlo je do greške. Proverite ispravnost simbola.</div>";
                        goto END;
                    }

                    // *** Dopremamo informacije o deonici
                    $sql = "SELECT * FROM share WHERE symbol = :symbol AND user_id = :id";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":symbol", $symbol);
                    $stm->bindParam(":id", $id);
                    $stm->execute();
                    $result = $stm->fetch(PDO::FETCH_ASSOC);
                    if (!$result) {
                        echo "<div class='alert alert-danger'>Ne posedujete ove deonice</div>";
                        goto END;
                    }
                    if ($result['shares'] < $numOfShares) {
                        echo "<div class='alert alert-danger'>Nemate dovoljno deonica</div>";
                        goto END;
                    }
                    // ***

                    // *** Umanjujemo broj deonica korisnika
                        $updatedNumOfShares = $result['shares'] - $numOfShares;
                        // Ukoliko korisnik nakon prodaje nema vise ovih deonica, brisemo ih iz tabele
                        if ($updatedNumOfShares == 0) {
                            $sql = "DELETE FROM share WHERE symbol = :symbol AND user_id = :id";
                            $stm = $pdo->prepare($sql);
                            $stm->bindParam(":symbol", $symbol);
                            $stm->bindParam(":id", $id);
                            $stm->execute();
                        } else {
                            $sql = "UPDATE share SET shares = :shares WHERE symbol = :symbol AND user_id = :id";
                            $stm = $pdo->prepare($sql);
                            $stm->bindParam(":shares", $updatedNumOfShares);
                            $stm->bindParam(":symbol", $symbol);
                            $stm->bindParam(":id", $id);
                            $stm->execute();
                        }
                    // ***
                        
                    // *** Evidentiramo transakciju sa negativnim brojem deonica
                    $negativeNumOfShares = $numOfShares * -1;
                    $sql = "INSERT INTO transaction (symbol, shares, price, user_id) VALUES (:symbol, :shares, :price, :user_id)";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":symbol", $symbol);
                    $stm->bindParam(":shares", $negativeNumOfShares);
                    $stm->bindParam(":price", $price);
                    $stm->bindParam(":user_id", $id);
                    $stm->execute();
                    // ***
                        
                    // *** Uvecavamo kes korisnika
                    $totalPrice = $price * $numOfShares;
                    $cash += $totalPrice;
                    $_SESSION['cash'] = $cash;
                    $sql = "UPDATE user SET cash = :cash WHERE id = :id";
                    $stm = $pdo->prepare($sql);
                    $stm->bindParam(":cash", $cash);
                    $stm->bindParam(":id", $id);
                    $stm->execute();
                    // ***

                    echo "<div class='alert alert-success'>Uspesna transakcija</div>";
                }
                END:
            ?>
            <form action="" method="post">
                <div class="mb-3">
                    <input name="symbol" autocomplete="off" list="symbols" placeholder="Simbol" class="form-control mx-auto w-auto">
                        <datalist id="symbols">
                            <?php
                                $sql = "SELECT * FROM share WHERE user_id = :id";
                                $stm = $pdo->prepare($sql);
                                $stm->bindParam(":id", $_SESSION['id']);
                                $stm->execute();
                                $shares = $stm->fetchAll();

                                foreach ($shares as $share) {
                                    echo "
                                        <option value='$share[symbol]'>
                                    ";
                                }
                            ?>
                        </datalist>
                </div>
                <div class="mb-3">
                    <input autocomplete="off" class="form-control mx-auto w-auto" min="1" name="shares" placeholder="Kolicina" type="number">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Prodaj" name="submit">
                </div>
            </form>
        </div>
    </div>
</body>
</html>