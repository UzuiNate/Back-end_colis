<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class ItineraireController {

    //CREATION
    public function creationItineraire () {
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        $db = connectDatabase();
        $result = [];

        if (!isset($data_['codeIt'], 
                   $data_['villDep'], 
                   $data_['villAr'])
            ){
            $result['status'] = "error";
            $result['message'] = "arguments not set";
        } else {
            $codeIt = $data_['codeIt'];
            $villDep = $data_['villeDep'];
            $villArr = $data_['villeAr'];
            $query = "INSERT INTO itineraire (codeIt,villDep,villArr)
            VALUES('$codeIt', '$villDep', '$villArr');";

            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result['status'] = "success";
                $result['message'] = "insertion successfull";

            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occured while trying to do an insertion";
            }
        }

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

        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $result['status'] = "error";
            $result['message'] = "an error occured while trying to do the listing";
        }

        return json_encode($result);
    }

    //UPDATE
    public function updateItineraire() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['villArr'],
                   $data_['villDep'], 
                   $data_['codeIt'])
            ){
            $result['status'] = "error";
            $result['message'] = "arguments missing";
        } else {
            $villAr = $data_['villArr'];
            $villDep = $data_['villDep'];
            $codeIt = $data_['codeIt'];

            $query = "UPDATE itineraire SET villArr = '$villAr', villDep = '$villDep' WHERE codeIt = '$codeIt';";
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result['status'] = "success";
                $result['message'] = "update succcessfull";

            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an erro occured while updating informations";
            }
        }

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteItineraire(string $delete) {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['delete'])){
            $result['status'] = "error";
            $result['message'] = "missing argument";
        } else {
            $delete = $data_['delete'];

            $query = "DELETE FROM itineraire WHERE codeIt = '". $delete ."';";
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "deletion successfull";

            } catch (PDOException $e) {
                $result['status'] = "error";
                $result['message'] = "an error occured while trying a deletion";
            }
        }

        return json_encode($result);
    }

}
