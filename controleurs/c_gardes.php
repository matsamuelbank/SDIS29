<?php
// ****************************************'
//  Le CASTEL-BTS SIO/ PROJET SDIS29       '
//  Programme: c_gardes.php                '
//  Objet    : gestion des gardes          '
//  Client   : Bts SIO2                    '
//  Version  : 2023                        '
//  Date     : 18/09/2023 à 23h50          '
//  Auteur   : pascal-blain@wanadoo.fr     '
//*****************************************'
$action = $_REQUEST['action'];
switch($action) 
{
case 'voir':
	{
		include("vues/v_entete.php");
		if(!isset($_REQUEST['zSemaine'])){$_REQUEST['zSemaine'] = date('W');}
		$semaine 		= $_REQUEST['zSemaine'];
		if(!isset($_REQUEST['zAnnee'])){$_REQUEST['zAnnee'] = date('Y');}
		$annee 			= $_REQUEST['zAnnee'];
		$premierJour 	= strtotime("+$semaine weeks",mktime(0,0,0,1,1,$annee));
		if (date('w',$premierJour) != 1){$premierJour = strtotime("last monday",$premierJour);}
		$lesTranches	= $pdo->getParametre("tranche");	
		$lesTypesDispos	= $pdo->getParametre("dispo");
		//var_dump($lesPompiers	= $pdo->getLesPompiers($_SESSION['cis']));
		$lesPompiers	= $pdo->getLesPompiers($_SESSION['cis']);
		$lesDispos		= $pdo->getDisposHebdo("*", $semaine, $annee);
		include("vues/v_ficheGardes.php");
		break;
	}
//----------------------------------------- 
case 'majGarde':
	{	//$Garde = ($_REQUEST["ztExGarde"]=="0") ? 1 : 0;
		//break;
		$pCis = $_SESSION['cis'];
		// il faut reecuperer l'id du pompier choisie et non celui de la session 
		//$pId = $_SESSION['idUtilisateur']; pas ceci 
		$date = $_GET["date"];
		$tranche = $_GET["tranche"];
		//$dispo= $_GET["dispo"];
		$valeurGarde = $_GET["valeurGarde"];
		$id = $_GET["id"];
		$res= $pdo->majGarde($pCis, $id, $date, $tranche,$valeurGarde);
		//echo var_dump($res);
		header ('location: index.php?choixTraitement=gardes&action=voir');
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
