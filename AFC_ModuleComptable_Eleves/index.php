<?php

session_start();
require_once("Include/class.FicheFrais.inc.php");
require_once("Include/class.CategorieFraisForfaitise.inc.php");
require_once("Include/fct.inc.php");
require_once("Include/class.pdogsb.inc.php");
require_once("Include/Bibliotheque01.inc.php");
include("Vues/v_entete.php");

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

if (!isset($_REQUEST['uc']) || !$estConnecte) {
    $_REQUEST['uc'] = 'connexion';
}

$uc = $_REQUEST['uc'];

switch ($uc) {
    case 'connexion': {
            include("controleurs/c_connexion.php");
            break;
        }
    case 'validerFicheFrais': {
            include ("controleurs/c_valideFrais.php");
            break;
    }
    case 'cloturerSaisieFichesFrais':
    {
            include ("controleurs/c_clotureFichesSaisie.php");
            break;
    }
//	case 'gererFrais' :{
//		include("controleurs/c_gererFrais.php");break;
//	}
//	case 'etatFrais' :{
//		include("controleurs/c_etatFrais.php");break;
//	}
}

//$pdo->listeVisiteur();

include("Vues/v_pied.php");
?>

