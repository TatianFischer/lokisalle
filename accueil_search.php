<?php
require_once('inc/init.inc.php');

if(isset($_POST)){
	//debug($_POST);
	$order = "";

	if(isset($_POST['categorie'])){
		$categories = $_POST['categorie'];
		$i = 0; // Pour afficher OR sauf au dernier tour.
		$order .= "AND ( ";
		foreach ($categories as $categorie) {
			$i++;
			$order .= "categorie = '$categorie'";
			if($i != sizeof($categories)){
				// On affiche OR sauf au dernier tour
				$order .=" OR ";
			}
		}
		$order .= " )";
	}

	if(isset($_POST['ville'])){
		$villes = $_POST['ville'];
		$i=0;
		$order .= "AND (";
		foreach ($villes as $ville) {
			$i++;
			$order .= "ville = '$ville'";
			if($i != sizeof($villes)){
				$order .= " OR ";
			}
		}
		$order .= " )";
	}


	if(!empty($_POST['date_depart'])){
		$date_depart = $_POST['date_depart']." 00:00:00";
		$order .= " AND date_depart <= '$date_depart'";
	} elseif(!empty($_POST['date_arrivee'])){
		$date_arrivee = $_POST['date_arrivee']." 00:00:00";
		$order .= " AND date_arrivee >= '$date_arrivee'";
	}

	$array_capacite = explode(' ', $_POST['capacite']);
	$order .= " AND capacite BETWEEN '$array_capacite[0]' AND '$array_capacite[2]'";

	$array_prix = explode(' ', $_POST['prix']);
	$order .= " AND prix BETWEEN '$array_prix[0]' AND '$array_prix[3]'";

	$req = "SELECT s.id_salle, p.id_produit, s.photo, s.description, p.prix, s.titre, 
			date_format(p.date_arrivee, '%d/%m/%Y') AS arrivee, 
			date_format(p.date_depart, '%d/%m/%Y') AS depart
			FROM salle s, produit p
			WHERE p.id_salle = s.id_salle
			AND p.etat = 'libre'
			$order";
	//debug($req);

	$resultat = $pdo -> prepare($req);
	$resultat -> execute();
	$salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);

	$salles_courte_description = [];

	foreach ($salles as $salle) {
		$salle['courte_description'] = coupure_texte(60, $salle['description']);
		$salle['moyenne'] = moyenneSalle($salle['id_salle']);
		$salles_courte_description[] = $salle;
	}

	//debug($salles_courte_description);

	echo json_encode($salles_courte_description);
}