<!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <h2>
            Agent comptable :<br />
            <?php echo $prenom . " " . $nom . "\n"; ?>
        </h2>
    </div>
    <ul id="menuList">
        <li>
        </li>
       <!-- <li class="smenu">
            <a href="index.php?uc=gererFrais&action=saisirFrais" title="Saisie fiche de frais ">Saisie fiche de frais</a>
        </li> 
        <li class="smenu">
            <a href="index.php?uc=etatFrais&action=selectionnerMois" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
        </li> -->
       
       <li class="smenu">
            <a href="index.php?uc=validerFicheFrais&action=choixInitialVisiteur" title="Valider des fiches de frais">Valider des fiches de frais</a>
        </li>
        <li class="smenu">
            <a href="index.php?uc=cloturerSaisieFichesFrais&action=demanderConfirmationClotureFiches" title="Cloturation de fiches de frais">Cloturer des fiches frais </a>
        </li>
        <li class="smenu">
            <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Déconnexion</a>
        </li>
    </ul>
</div>
