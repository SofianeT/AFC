<body>
    <div class="info">
        <ul>
            <li>
                Attention : <br />
                <?= $message ?>
            </li>
        </ul>
    </div>
    <form name="frmChoix" id="frmChoix" action="index.php" method="post">
        <label for="mois">Préciser le mois :</label>
        <input type="number" min="200001" max="209912" value="<?= afficheMois() ?>" id="mois" name="mois" required />
        <input type="hidden" name="uc" value="cloturerSaisieFichesFrais" />
        <input type="hidden" name="action" value="traiterReponseClotureFiches" />
        <input type="submit" name="btnOUI" value="OUI" />
        <a href="index.php"><input type="button" name="btnNON" value="NON" /></a>
    </form>
</body>