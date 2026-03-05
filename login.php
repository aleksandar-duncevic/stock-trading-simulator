<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Prijava</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style_form.css">
</head>
<body>
    <div class="container mt-5">
        <?php
            if (isset($_POST["login"])) {

                include "database.php";

                $email = $_POST["email"];
                $pass = $_POST["password"];

                // *** Dopremanje informacija o korisniku iz baze
                $sql = "SELECT * FROM user WHERE email = :email";
                $stm = $pdo->prepare($sql);
                $stm->bindParam(':email', $email);
                $stm->execute();
                $user = $stm->fetch(PDO::FETCH_ASSOC);
                // ***
                
                if (!$user) {
                    echo "<div class='alert alert-danger'>Uneti podaci nisu ispravni</div>";
                    goto END;
                }

                if (!password_verify($pass, $user["password"])) {
                    echo "<div class='alert alert-danger'>Uneti podaci nisu ispravni</div>";
                    goto END;
                }
                
                if ($user["is_active"] != 1) {
                    echo "<div class='alert alert-danger'>Vas nalog je deaktiviran</div>";
                    goto END;
                }
                
                // *** Unosimo podatke o koriniku u SESSION niz
                session_start();
                $_SESSION["id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["is_active"] = $user["is_active"];
                $_SESSION["role_id"] = $user["role_id"];
                $_SESSION["cash"] = $user["cash"];
                // ***

                // *** Evidentiramo prijavu
                $sql = "UPDATE user SET last_sign_in = :last_sign_in, signed_in = 1 WHERE email = :email";
                $stm = $pdo->prepare($sql);
                $time= date('Y-m-d H:i:s');
                $stm->bindParam(':last_sign_in', $time);
                $stm->bindParam(':email', $email);
                $stm->execute();
                // ***

                // *** Proveravamo da li je trenutni broj prijavljenih korisnika najveci do sad
                    // Brojimo prijavljene korisnike
                    $sql = "SELECT COUNT(*) AS signed_in_count FROM user WHERE signed_in = 1";
                    $stm = $pdo->prepare($sql);
                    $stm->execute();
                    $signedInCount = $stm->fetch(PDO::FETCH_ASSOC);

                    // Dopremamo podatak o najvecem broju prijavljenih korisnika
                    $sql = "SELECT max_signed_in FROM max_signed_in";
                    $stm = $pdo->prepare($sql);
                    $stm->execute();
                    $maxSignedIn = $stm->fetch(PDO::FETCH_ASSOC);
                    
                    // Poredimo ih
                    if ($signedInCount['signed_in_count'] > $maxSignedIn['max_signed_in']) {
                        $sql = "UPDATE max_signed_in SET max_signed_in = :signed_in_count";
                        $stm = $pdo->prepare($sql);
                        $stm->bindParam(":signed_in_count", $signedInCount['signed_in_count']);
                        $stm->execute();
                    }
                // ***

                if ($_SESSION["role_id"] == 1) {
                    header("Location: administrator.php");
                } else {
                    header("Location: index.php");
                }
            }
            END:
        ?>
        <div class="form-group text-center">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" width="80" height="100" alt="Logo kompanije">
            </a>
        </div>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email:" name="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Lozinka:" name="password">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Prijava" name="login">
            </div>
            <div class="text-center mt-2">
                <p>Nemate nalog? <a href="registration.php">Registrujte se</a></p>
            </div>
        </form>
    </div>
</body>
</html>