<?php
// ****************************************'
//  Le CASTEL-BTS SIO/ PROJET SDIS29       '
//  Programme: c_interventions.php         '
//  Objet    : gestion des interventions   '
//  Client   : Bts SIO2                    '
//  Version  : 1.0                         '
//  Date     : 23/05/2019 à 12h00          '
//  Auteur   : pascal-blain@wanadoo.fr     '
//*****************************************'
//require_once ("../include/class.pdo.php");

$action = $_REQUEST['action'];
switch($action) 
{
case 'listeInterventions':
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
		$titre="CIS"; //Centre d'incendie et de secours :";
		$lesCasernes	= $pdo->getLesCasernes($_SESSION["adr1"]);
		// include("vues/v_choixCaserne.php");
		// $lesPompiers	= $pdo->getLesPompiers($choix);
		// $lesInterventions=$pdo->getLesInterventions($choix);
		// $intervention=1;
		// $lesParticipants= $pdo->getLesParticipants($choix, $intervention);
		include("vues/v_lesInterventions.php");
		break;
	}


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
		$titre="CIS"; //Centre d'incendie et de secours :";
		$lesCasernes	= $pdo->getLesCasernes($_SESSION["adr1"]);
		include("vues/v_choixCaserne.php");
		$lesPompiers	= $pdo->getLesPompiers($choix);
		$lesInterventions=$pdo->getLesInterventions($choix);
		$intervention=1;
		$lesParticipants= $pdo->getLesParticipants($choix, $intervention);
		include("vues/v_Intervention.php");
		break;

	
	
}

case 'interventions' :
{
	require_once ("../include/class.pdo.php");
	$pdo = PdoBD::getPdoBD();
	if( isset($_GET['indiceType']))
	{
		

		$indiceType = $_GET['indiceType'];

		// la requête SQL en fonction de l'indice
		$resultats = $pdo->getPlafond($indiceType);


		echo(json_encode($resultats));
	}
	break;
}
case 'lstPompiers' :
{
	require_once ("../include/class.pdo.php");
	$pdo = PdoBD::getPdoBD();
	if( (isset($_GET['idCaserne'])) && isset($_GET['laTrancheH']))
	{

		$idCaserne = $_GET['idCaserne'];
		$laTrancheH = $_GET['laTrancheH'];
		$date =  date('Y-m-d');

		$resultats = $pdo->getPompierGarde($laTrancheH, $idCaserne, $date);
		//header("location: index.php?choixTraitement=pompiers&action=voir&type=a");

		echo(json_encode($resultats));
	}
	break;
}	

case 'lstInterventions' :
{
	require_once ("../include/class.pdo.php");
	
	$pdo = PdoBD::getPdoBD();
    if(isset($_GET['idCaserne']))
    {
        $iCis = $_GET['idCaserne'];
        $resultats = $pdo->getInterventions($iCis);
        echo(json_encode($resultats));
    }
	break;
}


case 'finIntervention' :
{
	require_once ("../include/class.pdo.php");
	$pdo = PdoBD::getPdoBD();

	if(isset($_GET['idIntervention']) && isset($_GET['idCaserne']))  
	{
		$iId = $_GET['idIntervention'];
		$iCis = $_GET['idCaserne'];
		date_default_timezone_set('Europe/Paris');
		$heureDeFin = date('Y-m-d H:i:s');
		$res = $pdo->modifHeureDeFin($iId, $heureDeFin, $iCis);
		// echo $res;
	}
	break;
}
	
case 'ajoutIntervention' :
	{
		require_once ("../include/class.pdo.php");
		$pdo = PdoBD::getPdoBD();
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Récupération des données du formulaire
			$idCaserne = $_POST["lstCasernes"];
			//defini le fuseau horaire de Paris
			date_default_timezone_set('Europe/Paris');
			$heureDebut = date('Y-m-d H:i:s');
			$heureFin = null;
			if(isset($_POST["laTranche"]))
			{
				$laTranche = $_POST["laTranche"];
			}
	
			$typeIntervention = $_POST["type"];
	
			if(isset($_POST["nbPompier"]))
			{
				$nbPompierIntervention = $_POST["nbPompier"];
			}
		   
			$nbPompierIntervention;
			$lieuIntervention = $_POST["lieu"];
			$description = $_POST["description"];
			$numIntervention = $pdo->getNumIntervention($idCaserne);
			$date = date('Y-m-d H:i:s');
			$pdo->ajoutIntervention($idCaserne, $numIntervention, $lieuIntervention, $description, $date, $laTranche, $heureDebut, $heureFin, $typeIntervention, $nbPompierIntervention, );
	
	
			$donneesFormulaire = $_POST;
			
			// recuperation du tableau des id des pompiers et en utilise json_decode afin d'enlever le json
			// et de recuperer ces valeurs et pouvoir les utiliser sans json 
			$tabId = json_decode($donneesFormulaire['tabId'], true);
			//echo "valeur tableau de garde";
			foreach ($tabId as $idPompier) {
				//echo $idPompier . "\n <br>";
				$pdo->ajoutEquipeIntervention($idCaserne,$idPompier, $numIntervention);
			}
			// header("Location : ./index.php?choixTraitement=pompiers&action=voir&type=a");
			//header("Location: vues/c_pompiers.php");
		}
	   
	
	
		break;
	}
	

//----------------------------------------- 
case 'majGarde':
	{
		/*$pdo->majGarde($_REQUEST["ztLaDate"], $_REQUEST["ztLaTranche"], $_REQUEST["ztExGarde"], $_REQUEST["ztPompier"]);
		header ('location: index.php?choixTraitement=gardes&action=voir&zSemaine='.$_REQUEST["zSemaine"].'&zAnnee='.$_REQUEST["zAnnee"]);
		break;*/
	}

//----------------------------------------- 
default :
	{
		echo 'erreur d\'aiguillage !'.$action;
		break;
	}
}
/*
table equipe:
eCis  	smallint(6)
ePompier 	smallint(6)
eIntervention 	smallint(6) 

table intervention :
iCis  	smallint(6)
iId 	smallint(6)
iLieu 	varchar(50)
iDescription 	varchar(255)
iDate 	datetime 
iTranche 	tinyint(3) 	
iHeureDebut 	datetime 	
iHeureFin 	datetime
	*/
?>
