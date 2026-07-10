<?php

use BcMath\Number;
require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "connection.php";

class RecepteurController{

    //CREATION
    public function creationRecepteur () {

        $data = file_get_contents("php://input");
        $data_ = json_decode($data ,true);

        $db = connectDatabase();
        $result = [];
        
        if (!isset($data_['codeRecept'], $data_['date_recept'])){
            $result['status'] = "error";
            $result['message'] = "argument missing";

        } else {
            $codeRecept = $data_['codeRecept'];
            $date_recept = $data_['date_recept'];

            $query = "INSERT INTO recepteur (cedeRecept,date_recept)
            VALUES('$codeRecept', '$date_recept');";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "insertion successfull";

                $destinateur = "SELECT envoyeur.emailEnvoyeur FROM envoyeur JOIN colis ON envoyeur.idEnvoi = colis.id_Envoi JOIN recepteur ON colis.code_Recept = recepteur.codeRecept 
                    WHERE recepteur.codeRecept = '$codeRecept';";
                $message = "Votre colis a été bien reçu !";
                $contenu = "SELECT e.dateEnvois AS Date_Envoi,
                    e.nomEnvoyeur AS Nom_Expediteur,
                    v.idVoi AS ID_Voiture,
                    i.villDep AS Ville_Depart,
                    i.villArr AS Ville_Arrivee,
                    c.designColis AS Description_Colis,
                    e.frais_Envoi AS Frais_Envoi,
                    e.nomRecepteur AS Nom_Destinataire,
                    e.contactRecepteur AS Contact_Destinataire
                    FROM colis c
                    JOIN envoyeur e ON c.id_Envoi = e.idEnvoi
                    JOIN voiture v ON c.id_Voi = v.idVoi
                    JOIN itineraire i ON v.code_It = i.codeIt
                    WHERE c.idColis = '$codeRecept';";

                $statement = $db->prepare($contenu);
                $statement->execute();
                $pdf_result = $statement->fetchAll(PDO::FETCH_ASSOC);

                $statement02 = $db->prepare($destinateur);
                $statement02->execute();
                $dest_result = $statement02->fetchAll(PDO::FETCH_ASSOC);


                 
                mail($destinateur, $message , $contenu);
            } catch (PDOException $e){
                $result['status'] = "error";
                $result['message'] = "an error occured while trying an insert";
            }
        }
        
        return json_encode($result);
    }

    //LISTER
    public function listRecepteur() {
        $db = connectDatabase();
        $result = [];

        $query = "SELECT * FROM recepteur";
        
        try{
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $result['status'] = "error";
            $result['message'] = "an error occured while trying to list all receivers";
        }

        return json_encode($result);
    }

    //UPDATE
    public function updateVoiture() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['newName'], $data_['oldName'])){

        } else {
            $newName = $data_['newName'];
            $oldNom = $data_['oldNom'];

            $query = "UPDATE recepteur SET codeRecept = '". $newName ."' WHERE codeRecept = '". $oldNom ."';";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "update successfull";
    
            } catch (PDOException $e) {
                $result['status'] = "error";
                $result['message'] = "an error occured while attempting an update";
            }
        }

        return json_encode($result);
    }

    //SUPPRIMER
    public function deleteRecepteur() {
        $db = connectDatabase();
        $result = [];
        $data = file_get_contents("php://input");
        $data_ = json_decode($data, true);

        if (!isset($data_['delete'])){
            $result['status'] = "error";
            $result['message'] = "missing argument for deletion";

        } else {
            $delete = $data_['delete'];
            $query = "DELETE FROM recepteur WHERE codeRecept = '". $delete ."';";

            try {
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result['status'] = "success";
                $result['message'] = "deletion successfull";

            } catch (PDOException $e) {
                $result['status'] = "error";
                $result['message'] = "an erro occured while attempting a deletion";
            }
        }

        return json_encode($result);
    }
}