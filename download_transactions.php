<?php
    if (isset($_POST['submit'])) {
        include "session_checker.php";
        include "database.php";

        $id = $_SESSION['id'];
        
        ob_start();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=transactions_export.csv');

        $columns = array('Symbol', 'Shares', 'Price', 'Time_of_transaction');

        $sql = "SELECT symbol, shares, price, transacted_at FROM transaction WHERE user_id = :id";
        $stm = $pdo->prepare($sql);
        $stm->bindParam(":id", $id);
        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        ob_end_clean();

        $output = fopen('php://output', 'w');

        fputcsv($output, $columns);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        } 

        fclose($output);
        exit();
    }
?>