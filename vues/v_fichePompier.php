<!-- affichage du detail de la fiche pompier / Derniere modification le 18 septembre 2023 par Pascal Blain -->
<!-- ------------------------------------------------ fenetre modale en CSS -->
<style>
    .fModale {
        position: fixed;
        font-family: Arial, Helvetica, sans-serif;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 99999;
        /* display: none; */
        opacity: 0;
        -webkit-transition: opacity 400ms ease-in;
        -moz-transition: opacity 400ms ease-in;
        transition: opacity 400ms ease-in;
        pointer-events: none;
    }

    .fModale:target {
        /* display: block; */
        opacity: 1;
        pointer-events: auto;
    }

    .fModale > div {
        width: 200px;
        position: relative;
        margin: 10% auto;
        padding: 5px 20px 13px 20px;
        border-radius: 10px;
        background: #fff;
        background: -moz-linear-gradient(#fff, #999);
        background: -webkit-linear-gradient(#fff, #999);
        background: -o-linear-gradient(#fff, #999);
    }

    .fermer {
        background: #606061;
        color: #FFFFFF;
        line-height: 25px;
        position: absolute;
        right: -12px;
        text-align: center;
        top: -10px;
        width: 24px;
        text-decoration: none;
        font-weight: bold;
        -webkit-border-radius: 12px;
        -moz-border-radius: 12px;
        border-radius: 12px;
        -moz-box-shadow: 1px 1px 3px #000;
        -webkit-box-shadow: 1px 1px 3px #000;
        box-shadow: 1px 1px 3px #000;
    }

    .fermer:hover {
        background: #00d9ff;
    }
</style>
<?php
/*-----------------------------------------------------------------
Il existe en PHP un opérateur conditionnel dit opérateur ternaire ("?:")
Exemple d'utilisation pour l'opérateur ternaire : Affectation d'une valeur par défaut
<?php
$action = (empty($_POST['action'])) ? 'default' : $_POST['action'];

// La ligne ci-dessus est identique à la condition suivante :
if (empty($_POST['action'])) {
   $action = 'default';
} else {
   $action = $_POST['action'];
}
-----------------------------------------------------------------------*/
$titre1 = ($_SESSION['statut'] == 1) ? "Mes disponibilit&eacute;s" : "Disponibilit&eacute;s";
$titre2 = ($_SESSION['statut'] == 1) ? "Mes gardes" : "Gardes";
$titre3 = ($_SESSION['statut'] == 1) ? "Mon profil" : "Profil";
echo ('
 	<div id="fiche">
		<ul class="lesOnglets">
			<li class="actif onglet" id="onglet1" onclick="javascript:Affiche(\'1\',3);">' . $titre1 . '</li>
			<li class="inactif onglet" id="onglet2" onclick="javascript:Affiche(\'2\',3);">' . $titre2 . '</li>
			<li class="inactif onglet" id="onglet3" onclick="javascript:Affiche(\'3\',3);">' . $titre3 . '</li>
		</ul>');
/*================================================================================================== DISPONIBILITEES (1) */
echo ("
		<div style='display: block;' class='unOnglet' id='contenuOnglet1'>
			<fieldset><legend>X indique une garde</legend>"); ?>
    <!-- div class="boite" style="margin: 0px 10px;" -->

    <form name="frmDispos" action="index.php?choixTraitement=pompiers&action=voir" method="post">
        <input type="hidden" maxlength="2" name="zSemaine" value='<?php echo $semaine; ?>'>
        <input type="hidden" maxlength="2" name="zAnnee" value='<?php echo $annee; ?>'>
    </form>
    <table id="tableau" class="tableau">
        <tbody>
        <tr>
            <th><input id="sPrecedente" name="gauche" title="semaine précédente" src="images/gauche.gif"
                       onclick="autreSemaine('<?php echo date('W', strtotime("-7 days", $premierJour)) . "', '" . date('Y', strtotime("-7 days", $premierJour)) ?>')"
                       onmouseover="document.gauche.src='images/gauche_.gif'"
                       onmouseout="document.gauche.src='images/gauche.gif'" type="image"></th>

            <th colspan="26"><b><big>Semaine <?php echo $semaine . " : du lundi " . date('d/m/Y', $premierJour) . " au dimanche " . date('d/m/Y', strtotime("6 days", $premierJour)) . "</big></b></th>"; ?>

            <th><input id="sSuivante" name="droite" title="semaine suivante" src="images/droite.gif"
                       onclick="autreSemaine('<?php echo date('W', strtotime("+7 day", $premierJour)) . "', '" . date('Y', strtotime("+7 day", $premierJour)); ?>')"
                       onmouseover="document.droite.src='images/droite_.gif'"
                       onmouseout="document.droite.src='images/droite.gif'" type="image"></th>
        </tr>


        <tr>
            <?php
            $nomJour = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
            for ($jour = 0; $jour <= 6; $jour++) {
                echo ('<th colspan="4">' . $nomJour[$jour] . ' ' . date('d/m', strtotime('+' . $jour . ' day', $premierJour)) . '</th>');
            }
            ?>
        </tr>

        <tr>
            <?php
            for ($jour = 0; $jour <= 6; $jour++) {
                for ($tranche = 1; $tranche <= 4; $tranche++) {
                    echo '<td class="semaine" style="text-align : center;">' . $tranche . '</td>';
                }
            }
            ?>
        </tr>
        <tr>
            <?php

            foreach ($lesDispos as $uneLigne) {
                echo '<tr>';
                for ($jour = 0; $jour <= 6; $jour++) {

                    $date = date('Y-m-d H:i:s', strtotime('+' . $jour . ' day', $premierJour));
                    $dateDispo = date('d/m/Y', strtotime('+' . $jour . ' day', $premierJour));
                    $laSemaine = $semaine;
                    for ($tranche = 1; $tranche <= 4; $tranche++) {
                        $dispo = 'd' . $tranche;
                        // Définir la classe en fonction de la disponibilité
                        $class = '';
                        if ($uneLigne[$dateDispo][$dispo] == '0') {
                            $class = 'rouge';
                        } elseif ($uneLigne[$dateDispo][$dispo] == '1') {
                            $class = 'vert';
                        } elseif ($uneLigne[$dateDispo][$dispo] == '2') {
                            $class = 'gray';
                        }
                        echo '<th class="' . $class . '" data-date="' . $date . '" data-tranche="' . $tranche . '" data-dispo="' . $uneLigne[$dateDispo][$dispo] . '" data-laSemaine="' . $laSemaine . '"></th>';
                    }
                }
                echo '</tr>';
            }
            ?>
            </tr>
            <script>

                document.addEventListener("DOMContentLoaded", function() {
                    const cases = document.querySelectorAll(".rouge, .vert, .gray");
                    const semaineActuelle = <?php echo date('W'); ?>;

                    cases.forEach(function(caseCell) {
                        caseCell.addEventListener("click", function() {
                            var dateAvecAnnee = caseCell.getAttribute("data-date"); // Récupère la valeur de l'attribut data-date
                            var tranche = caseCell.getAttribute("data-tranche"); // Récupère la valeur de l'attribut data-tranche
                            var dispo = caseCell.getAttribute("data-dispo"); // Récupère la valeur de l'attribut data-dispo
                            var semaineCellule = caseCell.getAttribute("data-laSemaine");
                            if (parseInt(semaineCellule) > semaineActuelle) {
                                var action;
                                if (dispo === "0") {
                                    dispo = "1";
                                    action = "addActivite";
                                    caseCell.classList.remove("rouge");
                                    caseCell.classList.add("vert");
                                } else if (dispo === "1") {
                                    dispo = "2";
                                    action = "majActivite";
                                    caseCell.classList.remove("vert");
                                    caseCell.classList.add("gray");
                                } else if (dispo === "2") {
                                    dispo = "0";
                                    action = "supActivite";
                                    caseCell.classList.remove("gray");
                                    caseCell.classList.add("rouge");
                                }
                                caseCell.setAttribute("data-dispo", dispo);

                                // Créer une requête AJAX
                                var xhr = new XMLHttpRequest();
                                xhr.open('GET', `index.php?choixTraitement=pompiers&action=${action}&date=${dateAvecAnnee}&tranche=${tranche}&dispo=${dispo}`, true);
                                xhr.onload = function() {
                                    if (this.status == 200) {
                                        console.log('Requête AJAX réussie');
                                    } else {
                                        console.error('Erreur lors de la requête AJAX');
                                    }
                                };
                                xhr.send();
                            } else {
                                alert("Vous ne pouvez pas choisir les disponibilités pour les semaines antérieures.");
                            }
                        });
                    });
                });

            </script>
            </tbody>
    </table>
    <div id="fenetreDispo" class="fModale">
        <div>
            <a href="#" title="Fermer" class="fermer">X</a>
            <h2>ma disponibilit&eacute;<br>pour le <label id="ztJour">&nbsp;</label></h2>
            <form name="frmActivites" action="index.php?choixTraitement=pompiers&action=majActivite" method="post">
                <input type="hidden" name="zSemaine" value='<?php echo $semaine; ?>'>
                <input type="hidden" name="zAnnee" value='<?php echo $annee; ?>'>
                <input type="hidden" name="ztLaDate" value=''>
                <input type="hidden" name="ztLaTranche" value=''>
                <input type="hidden" name="ztExDispo" value=''>
                <br>
                <?php
                $i = 0;
                foreach ($lesTypesDispos as $uneLigne) {
                    if ($uneLigne["pIndice"] <> 3) {
                        echo '<input type="radio" name="brDispo" id="brDispo' . $i . '" value="' . $uneLigne["pIndice"] . '"/><label for="brDispo' . $i . '">' . $uneLigne["pLibelle"] . '</label><br>';
                    }
                    $i++;
                }
                ?>
                <br>
                <button onclick="document.forms['frmActivites'].submit()">Valider</button>
                <button href="#contenuOnglet1">Annuler</button>
            </form>
        </div>
    </div>
    <!-- /div -->
    <?php
    echo ("
			</fieldset>
		</div>");
/*================================================================================================== GARDES (2) */
echo ("
		<div style='display: none;' class='unOnglet' id='contenuOnglet2'>
		<fieldset><legend>Gardes r&eacute;alis&eacute;es ");
if (count($lesGardes) == 0) {
    echo "<i>(aucune garde enregistrée)</i></legend>";
} else {
    echo ("<i>(" . count($lesGardes) . " gardes)</i></legend>
			<table style='border: 0px solid white;'>
				<tr><th class='controleLong'>Date de la garde</th>");
    foreach ($lesTranches as $uneLigne) {
        echo ("<th>" . $uneLigne['pLibelle'] . "</th>");
    }
    $dateGarde = "premiere";
    $colonne = 1;
    echo "</tr>";
    foreach ($lesGardes as $uneLigne) {
        if ($dateGarde != $uneLigne['wDate']) {
            if ($dateGarde != "premiere") {
                while ($colonne <= count($lesTranches)) {
                    echo "<td class='controle' style='text-align : center;'>&nbsp;</td>";
                    $colonne++;
                }
                echo "</tr>
			";
            }
            echo "<tr><td class='controle' style='text-align : center;'>" . $uneLigne['wDate'] . "</td>";
            $dateGarde = $uneLigne['wDate'];
            $colonne = 1;
        }
        while ($colonne < $uneLigne['aTranche']) {
            echo "<td class='controle' style='text-align : center;'>&nbsp;</td>";
            $colonne++;
        }
        echo ("<td class='controle' style='text-align : center;background-color : lime;'>&nbsp;</td>");
        $colonne = $uneLigne['aTranche'] + 1;
    }
    while ($colonne <= count($lesTranches)) {
        echo "<td class='controle' style='text-align : center;'>&nbsp;</td>";
        $colonne++;
    }
    echo "</tr>";
    echo ("</table>");
}
echo ("
		</fieldset>
		</div>");
/*================================================================================================== COORDONNEES (3) */
echo ("
<div style='display: none;' class='unOnglet' id='contenuOnglet3'>
    <fieldset><legend>Coordonnées</legend>
        <label>Nom: </label><input type='text' name='nom' value='" . $lesInfosPompier['nom'] . "'readonly><br>
        <label>Prénom: </label><input type='text' name='prenom' value='" . $lesInfosPompier['prenom'] . "' readonly><br>
        <label>Adresse: </label><input type='text' name='adresse' value='" . $lesInfosPompier['pAdresse'] . "' readonly><br>
        <label>Code postal: </label><input type='text' name='cp' value='" . $lesInfosPompier['pCp'] . "' readonly><br>
        <label>Ville: </label><input type='text' name='ville' value='" . $lesInfosPompier['pVille'] . "' readonly><br>
        <label>Numéro BIP: </label><input type='text' name='numBip' value='" . $lesInfosPompier['pBip'] . "' readonly><br>
        <label>Adresse électronique: </label><input type='text' name='email' value='" . $lesInfosPompier['pMail'] . "' readonly><br>
        
    </fieldset>
    <fieldset><legend>Centre d'Incendie et de Secours</legend>
        <label>Code: </label><input type='text' name='pCis' value='" . $lesInfosPompier['pCis'] . "' readonly><br>
        <label>Nom: </label><input type='text' name='cNom' value='" . $lesInfosPompier['cNom'] . "' readonly><br>
        <label>Adresse: </label><input type='text' name='cAdresse' value='" . $lesInfosPompier['cAdresse'] . "' readonly><br>
        <label>Téléphone: </label><input type='text' name='cTel' value='" . $lesInfosPompier['cTel'] . "' readonly><br>
        <label>Groupement: </label><input type='text' name='cGroupement' value='" . $lesInfosPompier['cGroupement'] . "' readonly><br>
    </fieldset>
    <fieldset><legend>Fonction</legend>
        <label>Type: </label><input type='text' name='wType' value='" . $lesInfosPompier['wType'] . "' readonly><br>
        <label>Grade: </label><input type='text' name='wGrade' value='" . $lesInfosPompier['wGrade'] . "' readonly><br>
        <label>Statut: </label><input type='text' name='wStatut' value='" . $lesInfosPompier['wStatut'] . "' readonly><br>
    </fieldset>
    
</div>
</div>
");

/*================================================================================================== Onglet X */
echo ("
		<div style='display: none;' class='unOnglet' id='contenuOngletX'>
			<fieldset><legend>XXXX</legend>
			<table>
				<tr><th style='width:130px;'>.....</th></tr>
				<tr><td>xxxx</td></tr>
			</table>
			</fieldset>
		</div>

	</div>
</div>");				
?>