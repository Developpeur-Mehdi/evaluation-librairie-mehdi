<?php
require_once 'Emprunts.php';  // Inclure la classe Emprunts

use PHPUnit\Framework\TestCase;
use App\Emprunts;

class EmpruntsTest extends TestCase {

    // Créer un mock de PDO
    public function testGetEmpruntsByUser() {
        // Créer un mock de PDO
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);

        // Configurer le mock de PDO pour que la méthode prepare retourne notre mock de PDOStatement
        $pdoMock->method('prepare')->willReturn($stmtMock);

        // Configurer le mock de PDOStatement pour que la méthode execute ne fasse rien
        $stmtMock->method('execute')->willReturn(true);
        // Configurer le mock pour que fetchAll retourne un tableau simulé d'emprunts
        $stmtMock->method('fetchAll')->willReturn([
            [
                'id_emprunt' => 1,
                'titre' => 'Livre Test',
                'isbn' => '123456789',
                'date_emprunt' => '2025-01-01',
                'date_retour_prevue' => '2025-02-01',
                'date_retour_effective' => null
            ]
        ]);

        // Instancier la classe Emprunts avec le mock de PDO
        $emprunts = new Emprunts($pdoMock);

        // Appeler la méthode getEmpruntsByUser
        $result = $emprunts->getEmpruntsByUser(1);

        // Vérifier que le résultat contient un emprunt avec le bon titre
        $this->assertCount(1, $result);
        $this->assertEquals('Livre Test', $result[0]['titre']);
    }

}
?>
