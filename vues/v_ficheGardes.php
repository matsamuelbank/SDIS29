affichage de la feuille de gardes / Derniere modification le 18/09/2023 à 16h50 par Pascal Blain
<div style='display: block;' class='unOnglet' id='contenuOnglet1'>
	<fieldset><legend>Feuille de gardes</legend>
	<form name="frmDispos" action="index.php?choixTraitement=gardes&action=voir" method="post">	
		<input type="hidden" name="zSemaine"	value='<?php echo $semaine;?>'>
		<input type="hidden" name="zAnnee" 		value='<?php echo $annee;?>'>
		<input type="hidden" name="ztLaDate"	value="">
		<input type="hidden" name="ztLaTranche" value="">
		<input type="hidden" name="ztExGarde" 	value="">
		<input type="hidden" name="ztPompier" 	value="">		
	</form>			
	<table id="tableau" class="tableau">
		<tbody>
		<tr>
			<th><input id="sPrecedente" name="gauche" title="semaine précédente" src="images/gauche.gif" onclick="autreSemaine('<?php echo date('W',strtotime("-7 days",$premierJour))."', '".date('Y',strtotime("-7 days",$premierJour))?>')" onmouseover="document.gauche.src='images/gauche_.gif'" onmouseout="document.gauche.src='images/gauche.gif'"type="image"></th>

			<th colspan="27"><b><big>Semaine <?php echo $semaine." : du lundi ".date('d/m/Y',$premierJour)." au dimanche ".date('d/m/Y',strtotime("6 days",$premierJour))."</big></b></th>";?>

			<th><input id="sSuivante" name="droite" title="semaine suivante" src="images/droite.gif" onclick="autreSemaine('<?php echo date('W',strtotime("+7 day",$premierJour))."', '".date('Y',strtotime("+7 day",$premierJour));?>')" onmouseover="document.droite.src='images/droite_.gif'" onmouseout="document.droite.src='images/droite.gif'"type="image"></th>
		</tr>
		<tr><th>&nbsp;</th>
		<?php
		$nomJour = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
		for($jour=0; $jour<= 6; $jour++)
		{
		echo ('<th colspan="4">'.$nomJour[$jour].' '.date('d/m',strtotime('+'.$jour.' day',$premierJour)).'</th>');
		}		
		
		echo "</tr>".PHP_EOL."		<tr><th width='100'>Volontaires</th>";
		for($jour=0; $jour<= 6; $jour++) 
		{
			foreach ($lesTranches as $uneTranche)
			{echo '<th class="semaine" style="text-align : center;">'.$uneTranche["pIndice"].'</th>';} 
		}
		echo PHP_EOL."		</tr>";			
	
		foreach ($lesPompiers as $unPompier)
        {
            echo "<tr><td><small><small>".$unPompier['pNom']." ".$unPompier['pPrenom']."</small></small></td>";
            for($jour=0; $jour<= 6; $jour++) 
            {
                $date = date('Y-m-d', strtotime('+' . $jour . ' day', $premierJour));
                $dateDispo = date('d/m/Y', strtotime('+' . $jour . ' day', $premierJour));
                $idPompier=$unPompier["pId"];
                $cisPompier=$unPompier["pCis"];

                for ($tranche = 1; $tranche <= 4; $tranche++) {

                    //$dispo = 'd' . $tranche;
                        // Définir la classe en fonction de la disponibilité
                    $dispo = $lesDispos[$idPompier][$dateDispo]["d".$tranche];

                        $class = '';
                        if ($dispo=='0') {
                            $class = 'rouge';
                        } elseif ($dispo== '1') {
                            $class = 'vert';
                        } elseif ($dispo == '2') {
                            $class = 'gray';
                        }

                    $garde = $lesDispos[$idPompier][$dateDispo]["g".$tranche];
                    if($garde == null)
                    {
                        $garde=0;
                    }
                    // Vérification pour mettre un "X" dans la case si $garde est égal à 1
                    $cellContent = ($garde == 0) ? '' : 'X';

                    echo '<th class="' . $class . '" style="text-align : center;" data-cisPompier="' . $cisPompier . '" 
                                                                         data-idPompier="' . $idPompier . '" 
                                                                         data-dispo="' . $dispo . '" 
                                                                         data-garde="' . $garde . '" 
                                                                         data-date="' . $date . '" 
                                                                         data-tranche="' . $tranche . '"
                                                                         >'.$cellContent.'</th>';
                }
            }
        }
		?>
		<script>

                                
                document.addEventListener("DOMContentLoaded", function() {
                    const cases = document.querySelectorAll(".rouge, .vert, .gray");

                    cases.forEach(function(caseCell) {
                        caseCell.addEventListener("click", function() {
                            var dateAvecAnnee = caseCell.getAttribute("data-date");
                            var tranche = caseCell.getAttribute("data-tranche");
                            var dispo = caseCell.getAttribute("data-dispo");
                            var garde = caseCell.getAttribute("data-garde");
                            var id = caseCell.getAttribute("data-idPompier");

                            if (dispo == "1" && garde == "0") {
                                garde = 1;
                                caseCell.innerHTML = "X";
                            } else if (dispo == "1" && garde == "1") {
                                garde = 0;
                                caseCell.innerHTML = "";
                            }

                            if(dispo == "2" && garde == "0") {
                                garde = 1;
                                caseCell.innerHTML = "X";
                            } else if (dispo == "2" && garde == "1") {
                                garde = 0;
                                caseCell.innerHTML = "";
                            }

                            if(dispo == "0") {
                                garde = 0;
                                caseCell.innerHTML = "";
                            }

                            caseCell.setAttribute("data-dispo", dispo);
                            caseCell.setAttribute("data-garde", garde);

                            // requête AJAX
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', `index.php?choixTraitement=gardes&action=majGarde&date=${dateAvecAnnee}&tranche=${tranche}&dispo=${dispo}&valeurGarde=${garde}&id=${id}`, true);
                            xhr.onload = function() {
                                if (this.status == 200) {
                                    console.log('Requête AJAX réussie');
                                } else{
                                    console.error('Erreur lors de la requête AJAX');
                                }
                            };
                            xhr.send();
                        });
                    });
                });

//                 document.addEventListener("DOMContentLoaded", function() {
//     const cases = document.querySelectorAll(".rouge, .vert, .gray");

//     cases.forEach(function(caseCell) {
//         caseCell.addEventListener("click", function() {
//             var dateAvecAnnee = caseCell.getAttribute("data-date");
//             var tranche = caseCell.getAttribute("data-tranche");
//             var dispo = caseCell.getAttribute("data-dispo");
//             var garde = caseCell.getAttribute("data-garde");
//             var id = caseCell.getAttribute("data-idPompier");

//             if (dispo == "1" && garde == "0") {
//                 garde = 1;
//                 caseCell.innerHTML = "X";
//             } else if (dispo == "1" && garde == "1") {
//                 garde = 0;
//                 caseCell.innerHTML = "";
//             }

//             if(dispo == "2" && garde == "0") {
//                 garde = 1;
//                 caseCell.innerHTML = "X";
//             } else if (dispo == "2" && garde == "1") {
//                 garde = 0;
//                 caseCell.innerHTML = "";
//             }

//             if(dispo == "0") {
//                 garde = 0;
//                 caseCell.innerHTML = "";
//             }

//             /*caseCell.setAttribute("data-dispo", dispo);
//             caseCell.setAttribute("data-garde", garde);*/

//             // Redirect to the new URL
//             window.location.href = `index.php?choixTraitement=gardes&action=majGarde&date=${dateAvecAnnee}&tranche=${tranche}&dispo=${dispo}&valeurGarde=${garde}&id=${id}`;
//         });
//     });
// });


            </script>
		</tr>
		</tbody>
	</table>
	</fieldset>
</div>