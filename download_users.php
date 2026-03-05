<?php
    if (isset($_POST['submit'])) {
        include "database.php";
        
        ob_start();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=users_export.csv');

        $columns = array('Name', 'Email', 'Date of registration', 'Last sign in');

        $sql = "SELECT name, email, registered_at, last_sign_in FROM user";
        $stm = $pdo->prepare($sql);
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