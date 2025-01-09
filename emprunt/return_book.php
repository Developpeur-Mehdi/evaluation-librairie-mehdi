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
        $id_emprunt = $_POST['id_emprunt'];

        // Vérifiez si l'emprunt existe et appartient à l'utilisateur
        $query = "SELECT id_livre FROM emprunts WHERE id_emprunt = :id_emprunt AND id_utilisateur = :id_utilisateur AND date_retour_effective IS NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':id_emprunt' => $id_emprunt,
            ':id_utilisateur' => $_SESSION['user_id']
        ]);
        $emprunt = $stmt->fetch();

        if ($emprunt) {
            // Mettre à jour la date de retour effective
            $query = "UPDATE emprunts SET date_retour_effective = NOW() WHERE id_emprunt = :id_emprunt";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id_emprunt' => $id_emprunt]);

            // Rendre le livre disponible
            $query = "UPDATE livres SET statut = 'disponible' WHERE id = :id_livre";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id_livre' => $emprunt['id_livre']]);

            $message = "Livre retourné avec succès !";
        } else {
            $error = "Cet emprunt est introuvable ou a déjà été retourné.";
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
    <title>Retourner un livre</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <img class="logo" src="../image/logo.png" alt="Logo Librairie XYZ">
        <h1>Retourner un livre</h1>
    </header>
    <form method="post" action="">
        <label for="id_emprunt">ID de l'emprunt :</label>
        <input type="number" name="id_emprunt" required>
        <button style="margin-bottom: 10px;" type="submit">Retourner</button>
        <button onclick="window.location.href = '../index.php'">Retour à l'accueil</button>

    </form>
    <?php if (isset($message)) echo "<p style='color: green;'>" . htmlspecialchars($message) . "</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>" . htmlspecialchars($error) . "</p>"; ?>


    <script src="../js/animation.js"></script>
</body>
</html>
