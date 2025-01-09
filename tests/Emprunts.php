<?php
namespace App;

class Emprunts {
    private $pdo;

    // Le constructeur prend un objet PDO pour la connexion à la base de données
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getEmpruntsByUser($user_id) {
        try {
            $query = "SELECT e.id_emprunt, l.titre, l.isbn, e.date_emprunt, e.date_retour_prevue, e.date_retour_effective
                      FROM emprunts e
                      JOIN livres l ON e.id_livre = l.id
                      WHERE e.id_utilisateur = :id_utilisateur
                      ORDER BY e.date_emprunt DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id_utilisateur' => $user_id]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la récupération des emprunts : " . $e->getMessage());
        }
    }
}
?>
