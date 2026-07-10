<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class EnvoyeurController{

    //CREATION
    public function creationEnvoyeur () {
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);
        $db = connectDatabase();
        $result = [];

        if (!isset($data_['idEnvoi'], $data_['nomEnvoyeur'], $data_['emailEnvoyeur'], $data_['frais_Envoi'], $data_['dateEnvois'], $data_['nomRecepteur'], $data_['contactRecepteur'])){
            $result['status'] = 'error';
            $result['message'] = "argument missmatch";
        } else {
            $idEnvoi = $data_['idEnvoi'];
            $nomEnvoyeur = $data_['nomEnvoyeur'];
            $emailEnvoyeur = $data_['emailEnvoyeur'];
            $frais_Envoi = $data_['frais_Envoi'];
            $dateEnvois = $data_['dateEnvois'];
            $nomRecepteur = $data_['nomRecepteur'];
            $contactRecepteur = $data_['contactRecepteur'];

            $query = "INSERT INTO envoyeur (idEnvoi,nomEnvoyeur,emailEnvoyeur,frais_Envoi,dateEnvois,nomRecepteur,contactRecepteur)
            VALUES('$idEnvoi', '$nomEnvoyeur', '$emailEnvoyeur', '$frais_Envoi', '$dateEnvois', '$nomRecepteur', '$contactRecepteur');";
    
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result['status'] = "success";
                $result['message'] = "sender insertion successfull";

            } catch (PDOException $e) {
                $result['status'] = "error";
                $result['message'] = "an erro occurred while inserting a new sender";
            }
    
        }

        return json_encode($result);
    }

    //LISTER
    public function listEnvoyeur() {
        $db = connectDatabase();
        $result = [];

        try{
            $query = "SELECT * FROM envoyeur";
            $stmt = $db->prepare($query);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            $result['status'] = "error";
            $result['message'] = "an error occured while listing senders";
        }

        return json_encode($result);
    }

    //UPDATE
    public function updateEnvoyeur() {
        $db = connectDatabase();
        $result = [];

        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['newNom'], $data_['oldNom'])){
            $result['status'] = "error";
            $result['message'] = "arguments missing";

        } else {
            $newNom = $data_['newNom'];
            $oldNom = $data_['oldNom'];

            $query = "UPDATE envoyeur SET nomEnvoyeur = '". $newNom ."' WHERE nomEnvoyeur = '". $oldNom ."';";

            try {
                $stmt = $db->prepare($query);
                $stmt->execute();

                $result['status'] = "success";
                $result['message'] = "sender modification successfull";

            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occured while updating a sender's information";
            }
        }

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteEnvoyeur() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['delete'])){
            $result['status'] = "error";
            $result['message'] = "sender id not specified";
        } else {
            $delete = $data_['delete'];
            $query = "DELETE FROM envoyeur WHERE idEnvoyeur = '". $delete ."';";

            try{
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "sender deleted successfully";

            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an erro occured while trying to delete a sender";
            }
        }
        return json_encode($result);
    }
}