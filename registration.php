<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: registracija</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style_form.css">
</head>
<body>
    <div class="container mt-5">
        <?php 
            if (isset($_POST["submit"])) {

                $name = $_POST["name"];
                $email = $_POST["email"];
                $pass = $_POST["password"];
                $passwordConfirm = $_POST["password_confirm"];

                // *** Validacija podataka
                if (empty($name) || empty($email) || empty($pass) || empty($passwordConfirm)) {
                    echo "<div class='alert alert-danger'>Sva polja su obavezna</div>";
                    goto END;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<div class='alert alert-danger'>Email adresa nije ispravnog formata</div>";
                    goto END;
                }

                if (strlen($pass) < 8) {
                    echo "<div class='alert alert-danger'>Lozinka mora imati najmanje 8 karaktera</div>";
                    goto END;
                }

                if ($pass !== $passwordConfirm) {
                    echo "<div class='alert alert-danger'>Lozinke se ne podudaraju</div>";
                    goto END;
                }
                // ***

                // *** Provera da li korisnik vec postoji u bazi
                include 'database.php';
                $sql  = "SELECT * FROM user WHERE email = :email";
                $stm = $pdo->prepare($sql);
                $stm->bindParam(':email', $email);
                $stm->execute();
                $user = $stm->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    echo "<div class='alert alert-danger'>Lozinke se ne podudaraju</div>";
                    goto END;
                }
                // ***

                // *** Unosimo podatke o korisniku u bazu
                $sql = "INSERT INTO user (name, email, password) VALUES (:name, :email, :password)";
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                $stm = $pdo->prepare($sql);
                $stm->bindParam(':name', $name);
                $stm->bindParam(':email', $email);
                $stm->bindParam(':password', $hashed_password);
                $stm->execute();
                // ***

                echo "<script>alert('Uspesno ste se registrovali.')</script>";
                header("Location: login.php");

            }
            END:
        ?>
        <div class="form-group text-center">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" width="80" height="100" alt="Logo kompanije">
            </a>
        </div>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="Ime i prezime:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Lozinka:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password_confirm" placeholder="Potvrdite lozinku:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Registracija" name="submit">
            </div>
        </form>
    </div>
</body>
</html>