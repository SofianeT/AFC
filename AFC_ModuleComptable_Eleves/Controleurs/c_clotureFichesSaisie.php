<?php

require_once 'Include/class.Frais.inc.php';
require_once 'Include/class.FicheFrais.inc.php';

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'cloturerSaisieFichesFrais';
}
$action = $_REQUEST['action'];
switch ($action) {
    case 'demanderConfirmationClotureFiches': {
        $nom = $_SESSION['nom'];
        $prenom = $_SESSION['prenom'];
        include("Vues/v_sommaire.php");
        $nbFicheAcloturer = $pdo->getFicheACloturerNb(afficheMois());
        if($nbFicheAcloturer = 0)
        {        
            ajouterErreur('Aucune fiche a cloturer pour le mois de ' . afficheMois());
            include 'Vues/v_erreurs.php';
        }
        else
        {
        $message = 'Voulez vous cloturer la/les fiche(s) pour le mois de ' . afficheMois();
       include ('Vues/v_messageOuiNon.php');
        }
        break;

        }
    case 'traiterReponseClotureFiches': 
    {
    $nom = $_SESSION['nom'];
    $prenom = $_SESSION['prenom'];
    include("Vues/v_sommaire.php");    
    $cloture = $pdo->clotureFiche($_POST['mois']);
    $message =  " $cloture  fiche(s) ont bien été cloturée(s) ";
    include 'Vues/v_message.php';
    
    break;
    }
default :
    break;

}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

