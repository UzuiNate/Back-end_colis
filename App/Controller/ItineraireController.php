<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class ItineraireController {

    /*public function NouveauItineraire(string $search) {
        
        //$data = file_get_contents("php://input");
        //$data_ = json_decode($data);

        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM itineraire WHERE villArr = '" . $search . "' 
            OR villDep = '" . $search . "' 
            OR codeIt = '" . $search ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }*/

    //CREATION
    public function creationItineraire (string $codeIt, string $villDep, string $villArr) {

        //$data = file_get_contents("php://input");
        //$data_ = json_decode($data);

        $db = connectDatabase();
        $result = [];
        

        $query = "INSERT INTO itineraire (codeIt,villDep,villArr)
        VALUES('$codeIt', '$villDep', '$villArr');";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //LISTER
    public function afficherItineraire() {
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        $db = connectDatabase();
        $result = [];
        $query = "";

        if($data_["recherche"] !== "") {
            $query = "SELECT * FROM itineraire WHERE villArr LIKE '%" . $data_["recherche"] . "%' OR villDep LIKE '%" . $data_["recherche"] . "%';";
        } else {
            $query = "SELECT * FROM itineraire;";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //UPDATE
    public function updateItineraire(string $update) {
        $db = connectDatabase();
        $result = [];

        $query = "UPDATE itineraire SET villArr = '". $update ."' WHERE villArr = 'TOAMASINA';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteItineraire(string $delete) {
        $db = connectDatabase();
        $result = [];

        $query = "DELETE FROM itineraire WHERE codeIt = '". $delete ."';";
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }



}
