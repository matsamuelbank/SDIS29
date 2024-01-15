<!-- affichage d'une intervention / Derniere modification le 23 mai 2019 par Pascal Blain -->
<?php
$nbi = count($lesInterventions);
$titre = "Ajout";
$titre2 = "Interventions réalisées";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage d'une intervention</title>
    <style>
        .conteneur-flex {
            display: flex;
        }

        .div-gauche,
        .div-droite {
            flex: 1;
        }

        #pompierDeGarde,#pompierIntervention{
            border: 2px solid gray;
            flex: 1;
            padding: 5px;
            justify-content: space-between;
        }
        #lstPompier{
            display: flex;
        }

        .lePompier{
            border: 2px solid gray;
            padding: 4px;
            
        }
        #boutonAjoutIntervention
        {
            width : 312px;
        }

    </style>
</head>

<body>
    <form id="monFormulaire" action="controleurs/c_interventions.php?action=ajoutIntervention" method="POST">
    <input type="hidden" name="tabId" id="tabIdInput" value="" />
        <div id="fiche">
            <ul class="lesOnglets">
                <li class="actif onglet" id="onglet1" onclick="javascript:Affiche('1',3);"><?= $titre ?></li>
                <li class="actif onglet" id="onglet2" onclick="javascript:Affiche('2',3);"><?= $titre2 ?></li>
                <!-- <li class="inactif onglet" id="onglet3" onclick="javascript:Affiche('3',3);">onglet 3</li> -->
            </ul>

            <?php /*================================================================================================== nouvelle intervention (1) */ ?>


            <select name="lstCasernes" id="lstCasernes" STYLE="width:350px;">
                <?php 
                    if (!isset($_REQUEST['lstCasernes'])) {$choix = 'premier';} else {$choix =$_REQUEST['lstCasernes'];}

                    $i=1; 
                    //echo"<option>Choisissez une caserne</option>";
                    foreach ($lesCasernes as $uneCaserne)
                    {	
                        if($uneCaserne['cId'] == $choix or $choix == 'premier')
                            
                            {echo "<option selected value=\"".$uneCaserne['cId']."\">".$uneCaserne['cNom']." ".$uneCaserne['cAdresse'].")</option>\n	";
                            $choix = $uneCaserne['cId'];
                            $noA=$i;}
                        else
                            {echo "<option value=\"".$uneCaserne['cId']."\">".$uneCaserne['cNom']." ".$uneCaserne['cAdresse'].")</option>\n		";
                            $i=$i+1;}
                    }	    
                ?>       
                
            </select> <br> <br>

            <?php
            $heure = date('H');
            if ($heure >= 0 && $heure < 6) {
                $tranche_horaire = 1;
            } elseif ($heure >= 6 && $heure < 12) {
                $tranche_horaire = 2;
            } elseif ($heure >= 12 && $heure < 18) {
                $tranche_horaire = 3;
            } else {
                $tranche_horaire = 4;
            }

            ?>
            <input type="text" id="laTranche" name="laTranche" value="<?php echo $tranche_horaire ?>"  style="visibility: hidden;">
            <br><br>

            <label>Lieu : </label>
            <br>
            <textarea name="lieu" id="lieu" cols="90" rows="1" placeholder="Renseigner l'adresse de l'incident" style=resize:none></textarea>
            <br><br>

            <div class="conteneur-flex" id="hautInformations">
                <div class="div-gauche " id="gaucheIntervention">
                    <label>Type d'intervention : </label> <br>
                    <select name="type" id="type">
                    <?php
                    $lesCategorie = $pdo->getCategorie();
                    foreach ($lesCategorie as $categorie) {
                        echo '<optgroup label="' . $categorie['pLibelle'] . '">';
                        $lstType = $pdo->getLstInterventions($categorie['pIndice']);
                        foreach ($lstType as $type) {
                            echo '<option value="' . $type['pIndice'] . '">' . $type['typesInterventions'] . '</option>';
                        }
                        echo '</optgroup>';
                    }?>
                    </select> <br><br>
                    
                    <br>
                    <label>Liste des pompiers : </label> <br>
                    <div id="lstPompier">
                        <div id="pompierDeGarde"></div> <br> <br>
                        <div id="pompierIntervention"></div>
                    </div>
                    <input id="nbPompier" name="nbPompier"  style="visibility: hidden;">

                </div>

                <div class="div-droite" id="droiteHoraires">
                <div id="heureDebut">Heure de début: <?php /*defini le fuseau horaire de Paris*/ date_default_timezone_set('Europe/Paris'); echo date("H:i")?></div>
                    <input type="text" name="plafond" id="plafond" size=28 disabled >
                    <br>
                    <input type="text" name="plancher" id="plancher" size=28 disabled >

                </div>
            </div>

            <div class="conteneur-flex" id="basPompiers">
                <div class="div-gauche" id="gauchePompiers">
                    
                </div>

                <div class="div-droite" id="droiteDescription">
                    <label>Description complémentaire : </label>
                    <br>
                    <textarea name="description" id="description" cols="45" rows="8" style=resize:none></textarea>
                    <div>
                        <button id="boutonAjoutIntervention" type="submit" >Ajouter l'intervention</button>
                    </div>
                </div>
            </div>

            <?php /*================================================================================================== Onglet (2) */ ?>

            <div class='unOnglet' id='contenuOnglet2'>
                <fieldset>
                    <legend>XXXX</legend>
                    <table>
                        <tr>
                            <th style='width:130px;'>.....</th>
                        </tr>
                        <tr>
                            <td>xxxx</td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <?php /*================================================================================================== Onglet 3 */ ?>

            <div style='display: none;' class='unOnglet' id='contenuOnglet3'>
                <fieldset>
                    <legend>XXXX</legend>
                    <table>
                        <tr>
                            <th style='width:130px;'>.....</th>
                        </tr>
                        <tr>
                            <td>xxxx</td>
                        </tr>
                    </table>
                </fieldset>
            </div>
        </div>
    </form>
</body>

</html>
