<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class ColisController{

    //CREATION
    public function creationColis (int $idColis, string $designColis, int $frais_Colis, string $id_Voi, int $id_Envoi, int $code_Recept) {

        //$data = file_get_contents("php://input");
        //$data_ = json_decode($data);

        $db = connectDatabase();
        $result = [];
        

        $query = "INSERT INTO colis (idColis,designColis,frais_Colis,id_Voi,id_Envoi,code_Recept)
        VALUES('$idColis', '$designColis','$frais_Colis', '$id_Voi','$id_Envoi', '$code_Recept');";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //LISTER
    public function listColis() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM colis";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //UPDATE
    public function updateColis(string $nemName, $oldNom) {
        $db = connectDatabase();
        $result = [];

        $query = "UPDATE colis SET designColis = '". $nemName ."' WHERE designColis = '". $oldNom ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteColis(string $delete) {
        $db = connectDatabase();
        $result = [];

        $query = "DELETE FROM colis WHERE idColis = '". $delete ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

}