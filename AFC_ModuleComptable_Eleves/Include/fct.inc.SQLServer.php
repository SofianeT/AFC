<?php

function getLesVisiteurs($pdo)
{
		$req = "select * from visiteur";
		$res = $pdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
}
function getLesFichesFrais($pdo)
{
		$req = "select * from fiche_frais";
		$res = $pdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
}
function getLesIdFraisForfait($pdo)
{
		$req = "select CFF_ID as id from categorie_frais_forfaitise order by CFF_ID";
		$res = $pdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
}
function getDernierMois($pdo, $idVisiteur)
{
		$req = "select max(FICHE_MOIS) as dernierMois from fiche_frais where VIS_ID = '$idVisiteur'";
		$res = $pdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['dernierMois'];

}
function getMoisSuivant($mois){
		$numAnnee =substr( $mois,0,4);
		$numMois =substr( $mois,4,2);
		if($numMois=="12"){
			$numMois = "01"; 
			$numAnnee++;
		}
		else{
			$numMois++;

		}
		if(strlen($numMois)==1)
			$numMois="0".$numMois;
		return $numAnnee.$numMois;
}
function getMoisPrecedent($mois){
		$numAnnee =substr( $mois,0,4);
		$numMois =substr( $mois,4,2);
		if($numMois=="01"){
			$numMois = "12"; 
			$numAnnee--;
		}
		else{
			$numMois--;
		}
		if(strlen($numMois)==1)
			$numMois="0".$numMois;
		return $numAnnee.$numMois;
}
function creationFichesFrais($pdo)
{
	$lesVisiteurs = getLesVisiteurs($pdo);
	$moisActuel = getMois(date("d/m/Y"));
	$moisDebut = "201001";
	$moisFin = getMoisPrecedent($moisActuel);
	foreach($lesVisiteurs as $unVisiteur)
	{
		$moisCourant = $moisFin;
		$idVisiteur = $unVisiteur['VIS_ID'];
		$n = 1;
		while($moisCourant >= $moisDebut)
		{
			if($n == 1)
			{
				$etat = "CR";
				$moisModif = $moisCourant;
			}
			else
			{
				if($n == 2)
				{
					$etat = "VA";
					$moisModif = getMoisSuivant($moisCourant);
				}
				else
				{
					$etat = "RB";
					$moisModif = getMoisSuivant(getMoisSuivant($moisCourant));
				}
			}
			$numAnnee =substr( $moisModif,0,4);
			$numMois =substr( $moisModif,4,2);
			$dateModif = $numAnnee."-".$numMois."-".rand(1,8);
			$nbJustificatifs = rand(0,12);
//			$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
//			values ('$idVisiteur','$moisCourant',$nbJustificatifs,0,'$dateModif','$etat');";
			$req = "insert into fiche_frais(VIS_ID, FICHE_MOIS, FICHE_NB_JUSTIFICATIFS, FICHE_MONTANT_VALIDE, FICHE_DATE_DERNIERE_MODIF, EFF_ID) 
			values ('$idVisiteur','$moisCourant',$nbJustificatifs,0,'$dateModif','$etat');";
			$pdo->exec($req);
			$moisCourant = getMoisPrecedent($moisCourant);
			$n++;
		}
	}
}
function creationFraisForfait($pdo)
{
	$lesFichesFrais= getLesFichesFrais($pdo);
	$lesIdFraisForfait = getLesIdFraisForfait($pdo);
	foreach($lesFichesFrais as $uneFicheFrais)
	{
		$idVisiteur = $uneFicheFrais['VIS_ID'];
		$mois =  $uneFicheFrais['FICHE_MOIS'];
                $numFrais = 1; // Ajouté à la V2.
                
		foreach($lesIdFraisForfait as $unIdFraisForfait)
		{
			$idFraisForfait = $unIdFraisForfait['id'];
			if(substr($idFraisForfait,0,1)=="K")
			{
				$quantite =rand(300,1000);
			}
			else
			{
				$quantite =rand(2,20);
			}
//			$req = "insert into lignefraisforfait(idvisiteur,mois,idfraisforfait,quantite)
//			values('$idVisiteur','$mois','$idFraisForfait',$quantite);";
			$req = "insert into ligne_frais_forfaitise(VIS_ID, FICHE_MOIS, FRAIS_NUM, CFF_ID, LFF_QTE)
			values('$idVisiteur','$mois', $numFrais, '$idFraisForfait',$quantite);";
			$pdo->exec($req);
                        $numFrais++;
		}
	}

}
function getDesFraisHorsForfait()
{
	$tab = array(
				1 => array(
				      "lib" => "repas avec praticien",
					  "min" => 30,
					  "max" => 50 ),
				2 => array(
				      "lib" => "achat de matériel de papèterie",
					  "min" => 10,
					  "max" => 50 ),
				3	=> array(
				      "lib" => "taxi",
					  "min" => 20,
					  "max" => 80 ),
				4 => array(
				      "lib" => "achat d'espace publicitaire",
					  "min" => 20,
					  "max" => 150 ),
				5 => array(
				      "lib" => "location salle conférence",
					  "min" => 120,
					  "max" => 650 ),
				6 => array(
				      "lib" => "Voyage SNCF",
					  "min" => 30,
					  "max" => 150 ),
				7 => array(
					  "lib" => "traiteur, alimentation, boisson",
					  "min" => 25,
					  "max" => 450 ),
				8 => array(
					  "lib" => "rémunération intervenant/spécialiste",
					  "min" => 250,
					  "max" => 1200 ),
				9 => array(
					  "lib" => "location équipement vidéo/sonore",
					  "min" => 100,
					  "max" => 850 ),
				10 => array(
					  "lib" => "location véhicule",
					  "min" => 25,
					  "max" => 450 ),
				11 => array(
					  "lib" => "frais vestimentaire/représentation",
					  "min" => 25,
					  "max" => 450 ) 
		);
	return $tab;
}
function updateMdpVisiteur($pdo)
{
	$req = "select * from visiteur";
		$res = $pdo->query($req);
		$lesLignes = $res->fetchAll();
		$lettres ="azertyuiopqsdfghjkmwxcvbn123456789";
		foreach($lesLignes as $unVisiteur)
		{
			$mdp = "";
			$id = $unVisiteur['id'];
			for($i =1;$i<=5;$i++)
			{
				$uneLettrehasard = substr( $lettres,rand(33,1),1);
				$mdp = $mdp.$uneLettrehasard;
			}
			
			$req = "update visiteur set mdp ='$mdp' where visiteur.id ='$id' ";
			$pdo->exec($req);
		}


}
function creationFraisHorsForfait($pdo)
{
	$desFrais = getDesFraisHorsForfait();
	$lesFichesFrais= getLesFichesFrais($pdo);
	
	foreach($lesFichesFrais as $uneFicheFrais)
	{
		$idVisiteur = $uneFicheFrais['VIS_ID'];
		$mois =  $uneFicheFrais['FICHE_MOIS'];
 		$nbFrais = rand(0,5);
                $numFrais = 5; // Ajouté à la V2. On considère qu'il y a systématiquement 4 lignes de frais forfaitisés. Donc 5 est le numéro de la première ligne de frais HF.
                        
		for($i=0;$i<=$nbFrais;$i++)
		{
			$hasardNumfrais = rand(1,count($desFrais)); 
			$frais = $desFrais[$hasardNumfrais];
			$lib = $frais['lib'];
			$min= $frais['min'];
			$max = $frais['max'];
			$hasardMontant = rand($min,$max);
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$hasardJour = rand(1,28);
			if(strlen($hasardJour)==1)
			{
				$hasardJour="0".$hasardJour;
			}
			$hasardMois = $numAnnee."-".$numMois."-".$hasardJour;
//			$req = "insert into lignefraishorsforfait(idVisiteur,mois,libelle,date,montant)
//			values('$idVisiteur','$mois','$lib','$hasardMois',$hasardMontant);";
			$req = "insert into ligne_frais_hors_forfait(VIS_ID, FICHE_MOIS, FRAIS_NUM, LFHF_LIBELLE, LFHF_DATE, LFHF_MONTANT)
			values('$idVisiteur','$mois', $numFrais, '$lib','$hasardMois',$hasardMontant);";
			$pdo->exec($req);
                        $numFrais++;
		}
	}
}
function getMois($date){
		@list($jour,$mois,$annee) = explode('/',$date);
		if(strlen($mois) == 1){
			$mois = "0".$mois;
		}
		return $annee.$mois;
}
function majFicheFrais($pdo)
{
	
	$lesFichesFrais= getLesFichesFrais($pdo);
	foreach($lesFichesFrais as $uneFicheFrais)
	{
		$idVisiteur = $uneFicheFrais['VIS_ID'];
		$mois =  $uneFicheFrais['FICHE_MOIS'];
 		$dernierMois = getDernierMois($pdo, $idVisiteur);
		$req = "select sum(LFHF_MONTANT) as cumul from ligne_frais_hors_forfait where VIS_ID = '$idVisiteur' 
				and FICHE_MOIS = '$mois' ";
		$res = $pdo->query($req);
		$ligne = $res->fetch();
		$cumulMontantHorsForfait = $ligne['cumul'];
		$req = "select sum(LFF_QTE * CFF_MONTANT) as cumul from ligne_frais_forfaitise lff inner join categorie_frais_forfaitise cff
                    on	lff.CFF_ID = cff.CFF_ID
                    where lff.VIS_ID = '$idVisiteur' 
			  and lff.FICHE_MOIS = '$mois' ";
		$res = $pdo->query($req);
		$ligne = $res->fetch();
		$cumulMontantForfait = $ligne['cumul'];
		$montantEngage = $cumulMontantHorsForfait + $cumulMontantForfait;
		$etat = $uneFicheFrais['EFF_ID'];
		if($etat == "CR" )
			$montantValide = 0;
		else
			$montantValide = $montantEngage*rand(80,100)/100;
		$req = "update fiche_frais set FICHE_MONTANT_VALIDE =$montantValide where
		idVisiteur = '$idVisiteur' and mois='$mois'";
		$pdo->exec($req);
		
	}
}
?>




