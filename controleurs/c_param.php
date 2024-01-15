<?php
// ****************************************'
//  Le CASTEL-BTS SIO/ PROJET SDIS29       '
//  Programme: c_param.php                 '
//  Objet    : controleur parametres       '
//  Client   : Bts SIO2                    '
//  Version  : 2023                        '
//  Date     : 18/09/2023 à 16h00          '
//  Auteur   : pascal-blain@wanadoo.fr     '
//*****************************************'

if(!isset($_REQUEST['type'])){$_REQUEST['type'] = '';}
$type 		= $_REQUEST['type'];
if(!isset($_REQUEST['valeur'])){$_REQUEST['valeur'] = '';}
$valeur 	= $_REQUEST['valeur'];
if(!isset($_REQUEST['zPlancher'])){$_REQUEST['zPlancher'] = '';}
$plancher	= intval($_REQUEST['zPlancher']);
if(!isset($_REQUEST['zPlafond'])){$_REQUEST['zPlafond'] = '';}
$plafond	= intval($_REQUEST['zPlafond']);
if(!isset($_REQUEST['zType'])){$_REQUEST['zType'] = '';}
$zType		= $_REQUEST['zType'];
if(!isset($_REQUEST['zIndice'])){$_REQUEST['zIndice'] = '';}
$indice 	= $_REQUEST['zIndice'];
$action 	= $_REQUEST['action'];
switch($action) {
case 'voir':
	{
		include("vues/v_entete.php");
		$lesParametres=$pdo->getLesParametres();
		include("vues/v_choixParam.php");
		$enteteParametre=$lesParametres[$noP-1];
		$lesInfosParametre = $pdo->getParametre($choix,"*");
		include("vues/v_ficheParametre.php");
		$stat="2";
		break;
	}
//-----------------------------------------liste détaillée pour un parametre
case 'liste':
	{
		include("vues/v_entete.php");
		$lesParametres=$pdo->getLesParametres();
		include("vues/v_choixParam.php");

		$lesStatistiques = $pdo->getParametre($choix, $indice);
		$titre2=$lesStatistiques[0]['libelle'];
		include("vues/v_listeStat.php");
		break;
	}
//----------------------------------------- AJOUT
case 'ajouter':
case 'modifier':
case 'supprimer':
	{ 
		include("vues/v_entete.php");
		$infosParam = $pdo->getInfosParam($type, $valeur);
		include("vues/v_unParam.php");
		break;
	}
//----------------------------------------- VALIDATIONS	
case 'validerAjouter':
	{// enregistrement de la ligne et retour
		if ($_REQUEST['zOk']=="OK") {$pdo->ajoutParametre($type, $valeur, addslashes ($_REQUEST['zLibelle']), $plancher, $plafond);}
		header ('location: index.php?choixTraitement=parametres&action=voir&lstParam='.$type);
	}
//----------------------------------------- MODIFICATION 
case 'validerModifier':
	{ 
		if ($_REQUEST['zOk']=="OK") {$pdo->majParametre($type, $valeur, addslashes ($_REQUEST['zLibelle']), $plancher, $plafond);}	
		header ('location: index.php?choixTraitement=parametres&action=voir&lstParam='.$type);
		break;
	}
//----------------------------------------- SUPPRESSION
case 'validerSupprimer':
	{ 
		if ($_REQUEST['zOk']=="OK") {$pdo->supprimeParametre($type, $valeur);}	
		header ('location: index.php?choixTraitement=parametres&action=voir&lstParam='.$type);
		break;
	}	
default :
	{
		echo 'erreur d\'aiguillage !'.$action;
		break;
	}
}
?>