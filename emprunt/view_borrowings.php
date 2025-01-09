<?php
require('../config.php');
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Récupérer les emprunts de l'utilisateur
    $query = "SELECT e.id_emprunt, l.titre, l.isbn, e.date_emprunt, e.date_retour_prevue, e.date_retour_effective
              FROM emprunts e
              JOIN livres l ON e.id_livre = l.id
              WHERE e.id_utilisateur = :id_utilisateur
              ORDER BY e.date_emprunt DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id_utilisateur' => $_SESSION['user_id']]);
    $emprunts = $stmt->fetchAll();
} catch (PDOException $e) {
    // Gérer les erreurs de la base de données
    $error = "Erreur lors de la récupération des emprunts : " . $e->getMessage();
} catch (Exception $e) {
    // Gérer d'autres types d'erreurs
    $error = "Une erreur est survenue : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mes emprunts</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <img class="logo" src="../image/logo.png" alt="Logo Librairie XYZ">
        <h1>Mes emprunts</h1>
        <button style="width: 15%;" onclick="window.location.href = '../index.php'">Retour à l'accueil</button>

    </header>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>ISBN</th>
                <th>Date d'emprunt</th>
                <th>Date de retour prévue</th>
                <th>Date de retour effective</th>
            </tr>
            <?php foreach ($emprunts as $emprunt): ?>
            <tr>
                <td><?php echo htmlspecialchars($emprunt['id_emprunt']); ?></td>
                <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                <td><?php echo htmlspecialchars($emprunt['isbn']); ?></td>
                <td><?php echo htmlspecialchars($emprunt['date_emprunt']); ?></td>
                <td><?php echo htmlspecialchars($emprunt['date_retour_prevue']); ?></td>
                <td><?php echo $emprunt['date_retour_effective'] ? htmlspecialchars($emprunt['date_retour_effective']) : 'Non retourné'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <script src="../js/animation.js"></script>
</body>
</html>
