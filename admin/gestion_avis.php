<?php 
	require_once('/../inc/init.inc.php');

	// Redirection si USER pas un ADMIN
	if(!userAdmin()){ 
		header('location:'.RACINE_SITE.'connexion.php?action=deconnexion');
	}
	/*--------------------------------------
			HEADER DU SITE
	--------------------------------------*/
	$page = 'Gestion Avis';
	require_once('/../inc/header.inc.php');


	/*--------------------------------------
			AFFICHAGE DES AVIS
	--------------------------------------*/
	$contenu .= '<h1>Gestion des avis</h1>';
	$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter un avis
				</button></a>';

	// Récupérer tous les avis
	$resultat = $pdo -> query("SELECT * FROM avis");

	$contenu .= '<br><br>';
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';

	// Ligne de titre
	for ($i=0; $i < $resultat -> columnCount(); $i++) {// columCount() me retourne le nombre de colonne dans ma table
	
		$meta = $resultat -> getColumnMeta($i); // Me retourne les infos de chaque colonne

		$contenu .= '<th style="padding : 10px">' . $meta['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';

	$contenu .= '</tr>';

	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach ($lignes as $valeur) {
		$contenu .= '<tr style="padding : 10px">';
			
				

			
		foreach($valeur as $indice2 => $valeur2){
			// Si l'info que l'on parcourt est la note, on ne souhaite pas seulement l'écrire mais afficher le nombre d'étoiles correspondant
			if($indice2 == 'note'){
				$contenu .= '<td>';
				for($star = 1 ; $star <= $valeur2; $star++){
					$contenu .= '<span class="glyphicon glyphicon-star"></span>';
				}
				$contenu .= '</td>';
			}
			else{
				$contenu .= '<td style="padding : 10px">' . $valeur2 . '</td>';
			}
		}
			$contenu .= '<td><a href="?action=modification&id=' . $valeur['id_salle'] . '"><img src="' . RACINE_SITE . 'img/edit.png" /></a></td>';
			$contenu .= '<td><a href="?action=suppression&id=' . $valeur['id_salle'] . '"><img src="' . RACINE_SITE . 'img/delete.png" /></a></td>';
			// Dans le sliens de modification et de suppression, il est impératif d'ajouter l'id_salle ($valeur['id_salle]) afin de savoir quel est le salle à supprimer et à modifier.
		

		$contenu .= '</tr>';
	}
		$contenu .= '</table>';
/*}*/
		$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter un avis
				</button></a>';

	echo $contenu;
 ?>
<!-- ######################################### -->
<!-- ########### CONTENU HTML ################ -->
<!-- ######################################### -->


 <?php
 	/*--------------------------------------
			FOOTER DU SITE
	--------------------------------------*/ 
	require_once('/../inc/footer.inc.php');
 ?>