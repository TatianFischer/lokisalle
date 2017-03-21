<?php 
	require_once('/../inc/init.inc.php');


	// Redirection si USER pas un ADMIN
	if(!userAdmin()){ 
		header('location:'.RACINE_SITE.'connexion.php?action=deconnexion');
	}
	/*--------------------------------------
			HEADER DU SITE
	--------------------------------------*/
	$page = 'Gestion Produits';
	require_once('/../inc/header.inc.php');



	/*--------------------------------------
			INSERTION / MODIFICATION
	--------------------------------------*/ 

	if($_POST){
		// ******
		// VERIFICATION DES INFOS DU PRODUIT AVANT INSERTION
		// ******

		// ******
		// INSERTION DES INFOS DU PRODUIT DANS LA BDD
		// ******

		if(empty($msg)){
			if (isset($_GET['action']) && $_GET['action'] == "modification" && !empty($_GET['id'])) {
				// Modification
				$resultat = $pdo -> prepare("REPLACE INTO produit VALUES (:id, :id_salle, :date_arrivee, :date_depart, :prix, :etat)");
				$resultat -> bindParam(':id', $_POST['id_produit'], PDO::PARAM_INT);

			} else {
				// Ajout
				$resultat = $pdo -> prepare("INSERT INTO produit (id_salle, date_arrivee, date_depart, prix, etat) VALUES (:id_salle, :date_arrivee, :date_depart, :prix, :etat)");
			}

			$resultat -> bindParam(':id_salle', $_POST['salle'], PDO::PARAM_STR);
			$date_arrivee = $_POST['jour_arrivee'].' '.$_POST['heure_arrivee'];
			$resultat -> bindParam(':date_arrivee', $date_arrivee, PDO::PARAM_STR);
			$date_depart = $_POST['jour_depart'].' '.$_POST['heure_depart'];
			$resultat -> bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
			$resultat -> bindParam(':prix', $_POST['tarif'], PDO:: PARAM_INT);
			$resultat -> bindParam(':etat', $_POST['etat'], PDO:: PARAM_STR);

			$resultat -> execute();

			$_GET['action'] = "";
			$msg .= '<div class="validation"> Le produit a bien été ajouté/modifier </div>';
		}
}




	/*--------------------------------------
			AFFICHAGE DES PRODUITS
	--------------------------------------*/
	$contenu .= '<h1>Gestion des produits</h1>';
	$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter un produit
				</button></a>';

	// Récupérer tous les avis
	$resultat = $pdo -> query("SELECT * FROM produit");

	$contenu .= '<br><br>';
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';

	// Ligne de titre
	for ($i=0; $i < $resultat -> columnCount(); $i++) {// columCount() me retourne le nombre de colonne dans ma table
	
		$meta = $resultat -> getColumnMeta($i); // Me retourne les infos de chaque colonne

		$contenu .= '<th>' . $meta['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';

	$contenu .= '</tr>';

	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach ($lignes as $valeur) {
		$contenu .= '<tr>';
				
		foreach($valeur as $indice2 => $valeur2){
			// Si l'info que l'on parcourt est la note, on ne souhaite pas seulement l'écrire mais afficher le nombre d'étoiles correspondant
			if($indice2 == 'prix'){
				$contenu .= '<td>' . $valeur2 . ' €</td>';
			} else if($indice2 == 'id_salle'){
				$resultat = $pdo -> query("SELECT * FROM  salle WHERE id_salle = $valeur2");
				$salle = $resultat -> fetch(PDO::FETCH_ASSOC);
				
				$contenu .= '<td>';
				$contenu .= $valeur2.' - '.$salle['titre'];
				$contenu .= '<br><a href="'.RACINE_SITE.'photo/'.$salle['photo'].'" data-fancybox="images"><img src="' . RACINE_SITE . 'photo/' . $salle['photo'] .  '" height="80"/></a>';
				$contenu .= '</td>';
			}
			else{
				$contenu .= '<td>' . $valeur2 . '</td>';
			}
		}
			$contenu .= '<td><a href="?action=modification&id=' . $valeur['id_produit'] . '"><img src="' . RACINE_SITE . 'img/edit.png" /></a></td>';
			$contenu .= '<td><a href="?action=suppression&id=' . $valeur['id_produit'] . '"><img src="' . RACINE_SITE . 'img/delete.png" /></a></td>';
			// Dans les liens de modification et de suppression, il est impératif d'ajouter l'id_salle ($valeur['id_salle]) afin de savoir quel est le salle à supprimer et à modifier.
		

		$contenu .= '</tr>';
	}
		$contenu .= '</table>';
		$contenu .= '<a href="?action=ajout"><button type="submit" class="btn btn-default">Ajouter un produit </button></a>';
		$contenu .= '<br><br>';

	echo $contenu;
	echo $msg;


	if(isset($_GET['id']) && (!empty($_GET['id']))){ // On fait une modification
		$resultat = $pdo -> prepare("SELECT prix, id_salle, id_produit, date_format(date_arrivee, '%Y-%m-%d') as jour_arrivee, date_format(date_arrivee, '%T') as heure_arrivee, date_format(date_depart, '%Y-%m-%d') as jour_depart, date_format(date_depart, '%T') as heure_depart, etat FROM produit WHERE id_produit = :id");

		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();

		$produit_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
	}
		$jour_arrivee = (isset($produit_actuel)) ? $produit_actuel['jour_arrivee'] : '';
		$heure_arrivee = (isset($produit_actuel)) ? $produit_actuel['heure_arrivee'] : '';
		$jour_depart = (isset($produit_actuel)) ? $produit_actuel['jour_depart'] : '';
		$heure_depart = (isset($produit_actuel)) ? $produit_actuel['heure_depart'] : '';
		$tarif = (isset($produit_actuel)) ? $produit_actuel['prix'] : '';
		$salle_actuelle = (isset($produit_actuel)) ? $produit_actuel['id_salle'] : '';
		$etat = (isset($produit_actuel)) ? $produit_actuel['etat'] : '';

		$id_produit = (isset($produit_actuel)) ? $produit_actuel['id_produit'] : '';
		$submit = (isset($produit_actuel)) ? 'Modifier' : 'Enregistrer';
 ?>

<!-- ######################################### -->
<!-- ########### CONTENU HTML ################ -->
<!-- ######################################### -->
<form class="form-horizontal" method="post">
	<fieldset>
		<input type="hidden" name="id_produit" value="<?= $id_produit; ?>">

		<div class="col-md-5 col-md-offset-1 col-sm-6">
			<!-- Arrivée-->
			<div class="form-group">
			  	<label class="col-sm-4 control-label" for="arrive">Date d'arrivée :</label>
			  	<div class="col-sm-8">
			    	<div class="input-group" id="arrivee">
			    		<span class="input-group-addon glyphicon glyphicon-calendar"></span>
			      		<input name="jour_arrivee" class="form-control" type="date" value="<?= $jour_arrivee ?>">
			      		<span class="input-group-addon glyphicon glyphicon-hourglass"></span>
			    		<input name="heure_arrivee" class="form-control" type="time" value="<?= $heure_arrivee ?>">
			    	</div>
			  	</div>
			</div>

			<!-- Départ-->
			<div class="form-group">
			  	<label class="col-sm-4 control-label" for="depart">Date de départ :</label>
			  	<div class="col-sm-8">
			    	<div class="input-group" id="depart">
			      		<span class="input-group-addon glyphicon glyphicon-calendar"></span>
			      		<input name="jour_depart" class="form-control" type="date" value="<?= $jour_depart ?>">
			      		<span class="input-group-addon glyphicon glyphicon-hourglass"></span>
			      		<input name="heure_depart" class="form-control" type="time" value="<?= $heure_depart ?>">
			    	</div>
			  	</div>
			</div>
			<!-- Tarif -->
			<div class="form-group">
			  	<label class="col-sm-4 control-label" for="tarif">Tarif :</label>
			  	<div class="col-sm-8">
				    <div class="input-group">
				      	<span class="input-group-addon glyphicon glyphicon-euro"></span>
				      	<input id="tarif" name="tarif" class="form-control" type="text" value="<?= $tarif ?>">
				    </div>
			  	</div>
			</div>
		</div>

		<div class="col-md-5 col-sm-6">
			<!-- SALLE -->
			<div class="form-group">
			  	<label class="col-sm-4 control-label" for="salle">Salle :</label>
			  	<div class="col-sm-8">
			    	<select id="salle" name="salle" class="form-control">
			    		<?php
			    		$resultat = $pdo -> query("SELECT * FROM  salle");
						$salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
						foreach ($salles as $key => $salle) {
							echo '<option value="'.$salle['id_salle'].'"';
							if(isset($produit_actuel) && $salle_actuelle == $salle['id_salle']){
								echo 'selected';
							} else {
								echo '';
							}
							echo '>'.$salle['id_salle'].' - '.$salle['titre'].' - '.$salle['capacite'].' personnes</option>';
						}
			      		?>
			    	</select>
			  	</div>
			</div>

			<!-- ETAT -->
			<div class="form-group">
				<label class="col-sm-4 control-label" for="etat">Etat :</label>
				<div class="col-sm-8">
					<select id="etat" name="etat" class="form-control">
						<option value="libre" <?= (isset($produit_actuel) && $etat == "libre") ? 'selected' : '' ?>>Libre</option>
						<option value="réservation" <?= (isset($produit_actuel) && $etat == "résevation") ? 'selected' : '' ?>>Réservation</option>
					</select>
				</div>
			</div>
			<!-- Button -->
			<div class="form-group">
		  		<div class="col-sm-8 col-sm-offset-4">
		    		<input type="submit" name="<?= $submit ?>" class="form-control" value="<?= $submit ?>">
		  		</div>
			</div>
		</div>

	</fieldset>
</form>




 <?php
 	/*--------------------------------------
			FOOTER DU SITE
	--------------------------------------*/ 
	require_once('/../inc/footer.inc.php');
 ?>