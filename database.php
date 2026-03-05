<?php
    $server = "localhost"; // Server na kojem se nalazi baza podataka
    $dbname = "stock_market"; // Ime baze podataka
    $username = "your_username"; // Korisničko ime za pristup bazi podataka
    $password = "your_password"; // Lozinka za pristup bazi podataka

    $pdo = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>