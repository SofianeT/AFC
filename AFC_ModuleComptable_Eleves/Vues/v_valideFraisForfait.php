
<h2>Frais au forfait</h2>
<form name="frmFraisForfait" id="frmFraisForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFF"
      onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais forfaitisés ?');">
    <table>
        <tr>
            <th>Forfait<br />étape</th><th>Frais<br />kilométriques</th><th>Nuitée<br />hôtel</th><th>Repas<br />restaurant</th><th></th>
        </tr>
        <tr>
            <!--<td><input type="text" size="3" name="txtEtape" id="txtEtape" tabindex="30" />-->    
            <td> <?php echo formInputText('txtEtape', 'txtEtape', $ETP, 3, 30, 30,FALSE) ?></td>
           <!-- <td><input type="text" size="3" name="txtKm" id="txtKm" tabindex="35" /></td>--> 
             <td> <?php echo formInputText('txtKm', 'txtKm', $KM, 3, 30, 35,FALSE) ?></td>
           <!-- <td><input type="text" size="3" name="txtNuitee" id="txtNuitee" tabindex="40" /></td>--> 
             <td> <?php echo formInputText('txtNuitee', 'txtNuitee', $NUI, 3, 30, 40,FALSE) ?></td>
            <!--<td><input type="text" size="3" name="txtRepas" id="txtRepas" tabindex="45" /></td>--> 
             <td> <?php echo formInputText('txtRepas', 'txtRepas',$REP, 3, 30, 45,FALSE) ?></td>
            <td>
                <input type="submit" id="btnEnregistrerFF" name="btnEnregistrerFF" value="Enregistrer" tabindex="50" />&nbsp;
                <input type="reset" id="btnReinitialiserFF" name="btnReinitialiserFF" value="Réinitialiser" tabindex="60" />
            </td>
        </tr>
    </table>
</form>
<br />
<br />