<?php
require_once('../inc/init.inc.php');

// Redirection si user n'est pas admin (ACCESSIBILITE/RESTRICTION).
if (!userAdmin()) { // Si userAdmin retourne FALSE(!) (user non amdin), on fait une redirection
	header('location' . RACINE_SITE . 'connexion.php?action=deconnexion');
}

// ENREGISTREMENT ET MODIFICATION DE SALLE
if ($_POST) {

	//debug($_POST);
	//debug($_FILES);// Files récupère les infos des fichiers passés dans le formulaire
	$nom_photo = 'default.jpg'; 

	if(isset($_POST['photo_actuelle'])){
		$nom_photo = $_POST['photo_actuelle'];
		// Si je suis dans le cadre d'une modification de produit, il existe un champs caché, photo_actuelle. Donc par défault la photo va prendre la valeur de la photo actuelle.
	}


	if (!empty($_FILES['photo']['name'])) {
		$nom_photo = $_FILES['photo']['name'];

		$chemin_photo = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $nom_photo;
		// Je recompose donc le chemin absolu de la photo, nom compris.
		//'C:xampp/htdocs/18-1-php-nas/site/photo/ref_parad-moskva.jpg'

		copy($_FILES['photo']['tmp_name'], $chemin_photo); // La fonction copy() me permet de copier un fichier d'un emplacement à un autre. Le premier argument est l'emplacement de base, et le second l'emplacement final.
		// L'emplacement final est représenté par son chemin absolu, comprenant le nom de la photo.
		if (isset($_POST['photo_actuelle']) && $_POST['photo_actuelle'] != 'default.jpg') {
		$nom_photo = $_POST['photo_actuelle']; // Si je suis dans le cadre d'une modification de salle, il existe un champs caché, photo_actuelle. Donc par défault la photo va prendre la valeur de la photo actuelle.
		$chemin_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $_POST['photo_actuelle'];
		if (file_exists($chemin_photo_a_supprimer)) {
			unlink($chemin_photo_a_supprimer);
		}
	}
}

	//Insertion des infos dans la BDD

	// 80 à 100 lignes de vérifications en temps normal (cf page inscription)
	//debug($_POST);
	if (isset($_POST['Modifier']) || (isset($_GET['action']) && $_GET['action'] == 'modification')) {   /* A MODIFIER POTENTIELLEMENT */

		$resultat = $pdo -> prepare("REPLACE INTO salle VALUES (:id, :titre, :description, :photo, :adresse, :cp, :ville, :pays, :capacite, :categorie)");

		$resultat -> bindParam(':id', $_POST['id_salle'], PDO::PARAM_INT);
	}

	else {
		$resultat = $pdo -> prepare("INSERT INTO salle (titre, description,  photo, pays, ville, adresse, cp, capacite, categorie) VALUES (:titre, :description, :photo, :pays, :ville, :adresse, :cp, :capacite, :categorie)");
	}

	


	// STR 
	$resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
 	$resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);  
 	$resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR); 
 	$resultat -> bindParam(':pays', $_POST['pays'], PDO::PARAM_STR); 
 	$resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR); 
 	$resultat -> bindParam(':photo', $nom_photo, PDO::PARAM_STR);
 	$resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR); 

 	//INT  
 	$resultat -> bindParam(':cp', $_POST['cp'], PDO::PARAM_INT);
 	$resultat -> bindParam(':capacite', $_POST['capacite'], PDO::PARAM_INT);

 	$resultat -> execute();

 	$_GET['action'] = 'affichage'; // retour )à l'affichage des salles.
 	$id_last_insert = $pdo -> lastInsertId();
 	$msg .= '<div class="alert alert-success" role="alert">La salle ' . $id_last_insert . ' a été ajoutée avec succès !</div>';
}

// SUPPRESSION DE salle
if (isset($_GET['action']) && $_GET['action'] == 'suppression') {
	// Si il est demandé de faire une action de suppression , je vérifie qu'il y a égalment un id et que cet id soit un entier "is_numeric()""
	if(isset($_GET['id']) && is_numeric($_GET['id'])){
		// On vérifie qu'il y a egalement un ID et que cet ID soit un entier
		$resultat = $pdo -> prepare("SELECT * FROM salle WHERE id_salle = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();

		if ($resultat -> rowCount() > 0) {
		// Si le salle existe bien dans la BDD...
		$salle = $resultat -> fetch(PDO::FETCH_ASSOC);

		
		// Je supprime sa ou ses photos du serveur
		$chemin_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $salle['photo']; // Je reconstitue le chemin absolu de la photo à supprimer.
		if (!empty($salle['photo']) && file_exists($chemin_photo_a_supprimer)) {// Si la photo existe en BDD et dans mon serveur alors je peux la supprimer grâce à la fonction unlink().
			unlink($chemin_photo_a_supprimer);
		}

		// Je supprime l'enregistrement
		$resultat = $pdo -> exec("DELETE FROM salle WHERE id_salle = $salle[id_salle]");
		$get['action'] = 'affichage';
		/*header('location:gestion_salle.php?action=affichage');*/
		$msg .= '<div class="alert alert-success" role="alert">La salle N°' . $salle['id_salle'] . ' a bien été supprimée !</div>';
		
		}
	}
}
	

// Lien d'action (sous-menu)
	// Utilité du sous menu, savoir si on affichage les salles ou le formulaire
$contenu .= '<h1>Gestion des salles</h1>';
$contenu .= '<a class="open_formulaire" href="?action=ajout"><button class="btn btn-default" type="submit">
					Ajouter une salle
				</button></a>';

// AFFICHAGE DES salleS
/*if (isset($_GET['action'])) {*/

	// Récupérer les infos de tous les salles dans la BDD

	// Faire des boucles pour afficher un tableau
	$resultat = $pdo -> query("SELECT * FROM salle");

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
			if($indice2 == 'photo'){
				$contenu .= '<td><a href="'.RACINE_SITE.'photo/'.$valeur2.'" data-fancybox="images"><img src="' . RACINE_SITE . 'photo/' . $valeur2 .  '" height="80"/></a></td>';
			}
			else if ($indice2 == "description") {
				$contenu .= '<td>'.coupure_texte(80, $valeur2).'<a tabindex="0" role="button" data-toggle="popover" data-container="body" data-trigger="focus" title="Description" data-content="'.$valeur2.'"> Voir plus</a></td>';
			}
			else{
				$contenu .= '<td>' . $valeur2 . '</td>';
			}
			// Si l'info que l'on parcourt est l'image du salle on ne souhaite pas seulement l'écrire mais l'afficher dans une balise img ( dans l'attribut src)


		}
			$contenu .= '<td><a class="open_formulaire" href="?action=modification&id=' . $valeur['id_salle'] . '"><img src="' . RACINE_SITE . 'img/edit.png" /></a></td>';
			$contenu .= '<td><a href="?action=suppression&id=' . $valeur['id_salle'] . '"><img src="' . RACINE_SITE . 'img/delete.png" /></a></td>';
			// Dans le sliens de modification et de suppression, il est impératif d'ajouter l'id_salle ($valeur['id_salle]) afin de savoir quel est le salle à supprimer et à modifier.
		

		$contenu .= '</tr>';
	}
		$contenu .= '</table>';
/*}*/
		$contenu .= '<br><br>';
		$contenu .= '<a class="open_formulaire" href="?action=ajout"><button class="btn btn-default" type="button">
					Ajouter une salle
				</button></a>';

	    //AFFICHAGE DU FORMULAIRE (action = ajout, modification)
		

		

$page = 'Gestion Salles';
require_once('../inc/header.inc.php');

echo $msg;
echo $contenu;

//if(isset($_GET['action']) && ($_GET['action'] == 'ajout' && $_GET['action'] == 'modification')) {
			// On me demande soit d'ajouter soit de modifier un salle, je peux donc afficher le formulaire
		// je ne ferme pas mon if donc tout le html sera ffiché si j'entre dans le if.

	if (isset($_GET['id']) && (!empty($_GET['id']))) {// Signifie que nous sommes dans une action de modification
		$resultat = $pdo -> prepare("SELECT * FROM salle WHERE id_salle = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();

		$salle_actuelle = $resultat -> fetch(PDO::FETCH_ASSOC);

	}


	$titre = (isset($salle_actuelle)) ? $salle_actuelle['titre'] : '';
	// Affectation en même temps que condition

	$titre = (isset($salle_actuelle)) ? $salle_actuelle['titre'] : '';
	$categorie = (isset($salle_actuelle)) ? $salle_actuelle['categorie'] : '';
	$description = (isset($salle_actuelle)) ? $salle_actuelle['description'] : '';
	$pays = (isset($salle_actuelle)) ? $salle_actuelle['pays'] : '';
	$ville = (isset($salle_actuelle)) ? $salle_actuelle['ville'] : '';
	$adresse = (isset($salle_actuelle)) ? $salle_actuelle['adresse'] : '';
	$photo = (isset($salle_actuelle)) ? $salle_actuelle['photo'] : '';
	$cp = (isset($salle_actuelle)) ? $salle_actuelle['cp'] : '';
	$capacite = (isset($salle_actuelle)) ? $salle_actuelle['capacite'] : '';

	$id_salle = (isset($salle_actuelle)) ? $salle_actuelle['id_salle'] : '';
	$submit = (/*isset($salle_actuelle)*/ isset($_GET['action']) && $_GET['action'] == 'modification') ? 'Modifier' : 'Enregistrer';



//}

?>

<!-- HTML -->

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<!-- CHAMPS CACHES -->
		<input class="form-control" type="hidden" id="id_salle" name="id_salle" value="<?= $id_salle ?>"/>

		<div class="col-md-5 col-md-offset-1 col-sm-6">
			<!-- TITRE -->
        	<div class="form-group">
            	<label class="col-sm-4 control-label">Titre :</label>
            	<div class="col-sm-8">
            		<div class="input-group">
			      		<span class="input-group-addon glyphicon glyphicon-tag"></span>
            			<input class="form-control" type="text" id="titre" name="titre" value="<?= $titre ?>"/>
			      	</div>
			    </div>
        	</div>

        	<!-- DESCRIPTION -->
        	<div class="form-group">
            	<label class="col-sm-4 control-label">Description :</label>
            	<div class="col-sm-8">
            		<textarea class="form-control" name="description"><?= $description ?></textarea>
            	</div>
        	</div>

        	<!-- PHOTO -->
        	<div class="form-group">
        		<?php 
					if (isset($salle_actuelle)) { ?>
						<label class="col-sm-4 control-label" for="photo_actuelle">Photo actuelle :</label>
						<div class="col-sm-8">
							<a href="<?= RACINE_SITE.'photo/'.$photo ?>" data-fancybox="images">
								<img src="<?= RACINE_SITE.'photo/'.$photo ?>" width="100">
							</a>
						</div>
						<input type="hidden" name="photo_actuelle" value="<?= $photo ?>"/>
				<?php }
				?>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="photo">Photo :</label>
				<div class="col-sm-8">
					<input type="file" name="photo" id="photo" value="<?= $photo ?>">
				</div>
			</div>

			<!-- CAPACITE -->
        	<div class="form-group">
            	<label class="col-sm-4 control-label">Capacité :</label>
            	<div class="col-sm-8">
            		<div class="input-group">
            			<span class="input-group-addon glyphicon glyphicon-blackboard"></span>
            			<input class="form-control" type="text" name="capacite" value="<?= $capacite ?>"/>
            		</div>
            	</div>           	
        	</div>        	
        </div>

        <div class="col-md-5 col-sm-6">
        	<!-- CATEGORIE -->
        	<div class="form-group">
            	<label class="col-sm-4 control-label">Catégorie :</label>
            	<div class="col-sm-8">
            		<div class="input-group">
            			<span class="input-group-addon glyphicon glyphicon glyphicon-bookmark"></span>
            			<select id="categorie" name="categorie" class="form-control">
            				<?php
			    			$resultat = $pdo -> query("SELECT DISTINCT categorie FROM salle");
							$categories = $resultat -> fetchAll(PDO::FETCH_ASSOC);
							foreach ($categories as $key => $valeur_categorie) {
								echo '<option value="'.$valeur_categorie['categorie'].'"';
								if(isset($salle_actuelle) && $categorie == $valeur_categorie['categorie']){
									echo 'selected';}else{ echo ''; }
									echo '>'.$valeur_categorie['categorie'].'</option>';
							}
			      		?>
            			</select>
            		</div>
            	</div>           	
        	</div>

            <!-- ADRESSE -->
            <div class="form-group">	
                <label class="col-sm-4 control-label" for="adresse">Adresse :</label>
                <div class="col-sm-8">
                	<div class="input-group">
                		<span class="input-group-addon glyphicon glyphicon-globe"></span>
                    	<input id="adresse" class="form-control" type="text" name="adresse" value="<?= $adresse ?>"/>
                	</div>
                </div>
            </div>

            <!-- VILLE -->
            <div class="form-group">	
                <label class="col-sm-4 control-label" for="ville">Ville :</label>
                <div class="col-sm-8">
                	<div class="input-group">
                		<span class="input-group-addon glyphicon glyphicon-globe"></span>
                    	<input id="ville" class="form-control" type="text" name="ville" value="<?= $ville ?>"/>
                	</div>
                </div>
            </div>

            <!-- CODE POSTAL -->
            <div class="form-group">	
                <label class="col-sm-4 control-label" for="cp">Code postal :</label>
                <div class="col-sm-8">
                	<div class="input-group">
                		<span class="input-group-addon glyphicon glyphicon-globe"></span>
                    	<input id="cp" class="form-control" type="text" name="cp" value="<?= $cp ?>"/>
                	</div>
                </div>
            </div>

        	<!-- PAYS -->
        	<div class="form-group">
                <label class="col-sm-4 control-label" for="pays">Pays :</label>
                <div class="col-sm-8">
                	<div class="input-group">
                		<span class="input-group-addon glyphicon glyphicon-globe"></span>
                    	<input id="pays" class="form-control" type="text" name="pays" value="<?= $pays ?>"/>
                	</div>
                </div>
            </div>
        </div><!-- fin col-md-6-->

                <!--<div class="col-sm-8">
                	<button type="submit" class="btn btn-default navbar-btn" value="<?= $submit ?>"><a href="?action=enregistrer&id=<?=  $valeur['id_salle'] ?>">Enregistrer</a></button>
                </div>-->
        <div class="form-group">
	        <div class="col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
	            <input type="submit" name="<?= $submit ?>" class="form-control" value="<?= $submit ?>">
			</div>
		</div>

            

        
    </fieldset>
</form>


<?php


require_once('../inc/footer.inc.php');
?>