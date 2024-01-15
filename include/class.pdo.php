<?php
/** 
 * @author 	: Pascal BLAIN, lycee le Castel à Dijon
 * @version : 2023-09-18 à 16h43
 * Classe d'acces aux donnees. Utilise les services de la classe PDO pour l'application
 * Les attributs sont tous statiques, les 4 premiers pour la connexion
 * $monPdo est de type PDO - $monPdoBD contient l'unique instance de la classe
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoBD
{   		
	private static $serveur='mysql:host=localhost';
	//private static $serveur='mysql:host=172.20.10.2';

	private static $bdd='dbname=bddap3';   		
	private static $user='root';    		
	private static $mdp='root';	
	private static $monPdo;
	private static $monPdoBD=null;
			
	private function __construct()
	{
		PdoBD::$monPdo = new PDO(PdoBD::$serveur.';'.PdoBD::$bdd, PdoBD::$user, PdoBD::$mdp); 
		PdoBD::$monPdo ->exec("SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");
		PdoBD::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct()
	{
		PdoBD::$monPdo = null;
	}

	/**
	 * Fonction statique qui cree l'unique instance de la classe PdoBD
	 * Appel : $instancePdoBD = PdoBD::getPdoBD();
	 */
	public  static function getPdoBD()
	{
		if(PdoBD::$monPdoBD==null)	{PdoBD::$monPdoBD= new PdoBD();}
		return PdoBD::$monPdoBD;  
	}

	/**
	 * Retourne les informations d'un centre de coordination
	 */
	public function getLesCasernes($leCentre)
	{		
		$req = "SELECT cId, cNom, cAdresse, cTel, cGroupement
					FROM caserne
					WHERE cGroupement='".$leCentre."'
					ORDER BY cNom;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des casernes ..", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}
	
	/**
	 * Retourne les informations des pompiers
	*/
	public function getLesPompiers($cis)
	{		
		$req = "SELECT pCis, pId, pNom, pPrenom, pStatut
					FROM pompier
					WHERE pCis=".$cis."
					ORDER BY pNom;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des pompiers ..", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}
	
	/**
	 * Retourne les informations d'un pompier sous la forme d'un tableau associatif
	*/
	public function getInfosPompier($login,$mdp)
{
    $req = "SELECT pCis, pId as id, pNom as nom, pPrenom as prenom, pMail, pLogin, pMdp, pStatut, pGrade, pType,
            (SELECT  pLibelle FROM parametre WHERE parametre.pIndice = pompier.pStatut AND pType='statAgt') as wStatut,
            (SELECT  pLibelle FROM parametre WHERE parametre.pIndice = pompier.pGrade AND pType='grade') as wGrade,
            (SELECT  pLibelle FROM parametre WHERE parametre.pIndice = pompier.pType AND pType='typePer') as wType,
            pAdresse, pCp, pVille, pBip, pCommentaire,
            (SELECT cNom FROM caserne WHERE caserne.cId = pompier.pCis) as cNom,
            (SELECT cAdresse FROM caserne WHERE caserne.cId = pompier.pCis) as cAdresse,
            (SELECT cTel FROM caserne WHERE caserne.cId = pompier.pCis) as cTel,
            (SELECT cGroupement FROM caserne WHERE caserne.cId = pompier.pCis) as cGroupement
            FROM pompier";
    if ($login==="*")
    {$req.=" WHERE pCis=".$_SESSION['cis']." AND pId=$mdp";}
    else
    {$req.=" WHERE pLogin='$login'
             AND pMdp='$mdp'";}
    $rs = PdoBD::$monPdo->query($req);
    if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des informations d'un pompier...", $req, PdoBD::$monPdo->errorInfo());}
    $ligne = $rs->fetch();
    return $ligne;
}

function majInfosPompier($id, $nom, $prenom, $adresse, $cp, $ville, $numBip, $email, $login, $pCis, $caNom, $caAdresse, $caTel, $caGroupement,$pompierStatut , $pompierGrade, $pompierType, $pCommentaire ){
    $sql = "UPDATE pompier
    INNER JOIN caserne ON pompier.pCis = caserne.cId
    SET pompier.pNom = '$nom',
        pompier.pPrenom = '$prenom',
        pompier.pAdresse = '$adresse',
        pompier.pCp = '$cp',
        pompier.pVille = '$ville',
        pompier.pBip = '$numBip',
        pompier.pMail = '$email',
        pompier.pLogin = '$login',
        pompier.pCis = '$pCis',
        caserne.cNom = '$caNom',
        caserne.cAdresse = '$caAdresse',
        caserne.cTel = '$caTel',
        caserne.cGroupement = '$caGroupement',
        pompier.pStatut = '$pompierStatut',
        pompier.pGrade = '$pompierGrade',
        pompier.pType = '$pompierType',
        pompier.pCommentaire = '$pCommentaire'
    WHERE pompier.pId = '$id' and pompier.pCis = '$pCis'";

	try {
		return self::$monPdo->exec($sql);
	} catch (PDOException $e) {
		echo "Une erreur s'est produite lors de la mise à jour des informations du pompier: " . $e->getMessage();
	}
}

function majInfosPompierSimple($id, $nom, $prenom, $adresse, $cp, $ville, $numBip, $email, $login, $pCis, $caNom, $caAdresse, $caTel, $caGroupement, $pCommentaire ){
    $sql = "UPDATE pompier
    INNER JOIN caserne ON pompier.pCis = caserne.cId
    SET pompier.pNom = '$nom',
        pompier.pPrenom = '$prenom',
        pompier.pAdresse = '$adresse',
        pompier.pCp = '$cp',
        pompier.pVille = '$ville',
        pompier.pBip = '$numBip',
        pompier.pMail = '$email',
        pompier.pLogin = '$login',
        pompier.pCis = '$pCis',
        caserne.cNom = '$caNom',
        caserne.cAdresse = '$caAdresse',
        caserne.cTel = '$caTel',
        caserne.cGroupement = '$caGroupement',
        pompier.pCommentaire = '$pCommentaire'
    WHERE pompier.pId = '$id' and pompier.pCis = '$pCis'";

	try {
		return self::$monPdo->exec($sql);
	} catch (PDOException $e) {
		echo "Une erreur s'est produite lors de la mise à jour des informations du pompier: " . $e->getMessage();
	}
}



function getlstTypePompier()
{
    $sql = 'SELECT pIndice, pLibelle FROM parametre where pType="typePer";';
    $res = self::$monPdo->prepare($sql);
    $res->execute();
    return $res->fetchAll();
}

function getlstGrade()
{
    $sql = 'SELECT pIndice, pLibelle FROM parametre where pType="grade";';
    $res = self::$monPdo->prepare($sql);
    $res->execute();
    return $res->fetchAll();
}

function getlstStatut()
{
	$sql = 'SELECT pIndice, pLibelle FROM parametre where pType="statAgt";';
    $res = self::$monPdo->prepare($sql);
    $res->execute();
    return $res->fetchAll();
}

public function majActivite($pCis, $pId, $date, $tranche,$dispo)
{

    $sql = "UPDATE activite 
    SET aDisponibilite = :dispo
    WHERE activite.aCis = :cis and activite.aPompier = :id and aTranche = :tranche and aDateGarde = :date ;";

    try {
        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute([
            'dispo' => $dispo,
            'cis' => $pCis,
            'id' => $pId,
            'tranche' => $tranche,
            'date' => $date
        ]);

        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "Une erreur s'est produite lors de la mise à jour de l'activité: " . $e->getMessage();
    }
    return false;
}

public function supActivite($pCis, $pId, $date, $tranche)
{
    $sql = "DELETE FROM activite 
    WHERE activite.aCis = :cis and activite.aPompier = :id and aTranche = :tranche and aDateGarde = :date ;";

    try {
        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute([
            'cis' => $pCis,
            'id' => $pId,
            'tranche' => $tranche,
            'date' => $date
        ]);

        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "Une erreur s'est produite lors de la suppression de l'activité: " . $e->getMessage();
    }

    // Retourner une valeur par défaut si une erreur se produit
    return false;
}


public function addActivite($pCis, $pId, $date, $tranche, $dispo)
{
    $sql = "INSERT INTO activite (aCis, aPompier, aDateGarde, aTranche, aDisponibilite) 
    VALUES (:cis, :id, :date, :tranche, :dispo);";

    try {
        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute([
            'cis' => $pCis,
            'id' => $pId,
            'date' => $date,
            'tranche' => $tranche,
            'dispo' => $dispo
        ]);

        return $stmt->rowCount();
    } catch (PDOException $e) {
        echo "Une erreur s'est produite lors de l'ajout de l'activité: " . $e->getMessage();
    }

    // Retourner une valeur par défaut si une erreur se produit
    return false;
}


/**
 * Met à jour la garde d'un pompier sur une tranche 
*/
	
	
public function majGarde($pCis, $pId, $date, $tranche, $valeurGarde)
{
    $req = "UPDATE ACTIVITE 
            SET aGarde = :valeurGarde
            WHERE aCis = :pCis AND aPompier = :pId AND aTranche = :tranche AND aDateGarde = :date";

    $stmt = PdoBD::$monPdo->prepare($req);

    $stmt->bindParam(':valeurGarde', $valeurGarde, PDO::PARAM_INT);
    $stmt->bindParam(':pCis', $pCis, PDO::PARAM_INT);
    $stmt->bindParam(':pId', $pId, PDO::PARAM_INT);
    $stmt->bindParam(':tranche', $tranche, PDO::PARAM_INT);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);

    $rs = $stmt->execute();

    if ($rs === false) {
        afficherErreurSQL("Problème lors de la mise à jour de la garde dans la base de données.", $req, $stmt->errorInfo());
    }

    echo $pCis . "  " . $date . "  " . $pId . "  " . $tranche . "   " . $valeurGarde;
}


/**
	* Met à jour une ligne de la table pompier 
*/
	public function majPompier($cis,$valeur,$nom,$prenom,$statut,$mail,$login,$mdp,$grade,$type,$adresse,$cp,$ville,$tel,$commentaire)
	{
		// $req = "
		
		
		
		
		// ;";
		// $rs = PdoBD::$monPdo->exec($req);
		// if ($rs === false) {afficherErreurSQL("Probleme lors de la mise à jour du pompier dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}

		$sql = " activite (aCis, aPompier, aDateGarde, aTranche, aDisponibilite) 
		VALUES (:cis, :id, :date, :tranche, :dispo);";
	
		try {
			$stmt = self::$monPdo->prepare($sql);
			$stmt->execute([
				'cis' => $pCis,
				'id' => $pId,
				'date' => $date,
				'tranche' => $tranche,
				'dispo' => $dispo
			]);
	
			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo "Une erreur s'est produite lors de l'ajout de l'activité: " . $e->getMessage();
		}
	
		// Retourner une valeur par défaut si une erreur se produit
		return false;
	}
	
/**
	* supprime une ligne de la table pompier 
*/
	public function supprimePompier($cis, $valeur)
	{
		$req = "
		;";
		$rs = PdoBD::$monPdo->exec($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la suppression du pompier dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
	}
	
/**
 * ajoute une ligne dans la table pompier
*/
	public function ajoutPompier($cis,$valeur,$nom,$prenom,$statut,$mail,$login,$mdp,$grade,$type,$adresse,$cp,$ville,$tel,$commentaire)
	{			
		$req = "INSERT INTO pompier 
				(pCis,pId,pNom,pPrenom,pStatut,pMail,pLogin,pMdp,pGrade,pType, pAdresse,pCp,pVille,pBip,pCommentaire,pDateEnreg,pDateModif) 
				VALUES 
					(
					);";
		$rs = PdoBD::$monPdo->exec($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de l'insertion du pompier dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
	}
	
/**
 * Retourne les informations des gardes d'un pompier (ou des pompiers) sous la forme d'un tableau associatif
*/
	public function getInfosGardes($pompier)
	{
		$req = "SELECT aPompier, DATE_FORMAT(aDateGarde,'%d/%m/%Y') as wDate, aTranche, pLibelle as tLibelle
				FROM  activite INNER JOIN parametre ON pType='tranche' AND aTranche=pIndice
				WHERE aCis=".$_SESSION['cis'];
		if ($pompier<>"*") {
		$req .= " AND aPompier=".$pompier;}
		$req .= " AND aGarde=True
				ORDER BY aPompier, aDateGarde DESC, aTranche ASC;";

		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des gardes d'un pompier...", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes;
	}
	
/**
 * Retourne les informations des disponibilites hebdomadaires d'un pompier sous la forme d'un tableau associatif
*/
	public function getDisposHebdo($pompier,$semaine, $annee)
	{
		$premierJour = strtotime("+$semaine weeks",mktime(0,0,0,1,1,$annee));
		if (date('w',$premierJour) != 1){$premierJour = strtotime("last monday",$premierJour);}
		$debut=date('Y/m/d',$premierJour);
		$fin=date('Y/m/d',strtotime("6 days",$premierJour));
		
		$req = "SELECT pId, pNom, pPrenom, DATE_FORMAT(aDateGarde,'%d/%m/%Y') as wDate, aTranche, aDisponibilite, aGarde, d.pValeur as dCouleur
				FROM (activite INNER JOIN parametre t ON t.pType='tranche'AND aTranche=t.pIndice
				INNER JOIN parametre d ON d.pType='dispo' AND aDisponibilite=d.pIndice)
				RIGHT OUTER JOIN pompier ON aCis=pCis AND aPompier=pId
				WHERE aCis=".$_SESSION['cis'];
		if ($pompier<>"*") {
		$req .= " AND aPompier=".$pompier;}
		$req .= " AND aDateGarde BETWEEN '".$debut."' AND '".$fin."'
				AND aDisponibilite>0
				ORDER BY aPompier, aDateGarde ASC, aTranche ASC;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des gardes d'un pompier...", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		
		$lesDispos = array();
		if ($pompier<>"*")
		{
			for($jour=0; $jour<= 6; $jour++) 
			{	$laDate = date('d/m/Y',strtotime('+'.$jour.' day',$premierJour));
				$lesDispos[$pompier][$laDate] = array('dPompier'=> $pompier, 'dDate'=> $laDate, 
											'd1'=>0, 'd2'=>0, 'd3'=>0, 'd4'=>0,
											'g1'=>0, 'g2'=>0, 'g3'=>0, 'g4'=>0,
											'c1'=>'gray', 'c2'=>'gray', 'c3'=>'gray', 'c4'=>'gray');
			}
		}
		else
		{
			$req = "SELECT pCis, pId, pNom, pPrenom, pStatut
					FROM pompier
					WHERE pCis=".$_SESSION['cis']."
					ORDER BY pNom, pPrenom;";
			$rs = PdoBD::$monPdo->query($req);
			if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des pompiers ..", $req, PdoBD::$monPdo->errorInfo());}
			$lesPompiers = $rs->fetchAll();
			
			foreach ($lesPompiers as $unPompier)		
			{ $pompier=$unPompier['pId'];	 
			for($jour=0; $jour<= 6; $jour++) 
				{$laDate = date('d/m/Y',strtotime('+'.$jour.' day',$premierJour));
				$lesDispos[$pompier][$laDate] = array('dPompier'=> $pompier, 'dDate'=> $laDate, 
											'd1'=>0, 'd2'=>0, 'd3'=>0, 'd4'=>0,
											'g1'=>0, 'g2'=>0, 'g3'=>0, 'g4'=>0,
											'c1'=>'gray', 'c2'=>'gray', 'c3'=>'gray', 'c4'=>'gray');
				}
			}
		}
		foreach ($lesLignes as $uneLigne)		
		{ 	
			$pompier	= $uneLigne['pId'];
			$laDate		= $uneLigne['wDate'];
			$dispo		= "d".$uneLigne['aTranche'];
			$garde		= "g".$uneLigne['aTranche'];
			$couleur	= "c".$uneLigne['aTranche'];
			$lesDispos[$pompier][$laDate][$dispo]=$uneLigne['aDisponibilite'];
			$lesDispos[$pompier][$laDate][$garde]=$uneLigne['aGarde'];
			$lesDispos[$pompier][$laDate][$couleur]=$uneLigne['dCouleur'];
		}
/*			echo "<PRE>";
			print_r($lesDispos);
			echo "</PRE>";*/		
	return $lesDispos;
	}
			
/**
 * Retourne dans un tableau associatif les informations de la table tranche 
*/
	public function getLesTranches()
	{
		$req = "SELECT pIndice as tId, pLibelle as tLibelle
				FROM parametre WHERE pType='tranche'
				ORDER by 1;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la recherche des tranches dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes;
	}

/**
	 * Retourne les informations de la table typeParametre
	*/
	public function getLesParametres()
	{
		$req = "SELECT tpId, tpLibelle, tpBooleen, tpChoixMultiple
					FROM typeParametre
					ORDER BY tpLibelle;";
		$rs = PdoBD::$monPdo->query($req);
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}

/**
 * Retourne dans un tableau associatif les informations de la table PARAMETRE (pour un type particulier)
*/
	public function getParametre($type)
	{
		$req = "SELECT pIndice, pLibelle, pValeur, pPlancher, pPlafond ";
		if ($type=="typePer"){$req.=",(SELECT count(*) FROM parametre p INNER JOIN pompier on p.pIndice=pompier.pType WHERE p.pType='$type' AND p.pIndice=parametre.pIndice) as nb";}
		elseif ($type=="statAgt"){$req.=",(SELECT count(*) FROM parametre p INNER JOIN pompier on p.pIndice=pompier.pStatut WHERE p.pType='$type' AND p.pIndice=parametre.pIndice) as nb";}
		elseif ($type=="grade"){$req.=",(SELECT count(*) FROM parametre p INNER JOIN pompier on p.pIndice=pompier.pgrade WHERE p.pType='$type' AND p.pIndice=parametre.pIndice) as nb";}
		else	{$req.=", 0 as nb";}
				$req.=" FROM parametre
				WHERE pType='$type'
				ORDER by pIndice;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la recherche des parametres ".$type." dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes;
	}

	/**
	 * Retourne dans un tableau associatifles informations de la table PARAMETRE (pour un type particulier)
	*/
	public function getInfosParam($type, $valeur)
	{	
		if ($valeur=="NULL") 
		{$req = "SELECT pType, max(pIndice)+1 AS pIndice, ' ' AS pLibelle, tpLibelle
					 FROM parametre INNER JOIN typeParametre ON typeParametre.tpId=parametre.pType
					 WHERE pType='$type';";
		}
		else
		{$req = "SELECT pType, pIndice, pLibelle, tpLibelle, pPlancher, pPlafond
					 FROM parametre INNER JOIN typeParametre ON typeParametre.tpId=parametre.pType
					 WHERE pType='$type'
					 AND pIndice='$valeur';";
		}		
		$rs = PdoBD::$monPdo->query($req);
		$ligne = $rs->fetch();		
		return $ligne;
	}

/**
 * Met a jour une ligne de la table PARAMETRE
*/
	public function majParametre($type, $valeur, $libelle, $plancher, $plafond)
	{
		$req = "UPDATE parametre SET pLibelle='$libelle', pPlancher='$plancher', pPlafond='$plafond'
					WHERE pType='$type'
					AND pIndice=$valeur;";
		$rs = PdoBD::$monPdo->exec($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la modification d'un parametre dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
	}
	
/**
 * supprime une ligne de la table PARAMETRE 
*/
	public function supprimeParametre($type, $valeur)
	{
		$req = "DELETE 
					FROM parametre
					WHERE pType='$type'
					AND pIndice=$valeur;";
		$rs = PdoBD::$monPdo->exec($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la suppression d'un parametre dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
	}
	
/**
 * ajoute une ligne dans la table PARAMETRE
*/
	public function ajoutParametre($type, $valeur, $libelle, $plancher, $plafond)
	{	
		$req = "INSERT INTO parametre 
					(pType, pIndice, pLibelle, pPlancher, pPlafond) 
					VALUES ('$type', $valeur, '$libelle', $plancher, $plafond);";
		$rs = PdoBD::$monPdo->exec($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de l'insertion d'un parametre dans la base de donn&eacute;es.", $req, PdoBD::$monPdo->errorInfo());}
	}


/**
 * envoyer un message electronique
*/
	public function envoyerMail($mail, $sujet, $msg, $entete)
	{			
		if (mail($mail, $sujet, $msg, null)==false)  
		{ echo 'Suite à un problème technique, votre message n a pas été envoyé a '.$mail.' sujet'.$sujet.'message '.$msg.' entete '.$entete;}
	}

/**
 * Retourne les informations d'une intervention
 */
	public function getInfosIntervention($intervention)
	{		
		$req = "SELECT iCis, iId, iLieu, iDescription, iDate , iTranche, iHeureDebut, iHeureFin
					FROM intervention
					WHERE iId=".$intervention.";";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture de l'intervention ...", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}
	
/**
 * Retourne les informations de toutes les interventions d'une caserne
 */
	public function getLesInterventions($cis)
	{		
		$req = "SELECT iCis, iId, iLieu, iDescription, iDate , iTranche, iHeureDebut, iHeureFin
					FROM intervention
					WHERE iCis=".$cis."
					ORDER BY iId;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des interventions de la caserne ...", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}
	
	/**
 * Retourne les participants à une intervention
 */
	public function getLesParticipants($cis, $intervention)
	{		
		$req = "SELECT pId, pNom, pPrenom
					FROM pompier INNER JOIN equipe ON pompier.pId=equipe.ePompier AND pompier.pCis=equipe.eCis
					WHERE eIntervention=".$intervention."
					AND eCis=".$cis."
					ORDER BY pNom, pPrenom;";
		$rs = PdoBD::$monPdo->query($req);
		if ($rs === false) {afficherErreurSQL("Probleme lors de la lecture des participants ..", $req, PdoBD::$monPdo->errorInfo());}
		$lesLignes = $rs->fetchAll();
		return $lesLignes; 
	}

	

	function getLstInterventions($indiceCat)
	{
	$req = "SELECT pIndice, pLibelle AS typesInterventions FROM parametre
	WHERE pType = 'typeInt' AND pIndice LIKE :pIndice";
	$stmt = PdoBD::$monPdo->prepare($req);
	$params = array(
		':pIndice' => $indiceCat . '%'
	);
	if (!$stmt->execute($params)) {
		afficherErreurSQL("Probleme lors de la recuperation des type d'interventions", $req, PdoBD::$monPdo->errorInfo());
	}
	$lesLignes = $stmt->fetchAll();
	return $lesLignes;
	}


	function getCategorie() {
		$req = "SELECT pLibelle,pIndice, pType FROM parametre WHERE pType = 'cateInt';";
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->execute();
		$lesLignes = $stmt->fetchAll();
		return $lesLignes;
	}

	function getPlafond($pIndice)
	{
		$req = "SELECT parametre.pLibelle, parametre.pPlancher, parametre.pPlafond FROM parametre WHERE pType='typeInt' AND pIndice=:pIndice";
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->bindParam(':pIndice', $pIndice, PDO::PARAM_INT);
		$stmt->execute();
		$lesLignes = $stmt->fetchAll();
		return $lesLignes;
	}




	// function getPompierGarde($tranche, $cis, $dateGarde)
	// {
		// 	$req = "SELECT pompier.pCis,
		// 	pompier.pNom,
		// 	pompier.pPrenom,
		// 	pompier.pId,
		// 	activite.aTranche,
		// 	activite.aGarde,
		// 	activite.aDisponibilite,
		// 	false as enintervention
		// FROM pompier
		// INNER JOIN activite
		// 	ON activite.aCis = pompier.pCis
		// 	AND activite.aPompier = pompier.pId
		// WHERE activite.aCis = :aCis
		// 	AND activite.aTranche = :aTranche
		// 	AND activite.aDateGarde = :aDateGarde
		// 	AND activite.aGarde = 1
		// 	AND pompier.pId not in (SELECT equipe.ePompier from equipe  inner join intervention on intervention.iCis = equipe.eCis and intervention.iId = equipe.eIntervention where equipe.eCis = :eCis and intervention.iHeureFin is null) 
		// UNION 
		// SELECT intervention.iCis , equipe.ePompier , intervention.iDate, equipe.eIntervention 
		// FROM intervention 
		// INNER JOIN equipe 
		// 	ON intervention.iCis = equipe.eCis 
		// 	AND intervention.iId = equipe.eIntervention 
		// WHERE intervention.iTranche = :iTranche 
		// 	AND intervention.iDate = :iDate 
		// 	AND intervention.iCis = :iCis";

	// 		$stmt = PdoBD::$monPdo->prepare($req);
	// 		$stmt->bindParam(':aCis', $cis);
	// 		$stmt->bindParam(':aTranche', $tranche);
	// 		$stmt->bindParam(':aDateGarde', $dateGarde);
	// 		$stmt->bindParam(':eCis', $cis);
	// 		$stmt->bindParam(':iTranche', $tranche);
	// 		$stmt->bindParam(':iDate', $dateGarde);
	// 		$stmt->bindParam(':iCis', $cis);
	// 		$stmt->execute();

	// 		$result = $stmt->fetchAll();
	// 		return $result;
	// }

	

	function getPompierGarde($tranche, $cis, $dateGarde)
	{
	
		$req = "SELECT pompier.pCis,
		pompier.pNom,
		pompier.pPrenom,
		pompier.pId,
		activite.aTranche,
		activite.aGarde,
		activite.aDisponibilite,
		false as enintervention
	FROM pompier
	INNER JOIN activite
		ON activite.aCis = pompier.pCis
		AND activite.aPompier = pompier.pId
	WHERE activite.aCis = :aCis
		AND activite.aTranche = :aTranche
		AND activite.aDateGarde = :aDateGarde
		AND activite.aGarde = 1
		AND pompier.pId not in (SELECT equipe.ePompier from equipe  inner join intervention on intervention.iCis = equipe.eCis and intervention.iId = equipe.eIntervention where equipe.eCis = :eCis and intervention.iHeureFin is null) union 
	SELECT pompier.pCis,
		pompier.pNom,
		pompier.pPrenom,
		pompier.pId,
		activite.aTranche,
		activite.aGarde,
		activite.aDisponibilite,
		true as enintervention
	from pompier 
	INNER JOIN activite
		ON activite.aCis = pompier.pCis
		AND activite.aPompier = pompier.pId
	INNER JOIN equipe on equipe.eCis =  pompier.pCis and pompier.pId = equipe.ePompier 
	inner join intervention on intervention.iCis = equipe.eCis and intervention.iId = equipe.eIntervention
	WHERE activite.aCis =  :aCis
		AND activite.aTranche = :aTranche
		AND activite.aDateGarde = :aDateGarde
		AND activite.aGarde = 1
		AND equipe.eCis = :eCis and intervention.iHeureFin is null;
	
		";
	
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->bindParam(':aCis', $cis);
		$stmt->bindParam(':aTranche', $tranche);
		$stmt->bindParam(':aDateGarde', $dateGarde);
		$stmt->bindParam(':eCis', $cis);
		$stmt->execute();
	
		$result = $stmt->fetchAll();
		return $result;
	}
	



	/*SELECT pompier.pCis,
    pompier.pNom,
    pompier.pPrenom,
    pompier.pId,
    activite.aTranche,
    activite.aGarde,
    activite.aDisponibilite,
    false as enintervention
FROM pompier
INNER JOIN activite
    ON activite.aCis = pompier.pCis
    AND activite.aPompier = pompier.pId
WHERE activite.aCis = 2924
    AND activite.aTranche = 3
    AND activite.aDateGarde = '2023-12-03'
    AND activite.aGarde = 1
    AND pompier.pId not in (SELECT equipe.ePompier from equipe  inner join intervention on intervention.iCis = equipe.eCis and intervention.iId = equipe.eIntervention where equipe.eCis = 2924 and intervention.iHeureFin is null) union 
SELECT pompier.pCis,
    pompier.pNom,
    pompier.pPrenom,
    pompier.pId,
    activite.aTranche,
    activite.aGarde,
    activite.aDisponibilite,
    true as enintervention
from pompier 
INNER JOIN activite
    ON activite.aCis = pompier.pCis
    AND activite.aPompier = pompier.pId
INNER JOIN equipe on equipe.eCis =  pompier.pCis and pompier.pId = equipe.ePompier 
inner join intervention on intervention.iCis = equipe.eCis and intervention.iId = equipe.eIntervention
WHERE activite.aCis =  2924
    AND activite.aTranche = 3
    AND activite.aDateGarde = '2023-12-03'
    AND activite.aGarde = 1
    AND equipe.eCis = 2924 and intervention.iHeureFin is null;
     */



	public function ajoutIntervention($cis, $idInterv, $iLieu, $iDescription, $iDate, $iTranche, $iHeureDebut, $iHeureFin, $iType, $iNbPompiers)
    {          
        // Ajout de l'intervention
        $req = "INSERT INTO intervention
                (iCis,iId,iLieu,iDescription,iDate,iTranche,iHeureDebut,iHeureFin,iType, iNbPompiers)
                VALUES
                    (:cis, :idInterv, :iLieu, :iDescription, :iDate, :iTranche, :iHeureDebut, :iHeureFin, :iType, :iNbPompiers);";
        $stmt2 = PdoBD::$monPdo->prepare($req);
        $params = array(
            ':cis' => $cis,
            ':idInterv' => $idInterv,
            ':iLieu' => $iLieu,
            ':iDescription' => $iDescription,
            ':iDate' => $iDate,
            ':iTranche' => $iTranche,
            ':iHeureDebut' => $iHeureDebut,
            ':iHeureFin' => $iHeureFin,
            ':iType' => $iType,
            ':iNbPompiers' => $iNbPompiers
        );
        $rs=$stmt2->execute($params);
        $stmt2->closeCursor();
       
        if ($rs === false) {afficherErreurSQL("Probleme lors de l'insertion du de l'intervention", $req, PdoBD::$monPdo->errorInfo());}
    }


	public function ajoutEquipeIntervention($cis, $idPompier,$idInterv)
    {
        $req = "INSERT INTO equipe
        (eCis,ePompier,eIntervention)
        VALUES
        (:cis, :idPompier, :idInterv);";
        $stmt = PdoBD::$monPdo->prepare($req);
        $params = array(
            ':cis' => $cis,
            ':idInterv' => $idInterv,
            ':idPompier' => $idPompier
        );
        $rs=$stmt->execute($params);
        $stmt->closeCursor();
        if ($rs === false) {afficherErreurSQL("Probleme lors de l'insertion de l'equipe ", $req, PdoBD::$monPdo->errorInfo());}
    }



	public function getNumIntervention($idCaserne)
    {
        $req = "SELECT MAX(intervention.iId) as num from intervention where iCis = $idCaserne;";
        $rs = PdoBD::$monPdo->query($req);
        $resultat = $rs->fetch();
        if(is_null($resultat['num']))
        {
         return 1 ;
        }
        else{
            return $resultat['num'] +1;
        }
    }

	public function getInterventions($iCis) {
		$req = "SELECT  DISTINCT caserne.cNom,intervention.iId, intervention.iLieu, intervention.iDescription, intervention.iDate, intervention.iHeureFin, intervention.iCis
		from pompier LEFT JOIN intervention on intervention.iCis = pompier.pCis
		LEFT join caserne on caserne.cId = pompier.pCis where intervention.iCis = :iCis;";
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->execute(['iCis' => $iCis]);
		$interventions = $stmt->fetchAll();
		return $interventions;
	}

	public function interventions() {
		$req = "SELECT  DISTINCT intervention.iCis, caserne.cNom,intervention.iId, intervention.iLieu, intervention.iHeureDebut,intervention.iDescription, intervention.iDate, intervention.iHeureFin, intervention.iCis
		from pompier LEFT JOIN intervention on intervention.iCis = pompier.pCis
		LEFT join caserne on caserne.cId = pompier.pCis  WHERE iHeureFin is null and iHeureDebut is not null ;
		";
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->execute();
		$interventions = $stmt->fetchAll();
		return $interventions;
	}


	function modifHeureDeFin($iId, $heureDeFin, $iCis) {
		$req = "UPDATE intervention SET iHeureFin = :heureDeFin WHERE iId = :iId and intervention.iCis = :iCis";
		$stmt = PdoBD::$monPdo->prepare($req);
		$stmt->execute(['heureDeFin' => $heureDeFin, 'iId' => $iId, 'iCis' => $iCis]);
	}
	
	
	
}

?>