<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class VoitureController{

    //CREATION
    public function creationVoiture (string $idVoi, string $designVoi, int $fras_Voi, string $Code_It) {

        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        $db = connectDatabase();
        $result = [];
        

        $query = "INSERT INTO voiture (idVoi,designVoi,frais_Voi,Code_It)
        VALUES('$idVoi', '$designVoi', '$fras_Voi','$Code_It');";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //LISTER
    public function listVoiture() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM voiture";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //UPDATE
    public function updateVoiture(string $nemName, $oldNom) {
        $db = connectDatabase();
        $result = [];

        $query = "UPDATE voiture SET idVoi = '". $nemName ."' WHERE idVoi = '". $oldNom ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteVoiture(string $delete) {
        $db = connectDatabase();
        $result = [];

        $query = "DELETE FROM voiture WHERE idVoi = '". $delete ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }
}