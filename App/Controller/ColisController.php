<?php
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class ColisController{

    //CREATION
    public function creationColis () {

        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);
        $db = connectDatabase();
        $result = [];

        if (!isset($data_['idColis'], 
                   $data_['designColis'], 
                   $data_['id_voi'], 
                   $data_['id_Envoi'], 
                   $data_['code_Recept'])
                   
            && !is_int($data_['idColis']) 
            && !is_int($data_['id_Envoi']) 
            && !is_int($data_['code_Recept'])
            ){
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
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['idColis'], $data_['designColis'], $data_['id_Voi'], $data_['id_Envoi'], $data_['code_Recept'])){
            $result['status'] = "error";
            $result['message'] = "arguments missing";
        } else {
            $idColis = $data_['idColis'];
            $designColis = $data_['designColis'];
            $id_Voi = $data_['id_Voi'];
            $id_Envoi = $data_['id_Envoi'];
            $code_Recept = $data_['code_Recept'];

            $query = "UPDATE colis SET 
                designColis = '$designColis',
                id_Voi = '$id_Voi',
                id_Envoi = '$id_Envoi',
                code_Recept = '$code_Recept',
                 WHERE idColis = '$idColis';";
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
                $query = "DELETE FROM colis WHERE idColis = '$delete';";
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

    //RECHERCHE PAR ID
    public function rechercheColis() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['rechercheId'])){
            $result['status'] = "error";
            $result['message'] = "id not set";
        } else {
            $rechercheColis = $data_['rechercheId'];
            $query = "SELECT * FROM colis WHERE idColis = '$rechercheColis' OR designColis LIKE '%$rechercheColis%';";
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

    //CALCUL DE SOMME
    public function sommeColis() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT SUM(frais_Envoi) FROM envoyeur;";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result['status'] = "error";
            $result['message'] = "error while trying to sum 'colis'";
        }

        return json_encode($result);
    }

    //LISTER LES COLIS RECU
    public function listColisRecu() {
        $db = connectDatabase();
        $result = [];

        try{
            $query = "SELECT colis.idColis, envoyeur.idEnvoi, envoyeur.nomEnvoyeur, envoyeur.emailEnvoyeur, recepteur.codeRecept, recepteur.date_recept
                FROM envoyeur 
                JOIN colis ON colis.id_Envoi = envoyeur.idEnvoi
                JOIN recepteur ON colis.code_Recept = recepteur.codeRecept;";
            $stmt = $db->prepare($query);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            $result['status'] = "error";
            $result['message'] = "an error occured while listing senders";
        }

        return json_encode($result);
    }

    //LISTER LES COLIS ENVOYER
    public function listColisEnvoyer() {
        $db = connectDatabase();
        $result = [];

        try{
            $query = "SELECT colis.idColis, envoyeur.idEnvoi, envoyeur.nomEnvoyeur, envoyeur.emailEnvoyeur, envoyeur.frais_Envoi, envoyeur.dateEnvois, envoyeur.nomRecepteur, envoyeur.contactRecepteur
                FROM colis
                JOIN envoyeur ON colis.id_Envoi = envoyeur.idEnvoi;";
            $stmt = $db->prepare($query);
            $stmt->execute();
    
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e){
            $result['status'] = "error";
            $result['message'] = "an error occured while listing senders";
        }

        return json_encode($result);
    }
}