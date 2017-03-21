<?php

require_once('inc/init.inc.php');

if (userConnecte()) {
	header('location:profil.php');
}

if($_POST){

	//debug($_POST);

	// ******************************************
	// ******** VERIFICATION DES CHAMPS *********
	// ******************************************
	// $verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $_POST['pseudo']);
	if (!empty($_POST['pseudo'])) {
		if (verif_regex('carac_nbr', $_POST['pseudo'])) {
			if (strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20) {
				$msg .= 'Veuillez renseigner un pseudo de 3 à 20 caractères. Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.';
			}
		} else {
			$msg .= 'Pseudo : caractères non accentués, chiffres, "-", "_" et ".".';
		}	
	} else {
		$msg .= 'Veuillez renseigner un pseudo !';
	}
	
	// VERIFICATION DU MOT DE PASSE
	if (!empty($_POST['mdp'])) {
		if (verif_regex('carac_nbr', $_POST['mdp'])) {
			if (strlen($_POST['mdp']) < 3 || strlen($_POST['mdp']) > 20) {
				$msg .= 'Veuillez renseigner un mot de passe de 3 à 20 caractères. Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.';
			}
		} else {
			$msg .= 'Mot de passe : caractères non accentués, chiffres, "-", "_" et ".".';
		}
	} else {
		$msg .= 'Veuillez renseigner un mot de passe !';
	}

	if (!empty($_POST['mdp2'])) {
		if($_POST['mdp2'] != $_POST['mdp']){
			$msg .='Les deux mots de passe sont différents';
		}
	} else {
		$msg .= 'Veuillez renseigner un mot de passe de confirmation !';
	}
	

	// VERIFICATION DU NOM
	if (!empty($_POST['nom'])) {
		if (verif_regex('carac_nbr', $_POST['nom'])){
			if (strlen($_POST['nom']) < 3 || strlen($_POST['nom']) > 20) {
				$msg .= 'Veuillez renseigner un nom de 3 à 20 caractères. Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.';
			}
		} else {
			$msg .= 'Nom : caractères non accentués, chiffres, "-", "_" et ".".';
		}
	} else {
		$msg .= 'Veuillez renseigner un nom !';
	}

		

	// VERIFICATION DU PRENOM
	if (!empty($_POST['prenom'])) {
		if (verif_regex('carac_nbr', $_POST['nom'])) {
			if (strlen($_POST['prenom']) < 3 || strlen($_POST['prenom']) > 20) {
				$msg .= 'Veuillez renseigner un prenom de 3 à 20 caractères.<br> Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.';
			}
		} else {
			$msg .= 'Prenom : caractères non accentués, chiffres, "-", "_" et ".".';
		}
	} else {
		$msg .= 'Veuillez renseigner un prenom !';
	}
		

	// VERIFICATION DE L'EMAIL
	if (!empty($_POST['email'])) {
		if(strlen($_POST['prenom']) > 50){
			$msg .= 'Email trop long !'; 
		} else {
			$regex_email = '/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/';
			if(!preg_match($regex_email, $_POST['email'])) {
				$msg .= 'Veuillez renseigner un email valide !';
			}
		}
		
	} else {
		$msg .= 'Veuillez renseigner un email !';
	}
		

	// VERIFICATION DE LA CIVIVITE
	if (empty($_POST['civilite']) || ($_POST['civilite'] != 'm' && $_POST['civilite'] != 'f')) {
		$msg .= 'Petit malin vous ne m\'aurez pas !';
	}

	if(!empty($msg)){
		$type = 'danger';
	}

	// ******************************************
	// ***** INSERTION DES INFOS DANS LA BDD ****
	// ******************************************
		// est-ce que le pseudo est bien disponible

 	if (empty($msg)) {// cela signifie que tout est OK pour tous les champs
 		// pour vérifier que le pseudo  est dispo je dois faire une requête auprès de la BDD et qu'il y a un moins 1 résultat
 		$resultat1 = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
 		$resultat1 -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
 		$resultat1 -> execute();

 		$resultat2 = $pdo -> prepare("SELECT * FROM membre WHERE email = :email");
 		$resultat2 -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
 		$resultat2 -> execute();

 		if ($resultat1 -> rowCount() > 0) {
 			$msg .= 'Pseudo indisponible veuillez choisir un autre pseudo.';
 			$type = 'warning';
 		} elseif ($resultat2-> rowCount() > 0) {
 			$msg .= 'Email déjà associé à un autre utilisateur';
 			$type = 'warning';
 		}
 		else{// Le pseudo et l'email sont bien disponibles.

 			$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, '0', NOW()) ");


 			$mdp_crypte = md5($_POST['mdp']); // MD5() cryptage simple
 			//STR
 			$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
 			$resultat -> bindParam(':mdp', $mdp_crypte, PDO::PARAM_STR); 
 			$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR); 
 			$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR); 
 			$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR); 
 			$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR); 
 			  
 			//INT
 			 
 			
 			$resultat -> execute();

 			$msg = 'Félicitation pour votre enregistrement';
 			$type = 'success';
 			// redirection ou message de félicitation.
 			//header('location:index.php');
 		}
 	}
}

$data = array('type' => $type, 'msg' => $msg);
echo json_encode($data);

/*$page = 'Inscription';
require_once('inc/header.inc.php');

echo $msg;


require_once('inc/footer.inc.php');*/