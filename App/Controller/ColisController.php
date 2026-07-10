<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class ColisController{

    //CREATION
    public function creationColis () {

        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);
        $db = connectDatabase();
        $result = [];

        if (!isset($data_['idColis'], $data_['designColis'], $data_['id_voi'], $data_['id_Envoi'], $data_['code_Recept'])){
            $result['status'] = "error";
            $result['message'] = "argument missmatch for 'colis' creation";
        } else {
            $idColis = $data_['idColis'];
            $designColis = $data_['designColis'];
            $id_Voi = $data_['id_Voi'];
            $id_Envoi = $data_['id_Envoi'];
            $code_Recept = $data_['code_Recept'];

            $query = "INSERT INTO colis (idColis,designColis,id_Voi,id_Envoi,code_Recept)
            VALUES('$idColis', '$designColis', '$id_Voi','$id_Envoi', '$code_Recept');";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result['status'] = "success";
                $result['message'] = "'colis' inserted successfully";

            } catch(PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occured while inserting a new 'colis'";
            }
        }

        return json_encode($result);
    }

    //LISTER
    public function listColis() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM colis";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['status'] = "error";
            $result['message'] = "error while trying to list 'colis'";
        }

        return json_encode($result);
    }

    //UPDATE
    public function updateColis() {
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);
        $db = connectDatabase();
        $result = [];

        if (!isset($data_["newName"], $data_["oldName"])){
            $result['status'] = "error";
            $result["message"] = "argument missmatch";
        } else{
            $newName = $data_['newName'];
            $oldName = $data_['oldName'];

            $query = "UPDATE colis SET designColis = '". $newName ."' WHERE designColis = '". $oldName ."';";
            try{
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occurred while updating the 'colis'";
            }   
        }

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteColis() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['delete'])){
            $result['status'] = "error";
            $result['message'] = "lack of information for the deletion";
        } else {
            $delete = $data_['delete'];

            try{
                $query = "DELETE FROM colis WHERE idColis = '". $delete ."';";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "'colis' deleted successfully";
            } catch (PDOException $e) {
                $result['status'] = "error";
                $result['message'] = "an error occured while deleting a 'colis'";
            }
        }
        
        return json_encode($result);
    }

    //Recher par id
    public function rechercheColis() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['rechercheId'])){
            $result['status'] = "error";
            $result['message'] = "id not set";
        } else {
            $rechercheId = $data_['rechercheId'];
            $query = "SELECT * FROM colis WHERE idColis = '". $rechercheId ."';";
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occured while trying to search for a colis";
            }
        }

        return json_encode($result);
    }

}