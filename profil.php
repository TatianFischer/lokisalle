<?php

require_once('inc/init.inc.php');

// redirection si l'utilisateur n'est pas connecté.
if (!userConnecte()) {
	header('location:connexion.php');
}


extract($_SESSION['membre']);

$page = 'Profil';
require_once('inc/header.inc.php');

// ******************************************
// ********** SUPPRESSION DE MEMBRE *********
// ******************************************
if(isset($_GET['action']) && $_GET['action'] == 'suppression' ){
	// Si une action est demandée et qu'il s'agit d'une suppression
	// Je supprime l'enregistrement
	$resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $id_membre"); 

	unset($_SESSION['membre']);
	header('location:inscription.php?msg=bye'); 
}

// ******************************************
// ********* MODIFICATION DE MEMBRE *********
// ******************************************
if ($_POST) {
	// INSERTION DES INFOS DES MEMBRES DANS LA BDD
		// ************ Vérication des champs ********************
	if (!empty($_POST['pseudo'])) {
		if (verif_regex('carac_nbr', $_POST['pseudo'])) {
			if (strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20) {
				$msg .= '<div class="erreur">Veuillez renseigner un pseudo de 3 à 20 caractères.<br> Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.</div>';
			}
		} else {
			$msg .= '<div class="erreur">Pseudo : caractères non accentués, chiffres, "-", "_" et ".".</div>';
		}	
	} else {
		$msg .= '<div class="erreur">Veuillez renseigner un pseudo !</div>';
	}
	

	// VERIFICATION DU NOM
	if (!empty($_POST['nom'])) {
		if (verif_regex('carac_nbr', $_POST['nom'])){
			if (strlen($_POST['nom']) < 3 || strlen($_POST['nom']) > 20) {
				$msg .= '<div class="erreur">Veuillez renseigner un nom de 3 à 20 caractères.<br> Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.</div>';
			}
		} else {
			$msg .= '<div class="erreur">Nom : caractères non accentués, chiffres, "-", "_" et ".".</div>';
		}
	} else {
		$msg .= '<div class="erreur">Veuillez renseigner un nom !</div>';
	}

		

	// VERIFICATION DU PRENOM
	if (!empty($_POST['prenom'])) {
		if (verif_regex('carac_nbr', $_POST['nom'])) {
			if (strlen($_POST['prenom']) < 3 || strlen($_POST['prenom']) > 20) {
				$msg .= '<div class="erreur">Veuillez renseigner un prenom de 3 à 20 caractères.<br> Seuls les caratères non accentués, les chiffres, "-", "_" et "." sont acceptés.</div>';
			}
		} else {
			$msg .= '<div class="erreur">Prenom : caractères non accentués, chiffres, "-", "_" et ".".</div>';
		}
	} else {
		$msg .= '<div class="erreur">Veuillez renseigner un prenom !</div>';
	}
		

	// VERIFICATION DE L'EMAIL
	if (!empty($_POST['email'])) {
		$regex_email = '/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/';
		if(!preg_match($regex_email, $_POST['email'])) {
			$msg .= '<div class="erreur">Veuillez renseigner un email valide !</div>';
		}
	} else {
		$msg .= '<div class="erreur">Veuillez renseigner un email !</div>';
	}
		

	// VERIFICATION DE LA CIVIVITE
	if (empty($_POST['civilite']) || ($_POST['civilite'] != 'm' && $_POST['civilite'] != 'f')) {
		$msg .= '<div class="erreur">Petit malin vous ne m\'aurez pas !</div>';
	}

	// ********
	// INSÉRER L'UTILISATEUR DANS LA BASE DE DONNÉES
	// ********

	if (empty($msg)) {
		$resultat = $pdo -> prepare("UPDATE membre
			SET pseudo = :pseudo, nom = :nom , prenom = :prenom, email = :email, civilite = :civilite
			WHERE id_membre = :id");

		$resultat -> bindParam(':id', $_POST['id_membre'], PDO::PARAM_INT);

		$resultat -> bindParam(':pseudo',$_POST['pseudo'], PDO::PARAM_STR);

		$resultat -> bindParam(':nom',$_POST['nom'], PDO::PARAM_STR);

		$resultat -> bindParam(':prenom',$_POST['prenom'], PDO::PARAM_STR);

		$resultat -> bindParam(':email',$_POST['email'], PDO::PARAM_STR);

		$resultat -> bindParam(':civilite',$_POST['civilite'], PDO::PARAM_STR);
		
		$resultat -> execute();
		
		$id_last_insert = $pdo -> lastInsertId();
		$msg .= '<div class="success"> Les informations ont bien été modifiées </div>';
	}
}
?>

<!-- ####################################### -->
<!-- ######### CONTENU HTML ################ -->
<!-- ####################################### -->

<!-- Modal : MODIFICATION -->
<div class="modal fade" id="modal_modification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	    	<form class="form-horizontal" id="form_inscription" method="post" action="">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel">Modification</h4>
	      		</div>
	      		<div class="modal-body">
					<fieldset>
						<input type="hidden" name="id_membre" value="<?= $id_membre ?>" />

						<!-- PSEUDO -->
	      				<div class="form-group">
	      					<label class="col-md-4 control-label" for="pseudo2">Pseudo :</label>
	      					<div class="col-md-5">
								<input id="pseudo2" type="text" name="pseudo" value="<?php if(isset($_POST['pseudo'])) {echo $_POST['pseudo'];} else{echo $pseudo;}?>" class="form-control input-md" autofocus/>
	      					</div>
						</div>

						<!-- NOM -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="nom">Nom :</label>
							<div class="col-md-5">
								<input id="nom" type="text" name="nom" value="<?php if(isset($_POST['nom'])) {echo $_POST['nom'];} else{echo $nom;} ?>" class="form-control input-md"/>
							</div>
						</div>
						
						<!-- PRENOM -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="prenom">Prénom :</label>
							<div class="col-md-5">
								<input type="text" name="prenom" value="<?php if(isset($_POST['prenom'])) {echo $_POST['prenom'];} else{echo $prenom;} ?>" class="form-control input-md"/>
							</div>
						</div>

						<!-- EMAIL -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="email">Email :</label>
							<div class="col-md-5">
								<input type="text" name="email" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];}else{echo $email;} ?>" class="form-control input-md"/>
							</div>
						</div>

						<!-- CIVILITE -->
						<div class="form-group">
							<label class="col-md-4 control-label" for="civilite">Civilité :</label>
							<div class="col-md-5">
								<select name="civilite" class="form-control"/><br><br>
									<option value="m" <?= ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
									<option value="f" <?= ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
								</select>
							</div>
						</div>
					</fieldset>
	      		</div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
			        <button type="submit" class="btn btn-primary">Enregistrer</button>
			    </div>
	      	</form>
	    </div>
  	</div>
</div> <!-- Fin de la modification de profil -->

<h1>Profil de <?= $pseudo ?></h1> 
<ul>
	<li>Prenom : <?= $prenom ?></li>
	<li>Nom : <?= $nom ?></li>
	<li>Pseudo : <?= $pseudo ?></li>
	<li>Email : <?= $email ?></li>
	<li>Sexe : <?= ($civilite == 'm') ? 'Homme' : 'Femme' ?></li>
	<li>Statut : <?= ($statut == '0') ? 'Visiteur' : 'Admin du site' ?></li>

</ul>
<ul>
	<li><a data-toggle="modal" data-target="#modal_modification"><img src="<?=RACINE_SITE?>img/edit.png" title="Modifier"/>Mettre à jour mes informations</a></li>
	<li><a href="?action=suppression"><img src="<?=RACINE_SITE?>img/delete.png" title="Supprimer"/>Se désinscrire</a></li>
</ul>

<h2>Commandes</h2>
<?php
// ******************************************
// ********AFFICHAGE DES COMMANDES***********
// ******************************************
$req = "SELECT c.date_enregistrement, s.titre, s.photo, s.ville, p.date_arrivee, p.date_depart, p.prix
			FROM commande c, salle s, produit p
			WHERE id_membre = $id_membre
			AND c.id_produit = p.id_produit
			AND s.id_salle = p.id_salle";
$resultat = $pdo -> query($req);


$contenu .= '<table border="1"><tr>';
for($i = 0 ; $i < $resultat -> columnCount() ; $i++){
	$meta = $resultat -> getColumnMeta($i) ;
	$contenu .= '<th>' . $meta['name'] . '</th>' ;
}
$contenu .= '</tr>';

if($resultat -> rowCount() > 0){
	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach ($lignes as $value){
		$contenu .= '<tr>' ;
		foreach ($value as $key2 => $valeur) {
			if($key2 == 'photo'){ // affichage de la photo
				$contenu .= '<td><img src="'.RACINE_SITE.'photo/'.$valeur.'" height="80"/></td>';
			}else if($key2 == 'prix'){
				$contenu .= '<td>' . $valeur . '€</td>' ;
				$prix = $valeur;
			}else{
				$contenu .= '<td>' . $valeur . '</td>' ;
			}
		}
	}
} else {
	$contenu .= '<tr>';
	$contenu .= '<td colspan="7"> Pas de commande. <a href="index.php">Commandez vite !!! </a></td>';
}

$contenu .= '</tr>';
$contenu .= '</table>';


echo $msg;
echo $contenu;


?>
<?php

require_once('inc/footer.inc.php');

?>