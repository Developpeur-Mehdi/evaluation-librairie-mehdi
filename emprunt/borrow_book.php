<?php
require('../config.php');
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $input = $_POST['livre_input']; // L'utilisateur peut saisir soit l'ID, soit l'ISBN
        $date_retour_prevue = date('Y-m-d', strtotime('+30 days'));

        // Déterminer si l'entrée est un ISBN ou un ID
        if (is_numeric($input)) {
            // Si l'entrée est un nombre, on suppose que c'est un ID
            $query = "SELECT id, statut FROM livres WHERE id = :input";
        } else {
            // Sinon, on suppose que c'est un ISBN
            $query = "SELECT id, statut FROM livres WHERE isbn = :input";
        }

        // Vérifiez si le livre est disponible
        $stmt = $pdo->prepare($query);
        $stmt->execute([':input' => $input]);
        $livre = $stmt->fetch();

        if ($livre && $livre['statut'] == 'disponible') {
            // Insérez l'emprunt dans la table
            $query = "INSERT INTO emprunts (id_utilisateur, id_livre, date_emprunt, date_retour_prevue) 
                      VALUES (:id_utilisateur, :id_livre, NOW(), :date_retour_prevue)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':id_utilisateur' => $_SESSION['user_id'],
                ':id_livre' => $livre['id'],
                ':date_retour_prevue' => $date_retour_prevue
            ]);

            // Mettez à jour le statut du livre
            $query = "UPDATE livres SET statut = 'emprunté' WHERE id = :id_livre";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id_livre' => $livre['id']]);

            $message = "Livre emprunté avec succès ! Retour prévu pour le $date_retour_prevue.";
        } else {
            $error = "Ce livre n'est pas disponible ou n'existe pas.";
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de la base de données
        $error = "Erreur lors de la transaction avec la base de données : " . $e->getMessage();
    } catch (Exception $e) {
        // Gérer d'autres types d'erreurs générales
        $error = "Une erreur est survenue : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Emprunter un livre</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
       <img class="logo" src="../image/logo.png" alt="Logo Librairie XYZ">
        <h1>Emprunter un livre</h1>
    </header>
    <form method="post" action="">
        <label for="livre_input">ID ou ISBN du Livre :</label>
        <input type="text" name="livre_input" required>
        <button style="margin-bottom: 10px;" type="submit">Emprunter</button>
        <button onclick="window.location.href = '../index.php'">Retour à l'accueil</button>

    </form>
    <?php if (isset($message)) echo "<p style='color: green;'>" . htmlspecialchars($message) . "</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>" . htmlspecialchars($error) . "</p>"; ?>




    <script src="../js/animation.js"></script>
</body>
</html>
