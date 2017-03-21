<?php

//-------------  FONCTION DEBUG
// Fonction pour afficher des print_r ou des var_dump

function debug($arg){

	echo '<div style="color: white; font-weight: bold; padding: 10px; background:#' . rand('111111', '999999') . '">';
	$trace = debug_backtrace(); // debug_backtrace() est une fonction qui permet de récupérer toutes les infos et notamment le fichier auquel on lui fait appel (retourne un tableau multidimensionnel).
	echo 'le debug a été demandé dans le fichier :' . $trace[0]['file'] . ' à la ligne :' . $trace[0]['line'] . '<hr>';

	echo '<pre>';
    print_r($arg);
    echo '</pre>';



	echo'</div>';
}

//-------- Fonction pour voir si l'utilisateur est connecté
function userConnecte(){
	if (isset($_SESSION['membre'])) {
		return TRUE;
	}
	else{
		return FALSE;
	}
}


// Fonction pour voir si l'utilisateur connecté est admin
function userAdmin(){
	if (userConnecte() && $_SESSION['membre']['statut'] == 1) {
		return TRUE;
	}
	else{
		return FALSE;
	}
}

// ----- FONCTION REGEX
function verif_regex($choix, $variable){
	if($choix == 'carac_nbr'){
		return preg_match('#^[a-zA-Z0-9.\' _-]+$#',$variable);
	} else if($choix == 'caractere') {
		return preg_match('#^[a-zA-Z.\' _-]+$#',$variable);
	} else if($choix == 'nombre') {
		return preg_match('#^[0-9]+$#',$variable);
	} else {
		echo 'erreur dans la fonction verif_regex()';
	}
}
//  La fonction preg_match() permet de vérifier si les caractères contenu dans une chaine de caractères sont conformes à ce que l'on attend.
	// Argument :
		//1. regex (expression régulière)
		//2. la chaine de caractère
	// Valeurs de retour :
		// 1 <=> true (ok)
		// 0 <=> false


// ---- FONCTION COUPURE TEXTE
function coupure_texte($max, $texte){
	if(mb_strlen($texte, 'utf8') >= $max);{
		// Met la portion de chaine dans $description
		$texte = mb_substr($texte, 0, $max, 'utf8');
		// Position du dernier espace
		$espace = mb_strrpos($texte, " ", 'utf8');
		// Test s'il y a un espace
		if($espace){
			$texte = mb_substr($texte, 0, $espace, 'utf8');
		}
		$texte .= "...";
	}
	return $texte;
}

// --FONCTION DE CALCUL DE LA MOYENNE DES NOTES D'UNE SALLE ET DE SON AFFICHAGE
function moyenneSalle($id){
	// Pour accèder à la variable $pdo (connection BDD)
	global $pdo;
	// Calcul de la moyenne
	$req = "SELECT avg(note) as moyenne FROM avis WHERE id_salle = $id";
	$resultat = $pdo -> query($req);
	$note = $resultat -> fetch(PDO::FETCH_ASSOC);
	return $note['moyenne'];
}

// -- FONCTION STATISTIQUES
function statistiques($req){
	// Pour accèder à la variable $pdo (connection BDD)
	global $pdo;
	$resultat = $pdo -> query($req);

	$contenu = '<table border="1"><tr>';
	// Ligne de titre
	for ($i=0; $i < $resultat -> columnCount(); $i++) {// columCount() me retourne le nombre de colonne dans ma table
		$meta = $resultat -> getColumnMeta($i); // Me retourne les infos de chaque colonne
		$contenu .= '<th>' . $meta['name'] . '</th>';
	}
	$contenu .= '</tr>';
	$lignes = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	foreach ($lignes as $valeur) {
		$contenu .= '<tr>';	
		foreach($valeur as $indice2 => $valeur2){
			// Si l'info que l'on parcourt est la note, on ne souhaite pas seulement l'écrire mais afficher le nombre d'étoiles correspondant
			$contenu .= '<td>';
			if($indice2 == 'moyenne'){
				for($star = 1 ; $star <= round($valeur2); $star++){
					$contenu .= '<span class="glyphicon glyphicon-star"></span>';
				}
				for($star = 1; $star <= (5 - round($valeur2)); $star++){
					$contenu .= '<span class="glyphicon glyphicon-star-empty"></span>';
				}
				$contenu .= ' ('.round($valeur2, 2).')';
			} else if ($indice2 == 'montant_total'){
				$contenu .= ($valeur2 != '') ? $valeur2.' €' : '0 €';
			}
			else{
				$contenu .= $valeur2;
			}
			$contenu .= '</td>';
		}
		$contenu .= '</tr>';
	}
	$contenu .= '</table>';
	return $contenu;	
}
?>