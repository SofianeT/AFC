<h2>Frais hors forfait</h2>
<?php if ($lesFHF) { ?>
    <form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFHF"
          onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées au frais hors forfait numéro ?');">
              
        <table>
            <tr>
                <th>Date</th>
                <th>Libelle</th>
                <th>Montant</th>
                <th>Action</th>
                <th>Reporter</th>
                <th>Supprimer</th>
            </tr>
            <?php $i = 0 ?>
            <?php foreach ($lesFHF as $unFHF) {
                ?>
                <tr>  
                    <?php echo formInputHidden('tabInfosFHF['.$i.'][hidNumFHF]', "", $unFHF['NUM'])?>
                    <td><?php echo formInputText('tabInfosFHF['.$i.'][txtDateFHF]', "", $unFHF['DATE'], 12, 30, 30, FALSE) ?></td>
                    <td><?php echo formInputText('tabInfosFHF['.$i.'][txtLibelle]', "", $unFHF['LIB'], 20, 100, 40, FALSE) ?></td>
                    <td><?php echo formInputText('tabInfosFHF['.$i.'][txtMontant]', "", $unFHF['MONTANT'], 10, 7, 50, FALSE) ?></td>

        
                    <td><input type="radio" name="<?php echo 'tabInfosFHF['.$i.'][rbHFAction]'?>" value="O" <?php ($unFHF['ACTION'] == "O" ? "selected" : "") ?> tabindex="70" checked="checked"/></td>
                    <td><input type="radio" name="<?php echo 'tabInfosFHF['.$i.'][rbHFAction]'?>" value="R" <?php ($unFHF['ACTION'] == "R" ? "selected" : "") ?>tabindex="80" /></td>
                    <td><input type="radio" name="<?php echo 'tabInfosFHF['.$i.'][rbHFAction]'?>" value="S" <?php ($unFHF['ACTION'] == "S" ? "selected" : "") ?> tabindex="90" /></td>



                </tr>
                <?php
        $i++;
        ?>
    <?php } ?>

        </table>
        <p>
            Nb de justificatifs pris en compte :&nbsp;
    <?php echo formInputText('txtHFNbJustificatifsPEC', 'txtHFNbJustificatifsPEC', $nbJustificatifs, 5, 30, 30, FALSE) ?>

        </p>
        <td>
            <input class="toHide" type="submit" id="btnEnregistrerFHF" form="frmFraisHorsForfait" name="btnEnregistrerFHF" value="Enregistrer" />&nbsp;
            <input type="reset" id="btnReinitialiserFHF" form="frmFraisHorsForfait" name="btnReinitialiserFHF" value="Réinitialiser" />
        </td>
    </form>
<?php } else {
    ?>
    <p>Pas de frais hors forfait pour cette fiche.</p>
<?php } ?>


<!--<h2>Frais hors forfait</h2>
                <form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="enregModifFHF.php"
                      onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais hors forfait ?');">
                    <table>
                        <tr>
                            <th>Date</th><th>Libellé</th><th>Montant</th><th>Ok</th><th>Reporter</th><th>Supprimer</th>
                        </tr>
                        <tr>
                            <td><input type="text" size="12" name="txtHFDate1" id="txtHFDate1" readonly="readonly" /></td>
                            <td><input type="text" size="50" name="txtHFLibelle1" id="txtHFLibelle1" readonly="readonly" /></td>
                            <td><input type="text" size="10" name="txtHFMontant1" id="txtHFMontant1" readonly="readonly" /></td>
                            <td><input type="radio" name="rbHFAction1" value="O" tabindex="70" checked="checked"/></td>
                            <td><input type="radio" name="rbHFAction1" value="R" tabindex="80" /></td>
                            <td><input type="radio" name="rbHFAction1" value="S" tabindex="90" /></td>
                        </tr>
                        <tr>
                            <td><input type="text" size="12" name="txtHFDate2" id="txtHFDate2" readonly="readonly" /></td>
                            <td><input type="text" size="50" name="txtHFLibelle2" id="txtHFLibelle2" readonly="readonly" /></td>
                            <td><input type="text" size="10" name="txtHFMontant2" id="txtHFMontant2" readonly="readonly" /></td>
                            <td><input type="radio" name="rbHFAction2" value="O" tabindex="100" checked="checked" /></td>
                            <td><input type="radio" name="rbHFAction2" value="R" tabindex="110" /></td>
                            <td><input type="radio" name="rbHFAction2" value="S" tabindex="120" /></td>
                        </tr>
                    </table>
                    <p>
                        Nb de justificatifs pris en compte :&nbsp;
                        <input type="text" size="4" name="txtHFNbJustificatifsPEC" id="txtHFNbJustificatifsPEC" tabindex="130" /><br />

                    </p>
                    <p>
                        <input type="submit" id="btnEnregistrerModifFHF" name="btnEnregistrerModifFHF" value="Enregistrer les modifications des lignes hors forfait" tabindex="140" />&nbsp;
                        <input type="reset" id="btnReinitialiserFHF" name="btnReinitialiserFHF" value="Réinitialiser" tabindex="150" />
                    </p>
                </form>
            </div>
            <br />
            <br />
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

-->