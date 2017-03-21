<?php
require_once('../inc/init.inc.php');

if(!userAdmin()){
	header('location:../index.php');
}

/*--------------------------------------
			HEADER DU SITE
	--------------------------------------*/
$page = 'Gestion commande';
require_once('../inc/header.inc.php');

if($_POST){
	if(empty($msg)){
		if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id'])){
			$resultat = $pdo -> prepare("REPLACE INTO commande VALUES (:id, :membre, :produit, :date_enregistrement)");
			$resultat -> bindParam(':id', $_POST['id_commande'], PDO::PARAM_INT);
		} else {	
			$etat_salle = $pdo -> prepare("UPDATE produit SET etat = 'reservation' WHERE id_produit = :produit");
			$etat_salle -> bindParam(':produit', $_POST['produit'], PDO::PARAM_INT);
			$etat_salle -> execute();
			
			$resultat = $pdo -> prepare("INSERT INTO commande (id_membre, id_produit, date_enregistrement) VALUES (:membre, :produit, :date_enregistrement)");
		}
		
		
		$resultat -> bindParam(':membre', $_POST['membre'], PDO::PARAM_INT);
        $resultat -> bindParam(':produit', $_POST['produit'], PDO::PARAM_INT);
        $date_enregistrement = $_POST['jour'].' '.$_POST['heure'];
		$resultat -> bindParam(':date_enregistrement', $date_enregistrement, PDO::PARAM_INT);
		$resultat -> execute();
		
		unset($_GET); // retour à l'affichage des membres.
		unset($_POST);
		$id_last_insert = $pdo -> lastInsertId();

		//if($_GET['action']) 
		$msg .= '<div class="validation">La commande ' . $id_last_insert . ' a été ajouté avec succès !</div>'; 
	}
}

if(isset($_GET['action']) && $_GET['action'] == 'suppression' ){
	// Si une action est demandée et qu'il s'agit d'une suppression
	if(isset($_GET['id']) && is_numeric($_GET['id'])){
		// Je supprime l'enregistrement
		$resultat = $pdo -> prepare("DELETE FROM commande WHERE id_commande = :id"); 
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();

		// Changemant état de la salle
		$etat_salle = $pdo -> prepare("UPDATE produit SET etat = 'libre' WHERE id_produit = :produit");
		$etat_salle -> bindParam(':produit', $_POST['produit'], PDO::PARAM_INT);
		$etat_salle -> execute();
		
		$_GET['action'] = 'affichage';
		header('location:gestion_commandes.php?action=affichage'); 
		$msg .= '<div class="validation">La commande N°' . $commande['id_commande'] . ' a bien été supprimé !</div>';
	}
}
    
    
 
$contenu .= '<h1>Gestion des commandes</h1>';
$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter une commande
				</button></a>';

	
	// Récupérer les infos de tous les commandes dans la BDD
	$req = "SELECT c.id_commande, c.id_produit, m.pseudo as Pseudo, s.titre as 'Nom de la salle', s.ville, p.prix, date_format(c.date_enregistrement, '%d-%m-%Y') as 'Date'
			FROM membre m, commande c, produit p, salle s 
			WHERE p.id_salle = s.id_salle
			AND c.id_membre = m.id_membre
			AND c.id_produit = p.id_produit";
	$resultat = $pdo -> query($req); 
	
	// Faire des boucles pour afficher un tableau
	$contenu .= '<br/><br/>';
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i=0; $i < $resultat -> columnCount(); $i++ ){ 
		$meta = $resultat -> getColumnMeta($i); 
		if($meta['name'] != 'mdp'){
			$contenu .= '<th>' . $meta['name'] .  '</th>';
		}
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';
	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach($lignes as $valeur){
		$contenu .= '<tr>';
		foreach($valeur as $indice2 => $valeur2){
			if($indice2 == 'prix'){
				$contenu .= '<td>' . $valeur2 . ' €</td>';
			} else if($indice2 == 'id_produit') {
				$req = "SELECT p.id_produit, s.titre, p.prix, date_format(p.date_arrivee, '%d-%m-%Y') as jour_arrivee, date_format(p.date_depart, '%d-%m-%Y') as jour_depart
			    			FROM produit p, salle s
			    			WHERE p.id_salle = s.id_salle
			    			AND p.id_produit = $valeur2";
			    	$resultat = $pdo -> query($req);
			    	$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
			    	$content = $produit['id_produit'].' - '.$produit['titre'].' - '.$produit['prix'].'€ - '.$produit['jour_arrivee'].' - '.$produit['jour_depart'];
				$contenu .= '<td><a tabindex="0" role="button" data-toggle="popover" data-container="body" data-trigger="focus" title="Description" data-content="'.$content.'">'.$valeur2.'</a></td>';
			} else {
				$contenu .= '<td>' . $valeur2 . '</td>';
			}			
		}
		$contenu .= '<td><a href="?action=modification&id=' . $valeur['id_commande'] . '"><img src="' . RACINE_SITE . 'img/edit.png" /></a></td>';
        
		$contenu .= '<td><a href="?action=suppression&id=' . $valeur['id_commande'] . '"><img src="' . RACINE_SITE . 'img/delete.png" /></a></td>';
        
		// Dans les liens de modification et de supprimer, il est impératif d'ajouter l'id_membre ($valeur['id_membre']) afin de savoir quel est le membre à supprimer et à modifier.
		
		$contenu .= '</tr>';
	}
	$contenu .= '</table>';

$contenu .= '<a href="?action=ajout"><button class="btn btn-default type="submit">
					Ajouter une commande
				</button></a>';


echo $msg;
echo $contenu;

if(isset($_GET['id']) && (!empty($_GET['id']))){ // On fait une modification
		$resultat = $pdo -> prepare("SELECT id_commande, id_membre, id_produit, date_format(date_enregistrement, '%Y-%m-%d') as jour, date_format(date_enregistrement, '%T') as heure FROM commande WHERE id_commande = :id");

		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();

		$commande_actuelle = $resultat -> fetch(PDO::FETCH_ASSOC);
		//debug($commande_actuelle);
	}
		$jour = (isset($commande_actuelle)) ? $commande_actuelle['jour'] : date('Y-m-d');
		$heure = (isset($commande_actuelle)) ? $commande_actuelle['heure'] : date('H:i:s');
		$id_commande = (isset($commande_actuelle)) ? $commande_actuelle['id_commande'] : '';
		$id_membre = (isset($commande_actuelle)) ? $commande_actuelle['id_membre'] : '';
		$id_produit = (isset($commande_actuelle)) ? $commande_actuelle['id_produit'] : '';
		$submit = (isset($commande_actuelle)) ? 'Modifier' : 'Enregistrer';

?>

<form class="form-horizontal" method="post">
	<fieldset>
		<input type="hidden" name="id_commande" value="<?= $id_commande; ?>">

		<!-- Date d'enregistrement -->
		<div class="form-group">
		  	<label class="col-sm-4 control-label" for="enregistrement">Date d'enregistrement :</label>
		  	<div class="col-sm-5">
		    	<div class="input-group" id="enregistrement">
		    		<span class="input-group-addon glyphicon glyphicon-calendar"></span>
		      		<input name="jour" class="form-control" type="date" value="<?= $jour ?>">
		      		<span class="input-group-addon glyphicon glyphicon-hourglass"></span>
		    		<input name="heure" class="form-control" type="time" value="<?= $heure ?>">
		    	</div>
		  	</div>
		</div>
		
		<!-- Membre -->
		<div class="form-group">
		  	<label class="col-sm-4 control-label" for="membre">Membre :</label>
		  	<div class="col-sm-5">
		    	<select id="membre" name="membre" class="form-control">
		    		<?php
		    		$req = "SELECT id_membre, pseudo
		    			FROM membre ORDER BY pseudo";

		    		$resultat = $pdo -> query($req);
					$membres = $resultat -> fetchAll(PDO::FETCH_ASSOC);
					foreach ($membres as $key => $membre) {
						echo '<option value="'.$membre['id_membre'].'"';
						if(isset($commande_actuelle) && $id_membre == $membre['id_membre']){
							echo 'selected';
						} else {
							echo '';
						}
						echo '>'.$membre['id_membre'].' - '.$membre['pseudo'].'</option>';
					}
		      		?>
		    	</select>
		  	</div>
		</div>

		<!-- Produit -->
		<div class="form-group">
		  	<label class="col-sm-4 control-label" for="produit">Produit :</label>
		  	<div class="col-sm-5">
		    	<select id="produit" name="produit" class="form-control">
		    		<?php
					//En cas de modif récupération du produit
					if($_GET['action'] == "modification"){
			      		$req = "SELECT p.id_produit, s.titre, p.prix, date_format(p.date_arrivee, '%d-%m-%Y') as jour_arrivee
			    			FROM produit p, salle s
			    			WHERE p.id_salle = s.id_salle
			    			AND p.id_produit = $id_produit";
			    		$resultat = $pdo -> query($req);
			    		$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
			    		echo '<option value="'.$produit['id_produit'].'" selected>'.$produit['id_produit'].' - '.$produit['titre'].' - '.$produit['prix'].'€ - '.$produit['jour_arrivee'].'</option>';
					}

					// Récupération des produits non réservés
		    		$req = "SELECT p.id_produit, s.titre, p.prix, date_format(p.date_arrivee, '%d-%m-%Y') as jour_arrivee
		    			FROM produit p, salle s
		    			WHERE p.id_salle = s.id_salle
		    			AND etat = 'libre'
		    			-- AND date_arrivee > NOW()"
		    			;

		    		$resultat = $pdo -> query($req);
					$produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
					foreach ($produits as $key => $produit) {
						echo '<option value="'.$produit['id_produit'].'"';
						if(isset($commande_actuelle) && $id_produit == $produit['id_produit']){
							echo 'selected';
						} else {
							echo '';
						}
						echo '>'.$produit['id_produit'].' - '.$produit['titre'].' - '.$produit['prix'].'€ - '.$produit['jour_arrivee'].'</option>';
					}

		      		
		      		?>
		    	</select>
		  	</div>
		</div>
		
		<!-- Button -->
		<div class="form-group">
	  		<div class="col-sm-5 col-sm-offset-4">
	    		<input type="submit" name="<?= $submit ?>" class="form-control" value="<?= $submit ?>">
	  		</div>
		</div>
	</fieldset>
</form> 

<?php
require_once('../inc/footer.inc.php');
?>






