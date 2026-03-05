<?php
    include "session_checker.php";
    include "database.php";
    $id = $_SESSION['id'];
    $sql = "UPDATE user SET signed_in = 0 WHERE id = :id";
    $stm = $pdo->prepare($sql);
    $stm->bindParam(":id", $id);
    $stm->execute();
    session_destroy();

    header("Location: login.php");
?>