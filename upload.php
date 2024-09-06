<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    
    if ($file) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Assuming the first row is the header
            $header = $data[0];
            $rows = array_slice($data, 1);

            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><DeclarationsRS VersionSchema="1.0"></DeclarationsRS>');

            // Static declarant and reference information
            $declarant = $xml->addChild('Declarant');
            $declarant->addChild('TypeIdentifiant', '1');
            $declarant->addChild('Identifiant', '0003876L');
            $declarant->addChild('CategorieContribuable', 'PM');

            $referenceDeclaration = $xml->addChild('ReferenceDeclaration');
            $referenceDeclaration->addChild('ActeDepot', '0');
            $referenceDeclaration->addChild('AnneeDepot', '2024');
            $referenceDeclaration->addChild('MoisDepot', '07');

            foreach ($rows as $row) {
                $ajouterCertificats = $xml->addChild('AjouterCertificats');
                $certificat = $ajouterCertificats->addChild('Certificat');
                $beneficiaire = $certificat->addChild('Beneficiaire');

                // Mapping Excel data to the XML structure
                $beneficiaire->addChild('IdTaxpayer')->addChild('MatriculeFiscal')->addChild('TypeIdentifiant', $row[0]);
                $beneficiaire->IdTaxpayer->MatriculeFiscal->addChild('Identifiant', $row[1]);
                $beneficiaire->IdTaxpayer->MatriculeFiscal->addChild('CategorieContribuable', $row[2]);
                $beneficiaire->addChild('Resident', $row[3]);
                $beneficiaire->addChild('NometprenonOuRaisonsociale', $row[4]);
                $beneficiaire->addChild('Adresse', $row[5]);
                $beneficiaire->addChild('Activite', $row[6]);

                $infosContact = $beneficiaire->addChild('InfosContact');
                $infosContact->addChild('AdresseMail', $row[7]);
                $infosContact->addChild('NumTel', $row[8]);

                $certificat->addChild('DatePayement', $row[9]);
                $certificat->addChild('Ref_certif_chez_declarant', $row[10]);

                $listeOperations = $certificat->addChild('ListeOperations');
                $operation = $listeOperations->addChild('Operation');
                $operation->addAttribute('IdTypeOperation', 'RS2_000002');
                $operation->addChild('AnneeFacturation', $row[11]);
                $operation->addChild('CNPC', $row[12]);
                $operation->addChild('P_Charge', $row[13]);
                $operation->addChild('MontantHT', $row[14]);
                $operation->addChild('TauxRS', $row[15]);
                $operation->addChild('TauxTVA', $row[16]);
                $operation->addChild('MontantTVA', $row[17]);
                $operation->addChild('MontantTTC', $row[18]);
                $operation->addChild('MontantRS', $row[19]);
                $operation->addChild('MontantNetServi', $row[20]);

                $totalPayement = $certificat->addChild('TotalPayement');
                $totalPayement->addChild('TotalMontantHT', $row[21]);
                $totalPayement->addChild('TotalMontantTVA', $row[22]);
                $totalPayement->addChild('TotalMontantTTC', $row[23]);
                $totalPayement->addChild('TotalMontantRS', $row[24]);
                $totalPayement->addChild('TotalMontantNetServi', $row[25]);
            }

            // Save the XML file
            header('Content-Type: application/xml');
            header('Content-Disposition: attachment; filename="output.xml"');
            echo $xml->asXML();
        } catch (Exception $e) {
            echo 'Erreur : ',  $e->getMessage();
        }
    } else {
        echo 'Aucun fichier téléchargé.';
    }
} else {
    echo 'Requête invalide.';
}
