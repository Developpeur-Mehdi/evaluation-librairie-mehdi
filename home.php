<?php
require('config.php');
session_start(); // S'assurer que la session est démarrée

// Récupérer le nombre total de livres
$queryTotalBooks = "SELECT COUNT(*) as total_books FROM livres";
$stmtTotalBooks = $pdo->prepare($queryTotalBooks);
$stmtTotalBooks->execute();
$resultTotalBooks = $stmtTotalBooks->fetch(PDO::FETCH_ASSOC);

// Récupérer le nombre d'utilisateurs enregistrés
$queryTotalUsers = "SELECT COUNT(*) as total_users FROM utilisateurs";
$stmtTotalUsers = $pdo->prepare($queryTotalUsers);
$stmtTotalUsers->execute();
$resultTotalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC);

// Récupérer le nombre total d'emprunts actifs
$queryActiveLoans = "SELECT COUNT(*) as total_active_loans FROM emprunts WHERE date_retour_effective IS NULL";
$stmtActiveLoans = $pdo->prepare($queryActiveLoans);
$stmtActiveLoans->execute();
$resultActiveLoans = $stmtActiveLoans->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur est connecté
$isAuthenticated = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
    <h1>Librairie XYZ</h1>
</header>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <ul>
            <?php if ($isAuthenticated) : ?>
                <li>Bonjour <?= htmlspecialchars($_SESSION['prenom']); ?></li>
                <li><a href="books.php">Voir la liste des livres</a></li>
                <li><a href="emprunt/view_borrowings.php">Mes emprunts</a></li>
                <li><a href="profile.php">Mon profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else : ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <div class="container">
            <h1>Dashboard</h1>
            <div class="statistics">
                <div class="statistic">
                    <h3>Total des Livres</h3>
                    <p><?= htmlspecialchars($resultTotalBooks['total_books']); ?></p>
                </div>
                <div class="statistic">
                    <h3>Utilisateurs Enregistrés</h3>
                    <p><?= htmlspecialchars($resultTotalUsers['total_users']); ?></p>
                </div>
                <div class="statistic">
                    <h3>Emprunts Actifs</h3>
                    <p><?= htmlspecialchars($resultActiveLoans['total_active_loans']); ?></p>
                </div>
            </div>

            <?php if ($isAuthenticated) : ?>
                <div class="actions">
                    <h3>Actions rapides</h3>
                    <ul>
                        <li><a href="emprunt/borrow_book.php">Emprunter un livre</a></li>
                        <li><a href="emprunt/return_book.php">Retourner un livre</a></li>
                        <li><a href="emprunt/view_borrowings.php">Voir mes emprunts</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; <?= date("Y"); ?> Librairie XYZ</p>
    </div>
</footer>
</body>
</html>
