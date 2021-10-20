<?php

/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'sqlsrv:Server=SVRSLAM01';
    //private static $bdd='dbname=gsbV2';
    private static $bdd = 'Database=GSB_VALIDE_MEMEGRINE';
    private static $user = 'AFC_MEMEGRINE';
    private static $mdp = 'afc_memegrine';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    private function __construct() {
        self::$monPdo = new PDO(self::$serveur . ';' . self::$bdd, self::$user, self::$mdp);
        self::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        self::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    public static function getPdoGsb() {
        if (self::$monPdoGsb == null) {
            self::$monPdoGsb = new PdoGsb();
        }
        return self::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom from visiteur
		where visiteur.login='$login' and visiteur.mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }
    
    public function getInfosComptable($login, $mdp) {
        $req = "select CPT_NUM as id, CPT_NOM as nom, CPT_PRENOM as prenom from comptable
		where CPT_LOGIN='" . "$login" . "' and CPT_MDP='" . "$mdp" . "'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }
    
    public function listeVisiteur()
    {
        $req = "exec listeVisiteur";
        $rs = PdoGsb::$monPdo->query($req);
       // $ligne = $rs->fetch(PDO::FETCH_ASSOC);
       // $ligne = $rs->fetch(PDO::FETCH_NUM);
        return $rs;
    }
    
    public function infosFicheFrais($id, $mois)
    {
        $req = "exec infosFicheFrais '" . $id . "','" . $mois."'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }
    public function infosFicheFraisForfaitise($id, $mois)
    {
        $req = "exec infosFicheFraisForfaitise '" . $id . "','" . $mois."'";
        $rs = PdoGsb::$monPdo->query($req);
        $lesLignes = $rs->fetchAll();
        return $lesLignes;
    }
    public function infosFicheFraisHorsForfaitise($id, $mois)
    {
        $req = "exec infosFicheFraisHorsForfaitise '" . $id . "','" . $mois."'";
        $rs = PdoGsb::$monPdo->query($req);
        $lignes = $rs->fetchAll();
        $nbLignes = count($lignes);
        for ($i =0;$i <$nbLignes; $i++)
        {
            $date = $lignes[$i]['LFHF_DATE'];
            $lignes[$i]['LFHF_DATE'] = dateAnglaisVersFrancais(($date));
        }
        return $lignes;
    }
    public function ListeVisiteursDepuisRecordset($valeuropt = NULL) {

        $code = "<label for=\"lstVisiteur\">Visiteur : </label><select name=\"lstVisiteur\" id=\"lstVisiteur\" tabindex=\"10\">\n";

        $recordset = $this->listeVisiteur();

        $recordset->setFetchMode(PDO::FETCH_NUM);

        $ligne = $recordset->fetch();



        if (is_null($valeuropt)) {

            while ($ligne) {

                $code .= "<option value=\"$ligne[0]\">$ligne[1]</option>\n";

                $ligne = $recordset->fetch();

            }

        } else {

            while ($ligne) {

                if ($ligne[0] == $valeuropt) {

                    $code .= "<option value=\"$ligne[0]\" selected>$ligne[1]</option>\n";

                } else {

                    $code .= "<option value=\"$ligne[0]\">$ligne[1]</option>\n";

                }

                $ligne = $recordset->fetch();

            }

        }

        $code .= '</select>';

        return $code;

    }
    
    
    public function getInfosCategorieFrais($unId) {
         $req = "exec SP_CATEGORIE_FF_GET_INFOS '".$unId."'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
        }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur'
		and lignefraishorsforfait.mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "exec nbJustificatifs '$idVisiteur','$mois'";
        //$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle,
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois'
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
     */
  /*  public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs,
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }*/
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select  FICHE_FRAIS.EFF_ID as idEtat, FICHE_DATE_DERNIERE_MODIF as dateModif, FICHE_NB_JUSTIFICATIFS as nbJustificatifs,
			FICHE_MONTANT_VALIDE as montantValide, EFF_LIBELLE as libEtat from  FICHE_FRAIS inner join ETAT_FICHE_FRAIS on FICHE_FRAIS.EFF_ID = ETAT_FICHE_FRAIS.EFF_ID
			where FICHE_FRAIS.VIS_ID ='$idVisiteur' and FICHE_FRAIS.FICHE_MOIS = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }
    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées
     * pour la fiche de frais dont l'id du visiteur et le mois de la fiche sont passés en paramètre.
     * Une transaction est utilisée pour garantir que toutes les mises à jour ont bien abouti, ou aucune.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisForfaitises Un tableau à 2 dimensions contenant pour chaque frais forfaitisé
     * le numéro de ligne et la quantité.
     * @return boolean Le résultat de la mise à jour.
     */
    public function setLesQuantitesFraisForfaitises($unIdVisiteur, $unMois, $lesFraisForfaitises) {
        $ok = FALSE;
        $req = "exec SP_LIGNE_FF_MAJ :idVisiteur, :mois, :num, :quantite";
        $test = self::$monPdo->prepare($req);
        $test->bindParam(':idVisiteur',$unIdVisiteur);
        $test->bindParam(':mois',$unMois);

        try {
            self::$monPdo->beginTransaction();
            foreach ($lesFraisForfaitises as $unFF) {
                $test->bindParam(':num', $unFF[0]);
                $test->bindParam(':quantite', $unFF[1]);
                $test->execute();
            }           
            self::$monPdo->commit();
            $ok = TRUE;
            echo "Mise à jour reussie";
        } catch (Exception $ex) {
            echo $ex->getMessage();
            self::$monPdo->rollBack();
        }
        return $ok;
    }

    
    /**
     *
     * Met à jour les frais hors forfait dans la base de données.
     * La mise à jour consiste à :
     * - reporter ou supprimer certaine(s) ligne(s) des frais hors forfait ;
     * - mettre à jour le nombre de justificatifs pris en compte.
     * Une transaction est utilisée pour assurer la cohérence des données.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisHorsForfait Un tableau à 2 dimensions contenant
     * pour chaque frais hors forfaitisé le numéro de ligne et l'action (R ou S) à effectuer.
     * @param type $nbJustificatifsPEC Le nombre de justificatifs pris en compte.
     * @return bool Le résultat de la mise à jour (TRUE : ok ; FALSE : pas ok).
     */
    public function setLesFraisHorsForfait($unIdVisiteur, $unMois, $lesFraisHorsForfait, $nbJustificatifsPEC) {
        $tab = [0,0];
        $reqSuppr = "exec SP_LIGNE_FHF_SUPPRIMER :idVisiteur, :mois, :num";
        $reqRepor= "exec SP_LIGNE_FHF_REPORTE :idVisiteur, :mois, :num";
        $reqNbjust= "exec SP_FICHE_NB_JPEC_MAJ :idVisiteur , :mois , :nouvNbJust";
        $testSupp = self::$monPdo->prepare($reqSuppr);    
        $testSupp->bindParam(':idVisiteur',$unIdVisiteur);
        $testSupp->bindParam(':mois',$unMois);
        $testSupp->bindParam(':num', $unNumLigne);
        $testRepor = self::$monPdo->prepare($reqRepor); 
        $testRepor->bindParam(':idVisiteur',$unIdVisiteur);
        $testRepor->bindParam(':mois',$unMois); 
        $testRepor->bindParam(':num',$unNumLigne);

        try {
            self::$monPdo->beginTransaction();
            foreach ($lesFraisHorsForfait as $unFHF) {
                $unNumLigne = $unFHF[0];
                switch ($unFHF[1])
                {
                    case 'S': 
                    
                    $testSupp->execute();
                    $tab[0] ++;
                    break;
                    
                    case 'R': 
                    $testRepor->execute();
                    $tab[1] ++;
                    break;
                default :
                    break;
                }
               
            }       
             $testJust = self::$monPdo->prepare($reqNbjust); 
              $testJust->bindParam(':idVisiteur',$unIdVisiteur);
              $testJust->bindParam(':mois',$unMois); 
             $testJust->bindParam(':nouvNbJust',  $nbJustificatifsPEC);
              $testJust->execute();
            
            
            self::$monPdo->commit();
            echo "Mise à jour reussie";
        } catch (Exception $ex) {
            echo $ex->getMessage();
            self::$monPdo->rollBack();
        }
        return $tab;
    }
    
    public function validerFiche($unIdVisiteur, $unMois, $unMontant){
        $req = "exec SP_FICHE_VALIDE :idVisiteur, :mois, :montant";
        $test = self::$monPdo->prepare($req);
        $test->bindParam(':idVisiteur',$unIdVisiteur);
        $test->bindParam(':mois',$unMois);
        $test->bindParam(':montant',$unMontant);
        $test->execute();
    }
    
    public function getFicheACloturerNb($mois) {
        $req = "select dbo.F_FICHE_A_CLOTURER_NB ('$mois')"; 
        $test = self::$monPdo->query($req);
       return $test->fetchColumn(0);  
                  
    }
    
    public function clotureFiche($mois) {
        $req = "exec SP_CLOTURE_FICHE '$mois'";
       $test = self::$monPdo->query($req);
       return $test->rowCount();       
        
    }

}
?>