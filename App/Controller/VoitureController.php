<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class VoitureController{

    // CREATION
    public function creationVoiture () {
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        $db = connectDatabase();
        $result = [];

        if (!isset($data_['idVoi'], 
                   $data_['designVoi'], 
                   $data_['frais_Voi'],
                   $data_['Code_It'])
            && !is_int($data_['idVoi']) 
            && !is_int($data_['fras_Voi'])
            ){
            $result['status'] = "error";
            $result['message'] = "arguments not set";
        } else {
            $idVoi = $data_['idVoi'];
            $designVoi = $data_['designVoi'];
            $fras_Voi = $data_['fras_Voi'];
            $Code_It = $data_['Code_It'];
            $query = "INSERT INTO voiture (idVoi,designVoi,fras_Voi,Code_It)
            VALUES('$idVoi', '$designVoi', '$fras_Voi','$Code_It');";

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
    public function listVoiture() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM voiture";
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
    public function updateVoiture() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['idVoi'], 
                   $data_['designVoi'], 
                   $data_['frais_Voi'], 
                   $data_['code_It'])
            ){
            $result['status'] = "error";
            $result['message'] = "arguments missing";
        } else {
            $idVoi = $data_['idVoi'];
            $designVoi = $data_['designVoi'];
            $frais_Voi = $data_['frais_Voi'];
            $code_It = $data_['code_It'];

            $query = "UPDATE voitutre SET designVoi = '$designVoi', frais_Voi = '$frais_Voi' WHERE idVoi = '$idVoi';";
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
    public function deleteVoiture(string $delete) {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['delete'])){
            $result['status'] = "error";
            $result['message'] = "missing argument";
        } else {
            $delete = $data_['delete'];

            $query = "DELETE FROM voiture WHERE idVoi = '$delete';";
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