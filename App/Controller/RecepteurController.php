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
        
        if (!isset($data_['codeRecept'], 
                   $data_['date_recept'])
            && !is_int($data_['codeRecept'])){
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

/*
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
*/

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

/*
    // Inclure la bibliothèque FPDF (Assurez-vous de l'avoir téléchargée ou installée via composer)
    require('fpdf/fpdf.php');

    function genererFicheLivraison($db, $codeRecept) {
    // 1. Protection contre les injections SQL (Requête préparée)
    $query = "SELECT e.dateEnvois AS Date_Envoi,
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
              WHERE c.code_Recept = :codeRecept;";

        $stmt = $db->prepare($query);
        $stmt->execute(['codeRecept' => $codeRecept]);
        $colis = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$colis) {
            die("Erreur : Aucun colis trouvé pour ce code récepteur.");
        }

        // 2. Initialisation et configuration de FPDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        // Encodage des textes en ISO-8859-1 pour FPDF (évite les bugs d'accents)
        $nomExp = utf8_decode($colis['Nom_Expediteur']);
        $nomDest = utf8_decode($colis['Nom_Destinataire']);
        $descColis = utf8_decode($colis['Description_Colis']);
        $vDep = utf8_decode($colis['Ville_Depart']);
        $vArr = utf8_decode($colis['Ville_Arrivee']);

        // --- EN-TÊTE DU DOCUMENT ---
        $pdf->SetFillColor(30, 58, 138); // Bleu Marine foncé (#1e3a8a)
        $pdf->Rect(0, 0, 210, 40, 'F');
        
        $pdf->SetFont('Helvetica', 'B', 20);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(15, 12);
        $pdf->Cell(100, 10, 'LOGITRANS SERVICES', 0, 0, 'L');
        
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->SetXY(120, 12);
        $pdf->Cell(75, 10, 'FICHE DE LIVRAISON', 0, 1, 'R');

        // Badge Status
        $pdf->SetXY(15, 24);
        $pdf->SetFillColor(16, 185, 129); // Vert (#10b981)
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(35, 6, 'COLIS BIEN RECEVOIR', 0, 0, 'C', true);

        // Espace de sécurité après l'en-tête
        $pdf->Ln(25); 

        // --- BLOCS EXPÉDITEUR & DESTINATAIRE ---
        $pdf->SetTextColor(30, 41, 59); // Couleur texte sombre
        $yStart = $pdf->GetY();

        // Colonne Gauche : Expéditeur
        $pdf->SetXY(15, $yStart);
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetTextColor(30, 58, 138);
        $pdf->Cell(85, 6, 'EXPEDITEUR', 'B', 1, 'L');
        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetX(15); $pdf->Cell(85, 6, 'Nom : ' . $nomExp, 0, 1, 'L');
        $pdf->SetX(15); $pdf->Cell(85, 6, 'Date d\'envoi : ' . $colis['Date_Envoi'], 0, 1, 'L');

        // Colonne Droite : Destinataire
        $pdf->SetXY(110, $yStart);
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetTextColor(30, 58, 138);
        $pdf->Cell(85, 6, 'DESTINATAIRE', 'B', 1, 'L');
        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetX(110); $pdf->Cell(85, 6, 'Nom : ' . $nomDest, 0, 1, 'L');
        $pdf->SetX(110); $pdf->Cell(85, 6, 'Contact : ' . $colis['Contact_Destinataire'], 0, 1, 'L');

        $pdf->Ln(15);

        // --- DÉTAILS DU TRANSPORT ---
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetTextColor(30, 58, 138);
        $pdf->Cell(180, 6, 'DETAILS DU TRANSPORT', 'B', 1, 'L');
        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(90, 7, 'Ville de depart : ' . $vDep, 0, 0, 'L');
        $pdf->Cell(90, 7, 'Ville d\'arrivee : ' . $vArr, 0, 1, 'L');
        $pdf->Cell(90, 7, 'ID Voiture / Transport : ' . $colis['ID_Voiture'], 0, 1, 'L');

        $pdf->Ln(10);

        // --- DESCRIPTION DU COLIS (Tableau) ---
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetFillColor(241, 245, 249); // Gris clair pour l'entête
        $pdf->Cell(180, 8, 'DESIGNATION DU COLIS', 1, 1, 'L', true);
        
        $pdf->SetFont('Helvetica', '', 10);
        // MultiCell gère automatiquement les retours à la ligne si la description est longue
        $pdf->MultiCell(180, 8, $descColis, 1, 'L');

        $pdf->Ln(10);

        // --- MONTANT / FRAIS ---
        $pdf->SetX(110);
        $pdf->SetFillColor(239, 246, 255); // Fond bleu très clair
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(45, 10, 'FRAIS D\'ENVOI :', 1, 0, 'R', true);
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(30, 58, 138);
        $pdf->Cell(40, 10, number_format($colis['Frais_Envoi'], 0, ',', ' ') . ' Ar', 1, 1, 'C', true);

        // --- PIED DE PAGE ---
        $pdf->SetY(-30);
        $pdf->SetFont('Helvetica', 'I', 8);
        $pdf->SetTextColor(148, 163, 184);
        $pdf->Cell(180, 4, utf8_decode('Ce document confirme la bonne réception du colis par le destinataire.'), 0, 1, 'C');
        $pdf->Cell(180, 4, 'Merci d\'avoir choisi LogiTrans Services.', 0, 1, 'C');

        // 3. Sortie du PDF (Téléchargement direct dans le navigateur)
        $pdf->Output('D', 'Fiche_Livraison_' . $codeRecept . '.pdf');
    }
*/

}