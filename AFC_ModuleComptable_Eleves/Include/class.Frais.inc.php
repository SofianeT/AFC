<?php

/**
 * Classe Frais
 *
 */
abstract class Frais {

    protected $idVisiteur;
    protected $moisFicheFrais;
    protected $numFrais;

    /**
     * Constructeur de la classe.
     *
     *  Rappel : en PHP le constructeur est toujours nommé
     *          __construct().
     *
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais) {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFicheFrais = $unMoisFicheFrais;
        $this->numFrais = $unNumFrais;
    }

    /**
     * Retourne l'id du visiteur.
     *
     * @return string L'id du visiteur.
     */
    public function getIdVisiteur() {
        return $this->idVisiteur;
    }

    /**
     * Retourne le mois de la fiche de frais.
     *
     * @return string Le mois de la fiche.
     */
    public function getMoisFiche() {
        return $this->moisFicheFrais;
    }

    /**
     * Retourne le numéro du frais (de la ligne).
     *
     * @return int Le numéro du frais.
     */
    public function getNumFrais() {
        return $this->numFrais;
    }

    abstract public function getMontant();

}

final class FraisForfaitise extends Frais {
    
    protected $quantite;
    protected $laCategorieFraisForfatise;

    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $uneQuantite, $uneCategorieFraisForfatise) {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->quantite = $uneQuantite;
        $this->laCategorieFraisForfatise = $uneCategorieFraisForfatise;
    }
    
    public function getQuantite() {
        return $this->quantite;
    }
    
    public function getLaCategorieFraisForfatise() {
        return $this->laCategorieFraisForfatise;
    }
    public function getMontant() {
        
        $test = $this->getQuantite() * $this->getLaCategorieFraisForfatise()->getMontant();
        return $test;             
    }
}

final class FraisHorsForfait extends Frais {
    
    protected $numFrais;
    protected $libelle;
    protected $date;
    protected $montant;
    protected $action;

    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $unLibelle, $uneDate, $unMontant,$uneAction = 'O') {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->numFrais = $unNumFrais;
        $this->libelle = $unLibelle;
        $this->date = $uneDate;
        $this->montant = $unMontant;
        $this->action = $uneAction;  
    }
    
    public function getNumFrais() {
        return $this->numFrais;
    }
    
    public function getMontant() {
        return $this->montant;
    }
    
    public function getLibelle() {
        return $this->libelle;
    }
    
    public function getDate() {
        return $this->date;
    }
    
    public function getAction() {
        return $this->action;
    }
}

