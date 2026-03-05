<?php
    $_SESSION["role_id"] == 1;
        include "database.php";
        $id = $_GET["id"];
        $status = $_GET["status"];
        $sql = "UPDATE user SET is_active = :status WHERE id = :id";
        $stm = $pdo->prepare($sql);
        $stm->bindParam(":id", $id);
        $stm->bindParam(":status", $status);
        $stm->execute();
    

    header("Location: administrator.php");
?>