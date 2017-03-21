<?php
require_once('inc/init.inc.php');

// traitement de la déconnexion.
if (isset($_GET['action']) && $_GET['action'] == 'deconnexion') {// Si une action est demandé dans l'url et que c'est une action de déconnexion.
	unset($_SESSION['membre']);// Je vide la partie 'membre' de la session pour déconnecter l'user
	header('location:index.php'); // Je redirige vers la même page pour éviter d conserver les paramètres dans l'url.
}


//Redirection si l'utilisateur est connecté.
if (userConnecte()) {
	header('location:profil.php');
}



if ($_POST) {
	//debug($_POST);

	// Sécurité pseudo
	if (!empty($_POST['pseudo'])) {
		if (verif_regex('carac_nbr', $_POST['pseudo'])) {
			if (strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20) {
				$msg .= 'Veuillez renseigner un pseudo de 3 à 20 caractères.<br> Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.';
			}
		} else {
			$msg .= 'Pseudo : caractères non accentués, chiffres, "-", "_" et ".".';
		}	
	} else {
		$msg .= 'Veuillez renseigner un pseudo !';
	}

	// Sécurité mdp
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



	if(empty($msg)){
		// Vérification que le pseudo existe
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo=:pseudo");
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR).
		$resultat -> execute();

		if ($resultat -> rowCount() > 0) {// le pseudo existe en BDD
			// Si existe, vérification que le mdp correspond
			$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
			//debug($membre);


			if ($membre['mdp'] == md5($_POST['mdp'])) {// Tout est OK, le mot de passe correspond bien.
				// On peut connecter notre utilisateur
				// Mettre ses infos dans $_SESSION ($_SESSION est un array, comme toutes les runkit_superglobales).
				$_SESSION['membre'] = array();
				//$_SESSION['membre']['pseudo'] == $membre['pseudo'];
				
				foreach ($membre as $indice => $valeur) {
					if ($indice != 'mdp') {
						$_SESSION['membre'][$indice] = $valeur;
					}
				}

				//debug($_SESSION);

				$msg = 'Félicitation';
				$type = 'success';
			}
			else
			{
				$msg .= 'Erreur de mot de passe, tu brûleras en enfer impie !';
				$type = 'danger';
			}
		}
		else
		{
			$msg .= ' Pseudo inconnu !';
			$type = 'danger';
		}

	}
	else 
	{
		$type = 'warning';
	}
}

$data = array('type' => $type, 'msg' => $msg);
echo json_encode($data);
// $page = 'connexion';
// require_once('inc/header.inc.php');




//require_once('inc/footer.inc.php');