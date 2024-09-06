<?php
// Inclure la connexion à la base de données
include 'connexion.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $typeIdentifiant = $_POST['typeIdentifiant'];
    $identifiant = $_POST['identifiant'];
    $categorieContribuable = $_POST['categorieContribuable'];
    $acteDepot = $_POST['acteDepot'];
    $anneeDepot = $_POST['anneeDepot'];
    $moisDepot = $_POST['moisDepot'];
    $benef_typeIdentifiant = $_POST['benef_typeIdentifiant'];
    $benef_identifiant = $_POST['benef_identifiant'];
    $benef_categorieContribuable = $_POST['benef_categorieContribuable'];
    $resident = $_POST['resident'];
    $nomRaisonSociale = $_POST['nomRaisonSociale'];
    $adresse = $_POST['adresse'];
    $activite = $_POST['activite'];
    $email = $_POST['email'];
    $numTel = $_POST['numTel'];
    $datePayement = $_POST['datePayement'];
    $ref_certif_chez_declarant = $_POST['ref_certif_chez_declarant'];
    $anneeFacturation = $_POST['anneeFacturation'];
    $cnpc = $_POST['cnpc'];
    $pCharge = $_POST['pCharge'];
    $montantHT = $_POST['montantHT'];
    $tauxRS = $_POST['tauxRS'];
    $tauxTVA = $_POST['tauxTVA'];
    $montantTVA = $_POST['montantTVA'];
    $montantTTC = $_POST['montantTTC'];
    $montantRS = $_POST['montantRS'];
    $montantNetServi = $_POST['montantNetServi'];
    $totalHT = $_POST['totalHT'];
    $totalTVA = $_POST['totalTVA'];
    $totalTTC = $_POST['totalTTC'];
    $totalRS = $_POST['totalRS'];
    $totalNetServi = $_POST['totalNetServi'];

    // Créer un nouvel objet DOMDocument
    $dom = new DOMDocument('1.0', 'UTF-8');

    // Créer l'élément racine <DeclarationsRS>
    $declarationsRS = $dom->createElement('DeclarationsRS');
    $declarationsRS->setAttribute('VersionSchema', '1.0');
    $dom->appendChild($declarationsRS);

    // Ajouter l'élément <Declarant>
    $declarant = $dom->createElement('Declarant');
    $declarant->appendChild($dom->createElement('TypeIdentifiant', $typeIdentifiant));
    $declarant->appendChild($dom->createElement('Identifiant', $identifiant));
    $declarant->appendChild($dom->createElement('CategorieContribuable', $categorieContribuable));
    $declarationsRS->appendChild($declarant);

    // Ajouter l'élément <ReferenceDeclaration>
    $referenceDeclaration = $dom->createElement('ReferenceDeclaration');
    $referenceDeclaration->appendChild($dom->createElement('ActeDepot', $acteDepot));
    $referenceDeclaration->appendChild($dom->createElement('AnneeDepot', $anneeDepot));
    $referenceDeclaration->appendChild($dom->createElement('MoisDepot', $moisDepot));
    $declarationsRS->appendChild($referenceDeclaration);

    // Ajouter l'élément <AjouterCertificats>
    $ajouterCertificats = $dom->createElement('AjouterCertificats');
    $certificat = $dom->createElement('Certificat');

    // Ajouter l'élément <Beneficiaire>
    $beneficiaire = $dom->createElement('Beneficiaire');
    $idTaxpayer = $dom->createElement('IdTaxpayer');
    $matriculeFiscal = $dom->createElement('MatriculeFiscal');
    $matriculeFiscal->appendChild($dom->createElement('TypeIdentifiant', $benef_typeIdentifiant));
    $matriculeFiscal->appendChild($dom->createElement('Identifiant', $benef_identifiant));
    $matriculeFiscal->appendChild($dom->createElement('CategorieContribuable', $benef_categorieContribuable));
    $idTaxpayer->appendChild($matriculeFiscal);
    $beneficiaire->appendChild($idTaxpayer);
    $beneficiaire->appendChild($dom->createElement('Resident', $resident));
    $beneficiaire->appendChild($dom->createElement('NometprenonOuRaisonsociale', $nomRaisonSociale));
    $beneficiaire->appendChild($dom->createElement('Adresse', $adresse));
    $beneficiaire->appendChild($dom->createElement('Activite', $activite));

    // Ajouter les informations de contact
    $infosContact = $dom->createElement('InfosContact');
    $infosContact->appendChild($dom->createElement('AdresseMail', $email));
    $infosContact->appendChild($dom->createElement('NumTel', $numTel));
    $beneficiaire->appendChild($infosContact);

    $certificat->appendChild($beneficiaire);

    // Ajouter les éléments <DatePayement> et <Ref_certif_chez_declarant>
    $certificat->appendChild($dom->createElement('DatePayement', $datePayement));
    $certificat->appendChild($dom->createElement('Ref_certif_chez_declarant', $ref_certif_chez_declarant));

    // Ajouter l'élément <ListeOperations>
    $listeOperations = $dom->createElement('ListeOperations');
    $operation = $dom->createElement('Operation');
    $operation->setAttribute('IdTypeOperation', 'RS2_000002');
    $operation->appendChild($dom->createElement('AnneeFacturation', $anneeFacturation));
    $operation->appendChild($dom->createElement('CNPC', $cnpc));
    $operation->appendChild($dom->createElement('P_Charge', $pCharge));
    $operation->appendChild($dom->createElement('MontantHT', $montantHT));
    $operation->appendChild($dom->createElement('TauxRS', $tauxRS));
    $operation->appendChild($dom->createElement('TauxTVA', $tauxTVA));
    $operation->appendChild($dom->createElement('MontantTVA', $montantTVA));
    $operation->appendChild($dom->createElement('MontantTTC', $montantTTC));
    $operation->appendChild($dom->createElement('MontantRS', $montantRS));
    $operation->appendChild($dom->createElement('MontantNetServi', $montantNetServi));
    $listeOperations->appendChild($operation);
    $certificat->appendChild($listeOperations);

    // Ajouter l'élément <TotalPayement>
    $totalPayement = $dom->createElement('TotalPayement');
    $totalPayement->appendChild($dom->createElement('TotalMontantHT', $totalHT));
    $totalPayement->appendChild($dom->createElement('TotalMontantTVA', $totalTVA));
    $totalPayement->appendChild($dom->createElement('TotalMontantTTC', $totalTTC));
    $totalPayement->appendChild($dom->createElement('TotalMontantRS', $totalRS));
    $totalPayement->appendChild($dom->createElement('TotalMontantNetServi', $totalNetServi));
    $certificat->appendChild($totalPayement);

    $ajouterCertificats->appendChild($certificat);
    $declarationsRS->appendChild($ajouterCertificats);

    // Enregistrer le document XML
    $dom->formatOutput = true;
    $xmlString = $dom->saveXML();

    // Sauvegarder le fichier XML
    $filename = 'declaration_' . time() . '.xml';
    $dom->save($filename);

    echo "Fichier XML généré avec succès : <a href='$filename'>$filename</a>";
}
?>
