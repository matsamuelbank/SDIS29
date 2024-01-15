<!-- affichage du détail d'une statistique / Derniere modification le 20 novembre 2013 par Pascal BLAIN -->
<?php
echo(' 	<div id="fiche">	
		<div>');	
echo ("<fieldset><legend>".$titre1."</legend>
				<h2> ".$titre2."</h2><p><i>(".count($lesStatistiques)." usagers ");
if($colonne=='B') {echo ("d&eacute;j&agrave; pr&eacute;sents durant la p&eacute;riode pr&eacute;c&eacute;dente");}					
if($colonne=='D') {echo ("arriv&eacute;s en cours de p&eacute;riode");}
if($colonne=='F') {echo ("sortis en cours de p&eacute;riode");}
if($colonne=='S') {echo ("en cours d'instruction ou suivis");}
if($colonne=='C') {echo ("sortis ou r&eacute;orient&eacute;s");}
echo (")</i></p>
				<table>
				<tr><th class='controleLong'>Nom de l'usager</th><th>Genre</th><th class='controleLong'>Ville</th><th>n&eacute;(e) le</th><th>No CAF</th>
				<th>Entr&eacute;e le</th><th>Sortie le</th></tr>");	
		foreach ($lesStatistiques as $uneLigne)		
		{ 	if($uneLigne['uHomme']==1) {$sexe="M";} else {$sexe="F";} 
echo("		<tr><td class='controleLong'><a href='index.php?uc=usagers&action=voir&lstUsagers=".$uneLigne['uId']."' style='text-decoration:none;'>".$uneLigne['uNom']." ".$uneLigne['uPrenom']."</a></td>
						<td style='text-align : center;'>".$sexe."</td>
						<td class='controleLong'>".$uneLigne['uCp']." ".$uneLigne['uVille']."</td>
						<td class='controle' style='text-align : center;'>".$uneLigne['uDateNaissance']."</td>
						<td class='controle' style='text-align : center;'>".$uneLigne['uNumCaf']."</td>
						<td class='controle' style='text-align : center;'>".$uneLigne['wEntree']."</td>
						<td class='controle' style='text-align : center;'>".$uneLigne['wSortie']."</td>
				</tr>");
		}
echo ("		</table>
			</fieldset>		
		</div>
	</div>");				
?>