<?php
    include "session_checker.php";
    include "database.php";

    $id = $_SESSION["id"];

    // Paginacija
    $records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;
    if (!in_array($records_per_page, [10, 25, 50, 100])) {
        $records_per_page = 10;
    }

    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;

    $offset = ($current_page - 1) * $records_per_page;

    // Izvlacimo uslov za pretragu iz GET zahteva
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

    // *** Konstruisemo SQL upit sa uslovom
    $sql = "SELECT * FROM transaction WHERE user_id = :id";
    if (!empty($search_query)) {
        $sql .= " AND (symbol LIKE :search OR CAST(shares AS CHAR) LIKE :search OR CAST(price AS CHAR) LIKE :search)";
    }
    $sql .= " LIMIT :limit OFFSET :offset";

    $stm = $pdo->prepare($sql);
    $stm->bindParam(":id", $id, PDO::PARAM_INT);
    if (!empty($search_query)) {
        $search_param = "%$search_query%";
        $stm->bindParam(":search", $search_param, PDO::PARAM_STR);
    }
    $stm->bindParam(":limit", $records_per_page, PDO::PARAM_INT);
    $stm->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stm->execute();
    $transactions = $stm->fetchAll();
    // ***

    // *** Izvlacimo broj korisnika koji odgovaraju pretrazi
    $sql_count = "SELECT COUNT(*) FROM transaction WHERE user_id = :id";
    if (!empty($search_query)) {
        $sql_count .= " AND (symbol LIKE :search OR CAST(shares AS CHAR) LIKE :search OR CAST(price AS CHAR) LIKE :search)";
    }
    $stm_count = $pdo->prepare($sql_count);
    $stm_count->bindParam(":id", $id, PDO::PARAM_INT);
    if (!empty($search_query)) {
        $stm_count->bindParam(":search", $search_param, PDO::PARAM_STR);
    }
    $stm_count->execute();
    $total_records = $stm_count->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);
    // ***

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berza: Transakcije</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="d-grid gap-3">
    <div class="container-fluid mb-5">
            <nav class="navbar navbar-expand-sm navbar-light bg-light">
                <a href="#" class="navbar-brand">
                    <img 
                        src="images/logo.png" 
                        class="d-line-block align-top" 
                        height="50px" width="40px" alt="logo">
                </a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link active">Portfolio</a>
                        </li>
                        <li class="nav-item">
                            <a href="check_price.php" target="_blank" class="nav-link active">Proveri cenu</a>
                        </li>
                        <li class="nav-item">
                            <a href="buy.php" target="_blank" class="nav-link active">Kupi</a>
                        </li>
                        <li class="nav-item">
                            <a href="sell.php" target="_blank" class="nav-link active">Prodaj</a>
                        </li>
                        <li class="nav-item">
                            <a href="transactions.php" target="_blank" class="nav-link active">Transakcije</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="navbar-nav active">
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Odjavi se</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="container text-end">
            <form action="download_transactions.php" method="post">
            <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Preuzmi podatke" name="submit">
                </div>
            </form>
        </div>
        <div class="container">
            <form method="get" class="d-flex justify-content-start mb-3">
                <label for="records_per_page" class="me-2">Prikaži:</label>
                <select name="records_per_page" id="records_per_page" class="form-select w-auto" onchange="this.form.page.value=1; this.form.submit()">
                    <option value="10" <?= $records_per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $records_per_page == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $records_per_page == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $records_per_page == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <input type="hidden" name="page" value="<?= $current_page ?>">
            </form>

            
            <form method="get" class="d-flex">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="Pretraži transakcije..." 
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn btn-primary">Pretraga</button>
            </form>

            <br>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-start">Simbol</th>
                        <th class="text-end">Kolicina</th>
                        <th class="text-end">Cena</th>
                        <th class="text-end">Vreme transakcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)) : ?>
                        <tr>
                            <td colspan="4" class="text-center">No transactions found.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($transactions as $transaction) : ?>
                            <tr>
                                <td class="text-start"><?= htmlspecialchars($transaction['symbol']) ?></td>
                                <td class="text-end"><?= htmlspecialchars($transaction['shares']) ?></td>
                                <td class="text-end">$<?= htmlspecialchars($transaction['price']) ?></td>
                                <td class="text-end"><?= date("d/m/Y H:i", strtotime($transaction['transacted_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="container">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?>">Prethodna</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?>">Sledeća</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>
