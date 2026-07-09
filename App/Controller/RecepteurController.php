<?php

use BcMath\Number;
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class RecepteurController{

    //CREATION
    public function creationRecepteur (int $codeRecept, string $date_recept) {

        //$data = file_get_contents("php://input");
        //$data_ = json_decode($data);

        $db = connectDatabase();
        $result = [];
        

        $query = "INSERT INTO recepteur (cedeRecept,date_recept)
        VALUES('$codeRecept', '$date_recept');";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //LISTER
    public function listRecepteur() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM recepteur";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //UPDATE
    public function updateVoiture(string $nemName, $oldNom) {
        $db = connectDatabase();
        $result = [];

        $query = "UPDATE recepteur SET codeRecept = '". $nemName ."' WHERE codeRecept = '". $oldNom ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteVoiture(string $delete) {
        $db = connectDatabase();
        $result = [];

        $query = "DELETE FROM recepteur WHERE codeRecept = '". $delete ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }
}