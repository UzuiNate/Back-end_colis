<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class EnvoyeurController{

    //CREATION
    public function creationEnvoyeur (int $idEnvoi, string $nomEnvoyeur, string $emailEnvoyeur, $frais_Envoi, $dateEnvois, $nomRecepteur, $contactRecepteur) {

        //$data = file_get_contents("php://input");
        //$data_ = json_decode($data);

        $db = connectDatabase();
        $result = [];
        

        $query = "INSERT INTO envoyeur (idEnvoi,nomEnvoyeur,emailEnvoyeur,frais_Envoi,dateEnvois,nomRecepteur,contactRecepteur)
        VALUES('$idEnvoi', '$nomEnvoyeur', '$emailEnvoyeur', '$frais_Envoi', '$dateEnvois', '$nomRecepteur', '$contactRecepteur');";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //LISTER
    public function listEnvoyeur() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM envoyeur";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //UPDATE
    public function updateEnvoyeur(string $newNom, $oldNom) {
        $db = connectDatabase();
        $result = [];

        $query = "UPDATE envoyeur SET nomEnvoyeur = '". $newNom ."' WHERE nomEnvoyeur = '". $oldNom ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteEnvoyeur(string $delete) {
        $db = connectDatabase();
        $result = [];

        $query = "DELETE FROM envoyeur WHERE idEnvoyeur = '". $delete ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }
}