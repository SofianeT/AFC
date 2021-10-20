<?php

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'choixInitialVisiteur';
}
$action = $_REQUEST['action'];
switch ($action) {
    case 'choixInitialVisiteur': {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $date = afficheMois();
            $visiteurs = $pdo->ListeVisiteursDepuisRecordset(isset($_REQUEST["lstVisiteur"]) ? $_REQUEST["lstVisiteur"] : NULL);
            include("Vues/v_sommaire.php");
            include("Vues/v_valideFraisChoixVisiteur.php");
            include("Vues/v_pied.php");
            break;
        }
    case 'afficherFicheFraisSelectionnee': {
            $_SESSION['idVisiteur'] = $_POST['lstVisiteur'];
            $_SESSION['moisFiche'] = $_POST['txtMoisFiche'];
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $date = afficheMois();
            $visiteurs = $pdo->ListeVisiteursDepuisRecordset(isset($_REQUEST["lstVisiteur"]) ? $_REQUEST["lstVisiteur"] : NULL);
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDD();
            $libelleEtat = $ficheFrais->getLibelleEtat();
            $lesQte = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $nbJustificatifs = $ficheFrais->getNbJustificatifs();
            $lignes = $ficheFrais->getLesFraisForfaitises();

            $lesFHF = $ficheFrais->getLesInfosFraisHorsForfait();

            $ETP = $lignes['1']->getQuantite();
            $KM = $lignes['2']->getQuantite();
            $NUI = $lignes['3']->getQuantite();
            $REP = $lignes['4']->getQuantite();
            $etat = $ficheFrais->getIdEtat();
            if ($etat != "CL") {
                switch ($etat) {
                    case 'RB': ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " à déja été remboursée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                    case 'VA': ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " à déja été validée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                    default :ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " n'est pas cloturée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                }
            } else {
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                break;
            }
            break;
        }
    case 'enregModifFF': {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $date = afficheMois();
            $visiteurs = $pdo->ListeVisiteursDepuisRecordset(isset($_REQUEST["lstVisiteur"]) ? $_REQUEST["lstVisiteur"] : NULL);
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initInfosFicheSansLesFrais();
            $ficheFrais->ajouterUnFraisForfaitise('ETP', $_POST['txtEtape']);
            $ficheFrais->ajouterUnFraisForfaitise('KM', $_POST['txtKm']);
            $ficheFrais->ajouterUnFraisForfaitise('NUI', $_POST['txtNuitee']);
            $ficheFrais->ajouterUnFraisForfaitise('REP', $_POST['txtRepas']);
            if ($ficheFrais->controlerQtesFraisForfaitises()) {

                $ficheFrais->mettreAJourLesFraisForfaitises();
            }

            include("Vues/v_sommaire.php");
            include("Vues/v_valideFraisChoixVisiteur.php");
            break;
        }
    case 'enregModifFHF': {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $date = afficheMois();
            $visiteurs = $pdo->ListeVisiteursDepuisRecordset(isset($_REQUEST["lstVisiteur"]) ? $_REQUEST["lstVisiteur"] : NULL);
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDDSansFHF();
            $libelleEtat = $ficheFrais->getLibelleEtat();
            $lesQte = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $nbJustificatifs = $ficheFrais->getNbJustificatifs();

            //$lesFHF = $ficheFrais->getLesInfosFraisHorsForfait();
            //$lesFHF->ajouterUnFraisHorsForfait($_SESSION['idVisiteur'], $_SESSION['moisFiche'],$_POST['numFHF'], $_POST['txtLibelle'], $_POST['txtDateFHF'],  $_POST['txtMontant'], $_POST['rbHFAction'.$i]);

            foreach ($_REQUEST['tabInfosFHF'] as $unFHF) {

                $ficheFrais->ajouterUnFraisHorsForfait($unFHF['hidNumFHF'], $unFHF['txtLibelle'], $unFHF['txtDateFHF'], $unFHF['txtMontant'], $unFHF['rbHFAction']);
            }
            if ($_POST['txtHFNbJustificatifsPEC'] != $nbJustificatifs) {
                $maj = $ficheFrais->setNbJustificatifs($_POST['txtHFNbJustificatifsPEC']);
            }
            if ($ficheFrais->controlerNbJustificatifs() == true) {
                $maj = $ficheFrais->mettreAJourLesFraisHorsForfait();
            } else {
                echo "erreur";
                $_REQUEST['uc'] = 'validerFicheFrais';
                $_REQUEST['action'] = 'afficherFicheFraisSelectionnee';
            }
        }
    default : {
            include("Vues/v_sommaire.php");
            include("Vues/v_valideFraisChoixVisiteur.php");
            break;
        }
    case 'validerFicheFrais': {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $date = afficheMois();
            $visiteurs = $pdo->ListeVisiteursDepuisRecordset(isset($_REQUEST["lstVisiteur"]) ? $_REQUEST["lstVisiteur"] : NULL);
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDD();
            $etat = $ficheFrais->getIdEtat();

            if ($etat != "CL") {
                switch ($etat) {
                    case 'RB': ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " à déja été remboursée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                    case 'VA': ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " à déja été validée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                    default :ajouterErreur("La fiche de frais de " . $_SESSION['idVisiteur'] . " du mois de " . $_SESSION['moisFiche'] . " n'est pas cloturée");
                        include ("Vues/v_erreurs.php");
                        include("Vues/v_sommaire.php");
                        include("Vues/v_valideFraisChoixVisiteur.php");
                        break;
                }
            } else {
                $libelleEtat = $ficheFrais->getLibelleEtat();
                $lesQte = $ficheFrais->getLesQuantitesDeFraisForfaitises();
                $nbJustificatifs = $ficheFrais->getNbJustificatifs();
                $lignes = $ficheFrais->getLesFraisForfaitises();

                $lesFHF = $ficheFrais->getLesInfosFraisHorsForfait();


                $ficheFrais->valider();
                $ETP = $lignes['1']->getQuantite();
                $KM = $lignes['2']->getQuantite();
                $NUI = $lignes['3']->getQuantite();
                $REP = $lignes['4']->getQuantite();
                $message = "La fiche frais a été validée avec succées";
                include("Vues/v_sommaire.php");
                include ("Vues/v_message.php");
                include("Vues/v_valideFraisCorpsFiche.php");
            }
        }
}
?>
