<?php

require_once('../inc/init.inc.php');

// REDIRECTION SI USER PAS ADMIN ! (ACCESSIBILITE/RESTRICTION)
if(!userAdmin()){ 
	header('location:../index.php');
}

// ENREGISTREMENT ET MODIFICATION DE membre

if($_POST){
	
	//debug($_POST); 
	//debug($_FILES); 
	
	
	//INSERTION DES INFOS DU membre DANS LA BDD
	
	if(!empty($_POST['mdp'])){
	
		
		if(isset($_GET['action']) && $_GET['action']  == 'modification' && !empty($_GET['id'])){
			$resultat = $pdo -> prepare("REPLACE INTO membre  VALUES (:id, :pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut, :date_enregistrement)");
			
			$resultat -> bindParam(':id', $_POST['id_membre'], PDO::PARAM_INT);
            $resultat -> bindParam(':date_enregistrement', $_POST['date_enregistrement'], PDO::PARAM_INT);
            
			
		}
		else{
			$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut, NOW())");
		}
		
		
		//STR
        $resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		
		$_POST['mdp'] = md5($_POST['mdp']);
		$resultat -> bindParam(':mdp', $_POST['mdp'], PDO::PARAM_STR);
		$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
		$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
		$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
		$resultat -> bindParam(':statut', $_POST['statut'], PDO::PARAM_INT);

        $resultat -> execute();
		
		$_GET['action'] = 'affichage'; // retour à l'affichage des membres.
		$id_last_insert = $pdo -> lastInsertId(); 
		$msg .= '<div class="validation">Le membre ' . $id_last_insert . ' a été ajouté avec succès !</div>'; 
	}
	else{
		$msg .= '<div class="erreur">Il faut obligatoirement préciser un mot de passe !</div>';
	}
}

// SUPPRESSION DE membre
if(isset($_GET['action']) && $_GET['action'] == 'suppression' ){
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])){
		// Je supprime l'enregistrement
		$resultat = $pdo -> prepare("DELETE FROM membre WHERE id_membre = :id"); 
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();
		
		$_GET['action'] = 'affichage';
		header('location:gestion_membre.php?action=affichage'); 
		$msg .= '<div class="validation">Le membre N°' . $membre['id_membre'] . ' a bien été supprimé !</div>';
	}
}


// Lien d'actions (sous-menu)
$contenu .= '<h1>Gestion des membres</h1>';
$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter un membre
				</button></a>';

// AFFICHAGE DES membres

	
	// Récupérer les infos de tous les membres dans la BDD
	$resultat = $pdo -> query("SELECT * FROM membre"); 
	
	// Faire des boucles pour afficher un tableau
	$contenu .= '<br/><br/>';
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i=0; $i < $resultat -> columnCount(); $i++ ){ 
		$meta = $resultat -> getColumnMeta($i); 
		if($meta['name'] != 'mdp'){
			$contenu .= '<th>' . $meta['name'] . '</th>';
		}
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';
	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach($lignes as $valeur){
		$contenu .= '<tr>';
		foreach($valeur as $indice2 => $valeur2){
			if($indice2 != 'mdp'){
				$contenu .= '<td style="padding: 20px">' . $valeur2 . '</td>';
			}
			
		}
		$contenu .= '<td><a href="?action=modification&id=' . $valeur['id_membre'] . '"><img src="' . RACINE_SITE . 'img/edit.png" /></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $valeur['id_membre'] . '"><img src="' . RACINE_SITE . 'img/delete.png" /></a></td>';
		
		$contenu .= '</tr>';
	}
	$contenu .= '</table>';

$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter un membre
				</button></a>';

$page = 'Gestion Membre';
require_once('../inc/header.inc.php');
echo $msg;
echo $contenu;


// AFFICHAGE DU FORMULAIRE (action = ajout, modification)


if(isset($_GET['action']) && (($_GET['action'] == 'ajout') ||   ($_GET['action'] == 'modification'))){
	
	
	if(isset($_GET['id'])){
	
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute(); 
		
		$membre_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
	}
    
}    
	
	$pseudo =(isset($membre_actuel)) ? $membre_actuel['pseudo'] : '';
	// Affectation en même temps que condition...
	$nom = (isset($membre_actuel)) ? $membre_actuel['nom'] : '';
	$prenom = (isset($membre_actuel)) ? $membre_actuel['prenom'] : '';
	$email = (isset($membre_actuel)) ? $membre_actuel['email'] : '';
	$civilite = (isset($membre_actuel)) ? $membre_actuel['civilite'] : '';
	$statut = (isset($membre_actuel)) ? $membre_actuel['statut'] : '';
    $id_membre = (isset($membre_actuel)) ? $membre_actuel['id_membre'] : '';
    $date_enregistrement = (isset($membre_actuel)) ? $membre_actuel['date_enregistrement'] : '';
    $submit = (isset($membre_actuel)) ? 'Modifier' :'Enregistrer';

?>
<!-- HTML -->
<br/><br/>
  

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<!-- l'attribut enctype permet de gérer les fichier upload grâce à la superglobale $_FILES -->
	<fieldset>
   		<!-- CHAMPS CACHES -->
		<input type="hidden" name="id_membre" value="<?= $id_membre ?>" />
   		<input type="hidden" name="date_enregistrement" value="<?= $date_enregistrement ?> ">
    
    	<div class="col-md-5 col-md-offset-1 col-sm-6">
	    	<!-- PSEUDO -->
	    	<div class="form-group">
			  	<label class="col-sm-4 control-label" for="pseudo">Pseudo : </label>
			  	<div class="col-sm-8">
			    	<div class="input-group">
			      		<span class="input-group-addon glyphicon glyphicon-user"></span>
			      		<input type="text" id="pseudo" name="pseudo" class="form-control value="<?= $pseudo ?>">
			    	</div>
			  	</div>
			</div>

			 <!-- MOT DE PASSE -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="mdp">Mot de passe : </label>
				<div class="col-sm-8">
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-lock"></span>
						<input class="form-control" type="password" name="mdp" id="mdp">
					</div>
				</div>
			</div>

			<!-- Nom -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="nom">Nom : </label>
				<div class="col-sm-8">
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-user"></span>
						<input class="form-control"  id="nom" type="text" name="nom" value="<?= $nom ?>">
					</div>
				</div>
			</div>

			<!-- PRENOM -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="prenom">Prénom : </label>
				<div class="col-sm-8">
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-user"></span>
						<input class="form-control" id="prenom" type="text" name="prenom" value="<?= $prenom ?>">
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-5 col-sm-6">
			<!-- EMAIL -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="email">Email :</label>
				<div class="col-sm-8">
					<div class="input-group">
						<span class="input-group-addon glyphicon glyphicon-envelope"></span>
						<input type="email" class="form-control" name="email" value="<?= $email ?>"/>
					</div>
				</div>
			</div>

			<!-- Civilite -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="civilite">Civilite :</label>
				<div class="col-sm-8">
					<select name="civilite" id="civilite" class="form-control">
						<option value="m" <?= (isset($membre_actuel) && $civilite == 'm') ? 'selected' : '' ?>>Homme</option>
						<option value="f" <?= (isset($membre_actuel) && $civilite == 'f') ? 'selected' : '' ?>>Femme</option>
					</select>
				</div>
			</div>
			
			<!-- STATUT -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="statut">Statut :</label>
				<div class="col-sm-8">
					<select name="statut" id="statut" class="form-control">
						<option value="0" <?= (isset($membre_actuel) && $statut == 0) ? 'selected' : '' ?>>Membre</option>
						<option value="1" <?= (isset($membre_actuel) && $statut == 1) ? 'selected' : '' ?>>Admin</option>
					</select>
				</div>
			</div>
		
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<input type="submit" class="form-control" value="<?= $submit ?>">
				</div>
			</div>
		</div>
	</fieldset> 
</form>



<?php
 // fermeture du if
require_once('../inc/footer.inc.php');
?>