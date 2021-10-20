<?php

require_once './Include/class.pdogsb.inc.php';
require_once './Include/fct.inc.php';
require_once './Include/class.Frais.inc.php';

final class FicheFrais {

    private static $pdo;
    private $idVisiteur;
    private $moisFiche;
    private $nbJustificatifs = 0;
    private $montantValide = 0;
    private $dateDerniereModif;
    private $idEtat;
    private $libelleEtat;
    //private $tabNumLigneFraisForfatise;

    /**
     * On utilise 2 collections pour stocker les frais :
     * plus efficace car on doit extraire soit les FF soit les FHF.
     * Avec une seule collection on serait toujours obligé de parcourir et
     * de tester le type de tous les frais avant de les extraires.
     *
     */
    private $lesFraisForfaitises = []; // Un tableau asociatif de la forme : <idCategorie>, <objet FraisForfaitise>
    private $lesFraisHorsForfait = [];

    /**
     * Un tableau des numéros de ligne des frais forfaitisés.
     * Les lignes de frais forfaitisés sont numérotées en fonction de leur catégorie.
     * Le tableau est static ce qui évite de le déclarer dans chaque instance de
     * FicheFrais.
     *
     */
    static private $tabNumLigneFraisForfaitise = ['ETP' => 1,
        'KM' => 2,
        'NUI' => 3,
        'REP' => 4];

    function __construct($unId, $unMois) {
        self::$pdo = PdoGsb::getPdoGsb();
        $this->idVisiteur = $unId;
        $this->moisFiche = $unMois;
    }
    
    public function initAvecInfosBDD() {
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
        $this->initLesFraisHorsForfait();
    }

    public function initAvecInfosBDDSansFF() {
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisHorsForfait();
    }
    
    public function initAvecInfosBDDSansFHF() {
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
    }
    
/*
    public function initAvecInfosBDD($unMontant, $uneDateDerniereModif, $unIdEtat, $unLibelleEtat,$unNbJustificatifs, $FraisForfatises, $FraisHorsForfait) {
        $this->montantValide = $unMontant;
        $this->dateDerniereModif = $uneDateDerniereModif;
        $this->nbJustificatifs = $unNbJustificatifs;
        $this->idEtat = $unIdEtat;
        $this->libelleEtat = $unLibelleEtat;
        $this->lesFraisForfaitises = $FraisForfatises;
        $this->lesFraisHorsForfait = $FraisHorsForfait;
    }

    public function initAvecInfosBDDSansFF($unId, $unLibelle, $unNbJustif, $FraisHF) {
        $this->idEtat = $unId;
        $this->libelleEtat = $unLibelle;
        $this->nbJustificatifs = $unNbJustif;
        $this->lesFraisHorsForfait = $FraisHF;

    }*/
    
    
    public function initInfosFicheSansLesFrais(){
        
        $laFiche = self::$pdo->getLesInfosFicheFrais($this->idVisiteur, $this->moisFiche);
        
        if ($laFiche) 
        {
            $this->nbJustificatifs = (int)$laFiche['nbJustificatifs'];
            $this->montantValide = $laFiche['montantValide'];
            $this->dateDerniereModif = $laFiche['dateModif'];
            $this->idEtat = $laFiche['idEtat'];
            $this->libelleEtat = $laFiche['libEtat'];
        } 
        else 
        {
            $this->nbJustificatifs = 0;
            $this->montantValide = 0;
            $this->dateDerniereModif = (new DateTime())->format('Y-m-d');
            $this->idEtat = '00';
        }
    } 
    
 /*   initialise les autres informations de la fiche, sans les lignes  de frais. Si la fiche pour le visiteur et le mois considérés  
n'existe pas, l'état de la fiche doit être '00'.  */

    
    public function initLesFraisForfaitises(){
        
        $lesLignes = self::$pdo->infosFicheFraisForfaitise($this->idVisiteur, $this->moisFiche);
        foreach ($lesLignes as &$uneLigne) 
        {
            $infoCategorie = self::$pdo->getInfosCategorieFrais($uneLigne['CFF_ID']);
            $categorie = new CategorieFraisForfaitise($uneLigne['CFF_ID'], $infoCategorie['CFF_LIBELLE'], $infoCategorie['CFF_MONTANT']);
            $this->lesFraisForfaitises[''. self::$tabNumLigneFraisForfaitise[trim($uneLigne['CFF_ID'])]] = 
                new FraisForfaitise($this->idVisiteur, $this->moisFiche, 
                self::$tabNumLigneFraisForfaitise[trim($uneLigne['CFF_ID'])], $uneLigne['LFF_QTE'], $categorie);
        }
    }
    
    public function initLesFraisHorsForfait() {
        
        $lesLignes = self::$pdo->infosFicheFraisHorsForfaitise($this->idVisiteur, $this->moisFiche);
        
        foreach ($lesLignes as &$uneLigne) 
        {
            $fraisHorsForfait = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'],$uneLigne['LFHF_LIBELLE'], $uneLigne['LFHF_DATE'], $uneLigne['LFHF_MONTANT']);
            $this->lesFraisHorsForfait[] = $fraisHorsForfait;
            /*
            $this->lesFraisHorsForfait['' . $uneLigne['FRAIS_NUM']] = 
                new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'],
                $uneLigne['LFHF_LIBELLE'], $uneLigne['LFHF_DATE'], $uneLigne['LFHF_MONTANT']);*/
        }
    }
        


    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param string $idCategorie L'ide de la catégorie du frais forfaitisé.
     * @param int $quantite Le nombre d'unité(s).
     */
    public function ajouterUnFraisForfaitise($idCategorie, $quantite) {
        //$ligne = self::$pdo->getInfosCategorieFrais($idCategorie);
        $unNumFrais = $this->getNumLigneFraisForfaitise($idCategorie);
        $uneCategorie = new CategorieFraisForfaitise($idCategorie);
        $newFrais = new FraisForfaitise($_SESSION['idVisiteur'], $_SESSION['moisFiche'], $unNumFrais, $quantite, $uneCategorie);
        $this->lesFraisForfaitises[$unNumFrais] = $newFrais;
        //$categorieFF = new CategorieFraisForfaitise($idCategorie, $ligne['CFF_LIBELLE'],$ligne['CFF_MONTANT']);
    }

    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param int $numFrais Le numéro de la ligne de frais hors forfait.
     * @param string $libelle Le libellé du frais.
     * @param string $date La date du frais, sous la forme AAAA-MM-JJ.
     * @param float $montant Le montant du frais.
     * @param string $action L'action à réaliser éventuellement sur le frais.
     */
    public function ajouterUnFraisHorsForfait($numFrais, $libelle, $date, $montant, $action = NULL) {
        $nvFrais = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $numFrais, $libelle, $date, $montant, $action);
        $this->lesFraisHorsForfait[$numFrais] = $nvFrais;
    }
    
    public function setNbJustificatifs($nbJustificatif) {
        $this->nbJustificatifs = $nbJustificatif;
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array La collections des frais forfaitisés.
     */
    public function getLesFraisForfaitises() {

        return $this->lesFraisForfaitises;
    }

    /**
     *
     * Retourne un tableau contenant les quantités pour chaque ligne de frais
     * forfaitisé de la fiche de frais.
     *
     * @return array Le tableau demandé.
     */
    public function getLesQuantitesDeFraisForfaitises() {
        $lesQte = []; 
        foreach($this->lesFraisForfaitises as &$FF)
        {
            $lesQte[] = $FF->getQuantite();
        }
    return $lesQte;
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array la collections des frais forfaitisés.
     */
    public function getLesFraisHorsForfait() {
        return $this->lesFraisHorsForfait;
    }

    /**
     *
     * Retourne un tableau associatif d'informations sur les frais forfaitisés
     * de la fiche de frais :
     * - le numéro du frais (numFrais),
     * - son libellé (libelle),
     * - sa date (date),
     * - son montant (montant).
     *
     * @return array Le tableau demandé.
     */
    public function getLesInfosFraisHorsForfait() {
        $lignes = array();
        $lesFraisHF = $this->lesFraisHorsForfait;
        //$lesFraisHF = $this->getLesFraisHorsForfait();
        if (count ($lesFraisHF) > 0){
            foreach($lesFraisHF as &$unFraisHF)
             {
              $uneLigne = array();
              $uneLigne['NUM'] = $unFraisHF->getNumFrais();
              $uneLigne['LIB'] = $unFraisHF->getLibelle();
              $uneLigne['DATE'] = $unFraisHF->getDate();
              $uneLigne['MONTANT'] = $unFraisHF->getMontant();  
              $uneLigne['ACTION'] = $unFraisHF->getAction();  
              
              $lignes[$unFraisHF->getNumFrais()] = $uneLigne;
             }
        ksort($lignes);
        return $lignes;
        }
        else
            {
              return false;
            }
    }
    
    public function getLibelleEtat() {
        return $this->libelleEtat;
    }
    public function getIdEtat() {
        return $this->idEtat;
    }

    public function getNbJustificatifs() {
        return $this->nbJustificatifs;
    }
    /**
     *
     * Retourne le numéro de ligne d'un frais forfaitisé dont l'identifiant de
     * la catégorie est passé en paramètre.
     * Chaque fiche de frais comporte systématiquement 4 lignes de frais forfaitisés.
     * Chaque ligne de frais forfaitisé correspond à une catégorie de frais forfaitisé.
     * Les lignes de frais forfaitisés d'une fiche sont numérotées de 1 à 4.
     * Ce numéro dépend de la catégorie de frais forfaitisé :
     * - ETP : 1,
     * - KM  : 2,
     * - NUI : 3,
     * - REP : 4.
     *
     * @param string $idCategorieFraisForfaitise L'identifiant de la catégorie de frais forfaitisé.
     * @return int Le numéro de ligne du frais.
     *
     */
    private function getNumLigneFraisForfaitise($idCategorieFraisForfaitise) {
        $numligne = self::$tabNumLigneFraisForfaitise[$idCategorieFraisForfaitise];
        return $numligne;
    }

    /**
     *
     * Contrôle que les quantités de frais forfaitisés passées en paramètre
     * dans un tableau sont bien des numériques entiers et positifs.
     * Cette méthode s'appuie sur la fonction lesQteFraisValides().
     *
     * @return booléen Le résultat du contrôle.
     */
    public function controlerQtesFraisForfaitises() {
        return lesQteFraisValides($this->getLesQuantitesDeFraisForfaitises());
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées.
     *
     * @return bool Le résultat de la mise à jour.
     *
     */
    public function mettreAJourLesFraisForfaitises() {
        $tab = [];
        $numFrais = 0;
        $quantite = 0;
        foreach ($this->lesFraisForfaitises as $unFF)
        {
            $numFrais = $unFF->getNumFrais();
            $quantite = $unFF->getQuantite();
            $tab[] = [$numFrais,$quantite];
        }
        $maj = self::$pdo->setLesQuantitesFraisForfaitises($this->idVisiteur, $this->moisFiche, $tab);
        return $maj;
    }
    
    public function mettreAJourLesFraisHorsForfait() {
        $tab = [];
        $numFrais = 0;
        foreach ($this->lesFraisHorsForfait as &$unFHF)
        {
            $numFrais = $unFHF->getNumFrais();
            $action = $unFHF->getAction();
            $tab[] = [$numFrais,$action];
        }
        $maj = self::$pdo->setLesFraisHorsForfait($this->idVisiteur, $this->moisFiche, $tab, $this->nbJustificatifs);
        return $maj;
    }
    
    public function controlerNbJustificatifs(){
        $nbJustificatif = $this->nbJustificatifs;
        $resultat=FALSE;
        if (is_numeric($nbJustificatif))
            {
            if ($nbJustificatif>0)
                {
                $resultat = TRUE;
                } 
            }
           return $resultat;
    }
    
    public function calculerLeMontantValide() 
    {
        $montant = 0;
        $tousLesFrais = array_merge($this->lesFraisForfaitises, $this->lesFraisHorsForfait);
        
        foreach($tousLesFrais as $unFrais)
        {
            $montant += $unFrais->getMontant();
        }
        $this->montantValide = $montant;
        
        return $this->montantValide;
    }
    
    public function valider() {
            self::$pdo->validerFiche($this->idVisiteur, $this->moisFiche, $this->calculerLeMontantValide());
        return true;
    }

}
