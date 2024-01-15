<?php
// ****************************************'
//  Le CASTEL-BTS SIO/ PROJET SDIS29       '
//  Programme: c_pompiers.php              '
//  Objet    : gestion des pompiers        '
//  Client   : Bts SIO2                    '
//  Version  : 2023.0                      '
//  Date     : 18/09/2023 à 16h50          '
//  Auteur   : pascal.blain@ac-dijon.fr    '
//*****************************************'
$action = $_REQUEST['action'];
switch($action) {
case 'voir':
	{
		$formulaire		="choixP";
		$champ			="lstPompiers";	
		include("vues/v_entete.php");
		$lesLignes		=$pdo->getLesPompiers($_SESSION['cis']);
		include("vues/v_choixPompier.php");
		$lesInfosPompier = $pdo->getInfosPompier("*",$choix);
		/*$leGrade = $pdo->getGrade("*", $choix);*/
		$lesTranches	= $pdo->getParametre("tranche");	
		$lesGardes		= $pdo->getInfosGardes($choix);
		if(!isset($_REQUEST['zSemaine'])){$_REQUEST['zSemaine'] = date('W');}
		$semaine 		= $_REQUEST['zSemaine'];
		if(!isset($_REQUEST['zAnnee'])){$_REQUEST['zAnnee'] = date('Y');}
		$annee 			= (int)$_REQUEST['zAnnee'];
		$lesDispos		= $pdo->getDisposHebdo($choix, $semaine, $annee);
		//echo "annee:".$annee."pompier: ".$choix."semaine : ".$semaine;
		$premierJour 	= strtotime("+$semaine weeks",mktime(0,0,0,1,1,$annee));
		if (date('w',$premierJour) != 1){$premierJour = strtotime("last monday",$premierJour);}
		$lesTypesDispos	= $pdo->getParametre("dispo");
		include("vues/v_fichePompier.php");
		break;
	}
//----------------------------------------- FORMULAIRE DE SAISIE
case 'ajouter':
case 'modifier':
case 'supprimer':
	{ 
		$formulaire		="frmA";
		$champ			="ztNom";	
		include("vues/v_entete.php");
		$choix= $_REQUEST['lstPompiers'];
		$lesInfosPompier = $pdo->getInfosPompier("*",$choix);
		$lesTypes 		= $pdo->getParametre("typePer");	
		$lesGrades 		= $pdo->getParametre("grade");
		$lesStatuts		= $pdo->getParametre("statAgt");
		include("vues/v_unPompier.php");
		break;
	}
//----------------------------------------- VALIDATION	
case 'validerAjouter':
case 'validerModifier':
	{
		//session_start();
		//include("include/class.pdo.php");
		//include('../include/class.pdo.php');
		//$pdo = PdoBD::getPdoBD();

		/*info util */
		$idUtilisateur = $_SESSION['idUtilisateur'];
		$nom =  $_POST['nom'];
		$prenom = $_POST['prenom'];
		$adresse = $_POST['adresse'];
		$cp = $_POST['cp'];
		$ville = $_POST['ville'];
		$numBip = $_POST['numBip'];
		$email = $_POST['email'];
		$login = $_POST['login'];

		/*info caserne*/

		$pCis = $_POST['pCis'];
		$caNom = $_POST['cNom'];
		$caAdresse = $_POST['cAdresse'];
		$caTel = $_POST['cTel'];
		$caGroupement = $_POST['cGroupement'];

		
		if (isset($_POST['statut']) && isset($_POST['grade']) && isset($_POST['typePer'])) {
			// Si les clés statut, grade et typePer existent, exécutez la requête majInfosPompier
			try {$pStatut = $_POST['statut'];
				$pGrade = $_POST['grade'];
				$pType = $_POST['typePer'];
				$pCommentaire = $_POST['ztObs'];

				$res =  $pdo->majInfosPompier($idUtilisateur, $nom, $prenom, $adresse, $cp, $ville, $numBip, $email, $login, $pCis, $caNom, $caAdresse, $caTel, $caGroupement, $_POST['statut'], $_POST['grade'], $_POST['typePer'], $_POST['ztObs']);
				var_dump($res);
			} catch (PDOException $e) {
				echo "Une erreur s'est produite (maj) : " . $e->getMessage();
			}
		} else {
			// Sinon, exécutez la requête majInfosPompierSimple
			try {
				$res =  $pdo->majInfosPompierSimple($idUtilisateur, $nom, $prenom, $adresse, $cp, $ville, $numBip, $email, $login, $pCis, $caNom, $caAdresse, $caTel, $caGroupement, $_POST['ztObs']);
				var_dump($res);
			} catch (PDOException $e) {
				echo "Une erreur s'est produite (maj) : " . $e->getMessage();
			}
		}
		header ('location: index.php?choixTraitement=pompiers&action=voir');
		break;
	}
		
case 'validerSupprimer':
	{
		$valeur	= $_REQUEST['agent'];		
		if ($_REQUEST['zOk']=="OK") 
		{
			if ($action==="validerSupprimer") {$pdo->supprimePompier($valeur);}
			else
				{
				$nom		= addslashes ($_REQUEST['ztNom']);
				$prenom		= addslashes ($_REQUEST['ztPrenom']);
				$type		= $_REQUEST['lstType'];
				$grade		= $_REQUEST['lstGrade'];
				$statut		= $_REQUEST['lstStatut'];
				$cis        = $_REQUEST['zCis'];
				$mail		= $_REQUEST['ztMail'];
				$login		= $_REQUEST['ztLogin'];
				$mdp		= md5($_REQUEST['ztMdp']);	if($_REQUEST['brMdp']==0 AND $action==="validerModifier") {$mdp="*";}
				$adresse	= addslashes ($_REQUEST['ztAdresse']);
				if (strlen($_REQUEST['ztCP'])>1)				{$cp	= $_REQUEST['ztCP'];} else {$cp = "Null";}
				$ville			= addslashes ($_REQUEST['ztVille']);
				if (strlen($_REQUEST['ztTel'])>1) 			{$tel	= str_replace(" ", "", $_REQUEST['ztTel']); $tel=str_replace(".", "", $tel);	$tel=str_replace("/", "", $tel);} else {$tel="Null";}
				$commentaire	= addslashes ($_REQUEST['ztObs']);
				if ($action==="validerAjouter") 
					{$pdo->ajoutPompier($cis, $valeur,$nom,$prenom,$statut,$mail,$login,$mdp,$grade,$type,$adresse,$cp,$ville,$tel,$commentaire);
					$sujet 	= "nouveau compte";
					$msg = "Bonjour ".$prenom." ".$nom.", \r\nLe Castel vient de créer un compte pour vous  ...\r\n";
					}
				else 
					{$pdo->majPompier($cis, $valeur,$nom,$prenom,$statut,$mail,$login,$mdp,$grade,$type,$adresse,$cp,$ville,$tel,$commentaire);
					$sujet 	= "nouveau mot de passe";
					$msg = "Bonjour ".$prenom." ".$nom.", \r\nLe Castel vient de modifier votre mot de passe  ...\r\n";
					}		
				$entete	= "From: Pascal Blain <pascal-blain@wanadoo.fr>\r\n";
				$entete	.= "Mime-Version: 1.0\r\n";
				$entete	.= "Content-type: text/html; charset=utf-8\r\n";
				$entete	.= "\r\n";
				$msg .= "Statut : ".$statut."\r\n";
				$msg .= "Identifiant : ".$login."\r\n";
				$msg .= "Mot de passe : ".$_REQUEST['ztMdp']."\r\n";
				//$pdo->envoyerMail($mail, $sujet, $msg, $entete);
				}
		}
		header ('location: index.php?choixTraitement=pompiers&action=voir&lstPompiers='.$valeur);
		break;
	}

//----------------------------------------- 
case 'majActivite':
{
	$pCis = $_SESSION['cis'];
	$pId = $_SESSION['idUtilisateur'];
	$date = $_GET["date"];
	$tranche = $_GET["tranche"];
	$dispo= $_GET["dispo"];
	
	$pdo->majActivite($pCis, $pId, $date,$tranche, $dispo);
	header ('location: index.php?choixTraitement=pompiers&action=voir');
	break;
}
case 'supActivite':
{
	$pCis = $_SESSION['cis'];
	$pId = $_SESSION['idUtilisateur'];
	$date = $_GET["date"];
	$tranche = $_GET["tranche"];
	$dispo= $_GET["dispo"];
	
	$pdo->supActivite($pCis, $pId, $date,$tranche);
	header ('location: index.php?choixTraitement=pompiers&action=voir');
	break;
}
case 'addActivite':
{
	$pCis = $_SESSION['cis'];
	$pId = $_SESSION['idUtilisateur'];
	$date = $_GET["date"];
	$tranche = $_GET["tranche"];
	$dispo= $_GET["dispo"];
	
	$pdo->addActivite($pCis, $pId, $date,$tranche, $dispo);
	header ('location: index.php?choixTraitement=pompiers&action=voir');
	break;
}
	
//----------------------------------------- 
default :
	{
		echo 'erreur d\'aiguillage !'.$action;
		break;
	}
}
?>
