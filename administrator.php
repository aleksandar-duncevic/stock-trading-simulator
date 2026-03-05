<?php
    include "session_checker.php";
    include "database.php";

    $id = $_SESSION["id"];

    // *** Dopremanje svih korisnika iz  baze
    $sql = "SELECT * FROM user WHERE id != :id";
    $stm = $pdo->prepare($sql);
    $stm->bindParam(":id", $id);
    $stm->execute();
    $users = $stm->fetchAll();
    // ***

    // Prebrojavanje registrovanih korisnika
    $numOfRegisteredUsers = count($users);

    // *** Prebrojavanje trenutno prijavljenih korisnika
    $sql = "SELECT COUNT(*) AS signed_in_count FROM user WHERE signed_in = 1";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    $signedInCount = $stm->fetch(PDO::FETCH_ASSOC);
    // ***

    // *** Izvlacimo broj najvise prijavljenih korisnika u istom trenutku
    $sql = "SELECT max_signed_in FROM max_signed_in";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    $maxSignedIn = $stm->fetch(PDO::FETCH_ASSOC);
    // ***

    // Paginacija
    $records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;
    if (!in_array($records_per_page, [10, 25, 50, 100])) {
        $records_per_page = 10; // Default to 10 if invalid
    }

    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;

    $offset = ($current_page - 1) * $records_per_page;

    // Izvlacimo uslov za pretragu iz GET zahteva
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

    // *** Konstruisemo SQL upit sa uslovom
    $sql = "SELECT * FROM user WHERE id != :id";
    if (!empty($search_query)) {
        $sql .= " AND (name LIKE :search OR email LIKE :search)";
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
    $users = $stm->fetchAll();
    // ***

    // *** Izvlacimo broj korisnika koji odgovaraju pretrazi
    $sql_count = "SELECT COUNT(*) FROM user WHERE id != :id";
    if (!empty($search_query)) {
        $sql_count .= " AND (name LIKE :search OR email LIKE :search)";
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
    <title>Berza: Administrator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="d-grid gap-3">
        <div class="container">
            <div class="container text-end mt-3">
                <a href="logout.php" class="btn btn-primary">Odjavi se</a>
            </div>
            <div class="container mb-5">
                <table class="table w-auto">
                    <tr>
                        <th>Registrovani korisnici</th>
                        <td><?php echo $numOfRegisteredUsers; ?></td>
                    </tr>
                    <tr>
                        <th>Prijavljeni korisnici</th>
                        <td><?php echo $signedInCount["signed_in_count"]; ?></td>
                    </tr>
                    <tr>
                        <th>Max prijavljenih korisnika</th>
                        <td><?php echo $maxSignedIn["max_signed_in"]; ?></td>
                    </tr>
                </table>
            </div>
            <div class="container">
                <form action="download_users.php" method="post">
                    <input type="submit" class="btn btn-primary" value="Preuzmi podatke" name="submit">
                </form>
            </div>
            <div class="container">
                <a href="administrator_graph.php" class="btn btn-primary" target="_blank">Registracija po mesecima</a>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <h2>Lista korisnika</h2>
            </div>
            <div class="container">
                <form method="get" class="d-flex justify-content-start mb-3">
                    <label for="records_per_page" class="me-2">Prikaži:</label>
                    <select name="records_per_page" id="records_per_page" class="form-select w-auto" onchange="this.form.submit()">
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
                        placeholder="Pretrazi korisnike..." 
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary">Pretraga</button>
                </form>

                <br>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="bg-primary" scope="col">Ime</th>
                            <th class="bg-primary" scope="col">Email</th>
                            <th class="bg-primary" scope="col">Datum registracije</th>
                            <th class="bg-primary" scope="col">Poslednja prijava</th>
                            <th class="bg-primary" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Stampanje liste korisnika
                            foreach ($users as $user) {
                                echo "
                                    <tr>
                                        <td class=align-middle>$user[name]</td>
                                        <td class=align-middle>$user[email]</td>
                                        <td class=align-middle>"; echo date("d/m/Y H:i", strtotime($user['registered_at'])); echo "</td>";  // Formatiranje datuma iz "YYYY-MM-DD HH:MM" u "DD/MM/YYYY HH:MM"
                                        echo "<td class=align-middle>";
                                        if ($user['last_sign_in'] !== NULL) {
                                            echo date("d/m/Y H:i", strtotime($user['last_sign_in']));
                                        } else {
                                            echo "Nema prijave";
                                        }
                                        echo "</td>";
                                        if ($user['is_active'] == 1) {
                                            echo "<td><a class='btn btn-danger' href='activation.php?status=0&id=$user[id]'>Deaktiviraj</a></td>";
                                        } else {
                                            echo "<td><a class='btn btn-success' href='activation.php?status=1&id=$user[id]'>&nbsp;&nbsp;Aktiviraj&nbsp;&nbsp</a></td>";
                                        }
                                echo "</tr>";
                            }
                        ?>
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
    </div>
</body>
</html>

