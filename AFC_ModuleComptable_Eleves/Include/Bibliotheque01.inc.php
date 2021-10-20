<?php
function formSelectDepuisRecordset($unLabel, $unNom, $id, $unRecordset, $tabIndex, $valeurOptionSelec = null)
{  
    $codeHTML = '<label for="' . $id . '">' . $unLabel . '</label>'
                . '<select name="' . $unNom . '"id="' . $id . '"tabIndex ="' . $tabIndex . '">' . "\n";
    $unRecordset->setFetchMode(PDO::FETCH_NUM);
    $ligne = $unRecordset->fetch();
    /*ajouter un while*/
    while ($ligne != false) 
    {
        if (isset($valeurOptionSelec) && $valeurOptionSelec == $ligne[0])
        {
            $codeHTML = $codeHTML . '<option value="' . $ligne[0] . '" selected="selected">' . $ligne[1] . '</option>';
        }
        else
        {
            $codeHTML = $codeHTML . '<option value="' . $ligne[0] . '">' . $ligne[1] . '</option>' . "\n";
        }
        $ligne = $unRecordset->fetch();
    }
    $codeHTML = $codeHTML . '</select>' . "\n";
    return $codeHTML;
}

function formInputText($leNom, $id, $valeur, $taille, $longueurMax, $tabIndex, $lectureSeule,$obligation= null)
{
    $codeHTML = '<input type="text" name="' . $leNom . '"id="' . $id . '"size="' . $taille . '"'
         . 'maxlength="' . $longueurMax . '"value="' . $valeur . '"tabIndex="' . $tabIndex . '"'; 
     
    if ($lectureSeule == true)
    {
        $codeHTML = $codeHTML . ' readonly="readonly" ';
    }
    if($obligation==true)
    {
         $codeHTML = $codeHTML . ' required="required" ';
    }
    $codeHTML = $codeHTML . '/>';
    
    return $codeHTML;
}

function formBoutonSubmit($name, $id, $value ,$tabIndex)
{
    return '<input type="submit" id="' . $id . '" name="' . $name . '" value="' . $value . '" tabindex="' . $tabIndex . '">';                     
}
     
function  formInputHidden ($name , $id , $value)
{
    return '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value.'">';    
}  

function formTextArea($unLabel, $unNom, $id, $valeur, $cols, $rows, $maxlength, $tabIndex, $readonly = false)
{
    return '<label for="' . $id . '">' . $unLabel . ' :</label> <br />' . "\n" . '<textarea id="' . $id .
            '" name="' . $unNom . '" cols="' . $cols . '" rows="' . $rows . '" ' . 'maxlength="' . $maxlength .
            '" tabindex="' . $tabIndex . '" ' . ($readonly ? 'readonly="readonly"' : '') . '>' . $valeur . '</textarea>';    
} 

function formInputPassword($leLabel, $leNom, $id, $valeur, $taille, $longueurMax, $tabIndex, $obligation ) //Fonction qui creer une zone de texte de type Mot de passe
{
    $codeHTML = '<label for="' . $id . '">' . $leLabel . ' : </label> '
         . '<input type="password" name="' . $leNom . '"id="' . $id . '"size="' . $taille . '"'
         . 'maxlength="' . $longueurMax . '"value="' . $valeur . '"tabIndex="' . $tabIndex . '"'; 
     
    if($obligation==true)
    {
         $codeHTML = $codeHTML . ' required="required" ';
    }
    $codeHTML = $codeHTML . '/>';
    
    return $codeHTML;
}
?> 

