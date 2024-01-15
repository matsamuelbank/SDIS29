<!-- v_unPompier.php / Derniere modification le 18 septembre 2023 par Pascal Blain -->
<div id="contenu">
<?php
 	if ($_REQUEST['action']=="supprimer") 
		{ 	echo '<h2>SUPPRESSION DU POMPIER '.$lesInfosPompier['nom'].' '.$lesInfosPompier['prenom'].'</h2>';
		 	echo '<form name="frmA" 	action="index.php?choixTraitement=pompiers&action=validerSupprimer&type='.$lesInfosPompier['pType'].'&agent='.$lesInfosPompier['id'].'&caserne='.$lesInfosPompier['pCis'].'" method="post">';} 
 	if ($_REQUEST['action']=="modifier") 
		{ 	echo '<h2>MODIFICATION DU POMPIER '.$lesInfosPompier['nom'].' '.$lesInfosPompier['prenom'].'</h2>'; 	
			echo '<form name="frmA" 	action="index.php?choixTraitement=pompiers&action=validerModifier&type='.$lesInfosPompier['pType'].'&agent='.$lesInfosPompier['id'].'&caserne='.$lesInfosPompier['pCis'].'" method="post">';
			
		}
 	if ($_REQUEST['action']=="ajouter") 
		{ 	echo "<h2>AJOUT D'UN NOUVEAU POMPIER</h2>";
			echo '
			<form name="frmA" 	action="index.php?choixTraitement=pompiers&action=validerAjouter&type='.$lesInfosPompier['pType'].'" method="post" onsubmit="return valider(this)">';}
	echo ("	
    <table style='border: 0px solid white;'>
	<tr>
	<td style='border :0px;'>
	<fieldset><legend>Coordonn&eacute;es</legend>
		<table>");	

 if ($_REQUEST['action']=="supprimer")  //-------------------------------------------------------- cas suppression
 {	echo ("	
	 	<div style='display: none;' class='unOnglet' id='contenuOnglet3'> 
 			<table style='border: 0px solid white;'>
			<tr>
				
			</tr>
			</table>
			");	
}
//------------------------------------------------------------------------------------ cas ajout ou modification


$titre = ""; // Initialisez $titre à une chaîne vide

if ($_REQUEST['action'] == "modifier") {
    $statut = $pdo->getlstStatut();
    $type = $pdo->getlstTypePompier();
    $grade = $pdo->getlstGrade();

    if ($_SESSION['statut'] == 1) {
        // Code HTML pour le statut 1
        $titre = "<fieldset><legend>Coordonnées</legend>
		<label>Nom: </label><input type='text' name='nom' value='".$lesInfosPompier['nom']."'><br>
		<label>Prénom: </label><input type='text' name='prenom' value='".$lesInfosPompier['prenom']."' ><br>
		<label>Adresse: </label><input type='text' name='adresse' value='".$lesInfosPompier['pAdresse']."' ><br>
		<label>Code postal: </label><input type='text' name='cp' value='".$lesInfosPompier['pCp']."' ><br>
		<label>Ville: </label><input type='text' name='ville' value='".$lesInfosPompier['pVille']."' ><br>
		<label>Numéro BIP: </label><input type='text' name='numBip' value='".$lesInfosPompier['pBip']."' ><br>
		<label>Adresse électronique: </label><input type='text' name='email' value='".$lesInfosPompier['pMail']."' ><br>
		<label>Nom de compte: </label><input type='text' name='login' value='".$lesInfosPompier['pLogin']."' ><br>
		<label>Mot de passe : </label><br>
	</fieldset>

	<fieldset><legend>Centre d'Incendie et de Secours</legend>
		<label>Code: </label><input type='text' name='pCis' value='".$lesInfosPompier['pCis']."' readonly><br>
		<label>Nom: </label><input type='text' name='cNom' value='".$lesInfosPompier['cNom']."' readonly><br>
		<label>Adresse: </label><input type='text' name='cAdresse' value='".$lesInfosPompier['cAdresse']."' readonly><br>
		<label>Téléphone: </label><input type='text' name='cTel' value='".$lesInfosPompier['cTel']."' readonly><br>
		<label>Groupement: </label><input type='text' name='cGroupement' value='".$lesInfosPompier['cGroupement']."' readonly><br>
	</fieldset>

	<fieldset><legend>Fonction</legend>
			<label>Le type de pompier</label>
			
			<select name='typePer' disabled>";
			foreach($type as $lstTypePer)
			{
			$titre .= "<option value='".$lstTypePer['pIndice']."'>".$lstTypePer['pLibelle']."</option>";
			}
			$titre .= "</select>

			<label>Le Grade</label>
			<select name='grade' disabled>";
			foreach($grade as $lstGrade)
			{
			$titre .= "<option value='".$lstGrade['pIndice']."'>".$lstGrade['pLibelle']."</option>";
			}
			$titre .= "</select>
			<label>Le statut</label>
			
			<select name='statut' disabled>";
			foreach($statut as $lstStatut)
			{
			$titre .= "<option value='".$lstStatut['pIndice']."'>".$lstStatut['pLibelle']."</option>";
			}
			$titre .= "</select>
			
	</fieldset>";
    } else {
        // Code HTML pour d'autres statuts
        $titre = "<fieldset><legend>Coordonnées</legend>
			<label>Nom: </label><input type='text' name='nom' value='".$lesInfosPompier['nom']."'><br>
			<label>Prénom: </label><input type='text' name='prenom' value='".$lesInfosPompier['prenom']."' ><br>
			<label>Adresse: </label><input type='text' name='adresse' value='".$lesInfosPompier['pAdresse']."' ><br>
			<label>Code postal: </label><input type='text' name='cp' value='".$lesInfosPompier['pCp']."' ><br>
			<label>Ville: </label><input type='text' name='ville' value='".$lesInfosPompier['pVille']."' ><br>
			<label>Numéro BIP: </label><input type='text' name='numBip' value='".$lesInfosPompier['pBip']."' ><br>
			<label>Adresse électronique: </label><input type='text' name='email' value='".$lesInfosPompier['pMail']."' ><br>
			<label>Nom de compte: </label><input type='text' name='login' value='".$lesInfosPompier['pLogin']."' ><br>
			<label>Mot de passe : </label><br>
		</fieldset>

		<fieldset><legend>Centre d'Incendie et de Secours</legend>
				<label>Code: </label><input type='text' name='pCis' value='".$lesInfosPompier['pCis']."' ><br>
				<label>Nom: </label><input type='text' name='cNom' value='".$lesInfosPompier['cNom']."' ><br>
				<label>Adresse: </label><input type='text' name='cAdresse' value='".$lesInfosPompier['cAdresse']."' ><br>
				<label>Téléphone: </label><input type='text' name='cTel' value='".$lesInfosPompier['cTel']."' ><br>
				<label>Groupement: </label><input type='text' name='cGroupement' value='".$lesInfosPompier['cGroupement']."' ><br>
		</fieldset>

		<fieldset><legend>Fonction</legend>
			<label>Le type de pompier</label>
			
			<select name='typePer'>";
			foreach($type as $lstTypePer)
			{
			$titre .= "<option value='".$lstTypePer['pIndice']."' >".$lstTypePer['pLibelle']."</option>";
			}
			$titre .= "</select>

			<label>Le Grade</label>
			<select name='grade' >";
			foreach($grade as $lstGrade)
			{
			$titre .= "<option value='".$lstGrade['pIndice']."'>".$lstGrade['pLibelle']."</option>";
			}
			$titre .= "</select>
			<label>Le statut</label>
			
			<select name='statut'>";
			foreach($statut as $lstStatut)
			{
			$titre .= "<option value='".$lstStatut['pIndice']."'>".$lstStatut['pLibelle']."</option>";
			}
			$titre .= "</select>
		</fieldset>";	
    }
    echo ($titre);
}




if ($_REQUEST['action']=="ajouter")
{
	echo ("	<tr><th style='width:130px;'>Nom</th>		<td style='width:130px;'><input id='ztNom' type='text' name='ztNom'></td> </tr>
			
			<br />");
}		
?>         
            <table style='border: 0px solid white; '>
            	<tr>
                <td style='border: 0px solid white;'>
                	<fieldset><legend>Observations</legend>
                 	<textarea name='ztObs' cols='70' rows='1'></textarea>					
					</fieldset>					
				</td>
				<td>
				<input type="image" 	name="btValider" 	alt="Valider" 	src="images/valider.jpg" value="OK" >
				<input type="image"		name="btAnnuler" 	alt="Annuler" 	src="images/annuler.jpg" value="nonOK" 	onclick="annuler('frmA');">
				</td>
                <td style='border: 0px solid white; witdh:130px; text-align:right;'>
                	<!-- <input type="hidden" 	name="zTypeAdm"		value="<?php if ($type=="adm") {echo ("true");} else {echo ("false");} ?>"> -->
                	<input type="hidden" 	name="zOk"			value="OK"> 

                </td>
                </tr>
			</table>   
    </form>
