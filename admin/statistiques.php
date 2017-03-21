<?php 
	require_once('/../inc/init.inc.php');

	 	if(!userAdmin()){ 
		header('location:'.RACINE_SITE.'connexion.php?action=deconnexion');
	}		



	/*--------------------------------------
			HEADER DU SITE
	--------------------------------------*/
	$page = "Statistiques";
	require_once('/../inc/header.inc.php');
?>
	<h1>Statistiques</h1>

	
	<div class="row">
		<div class="col-sm-12">
		<h2>Chiffre d'affaire par trimestre</h2>
			<table border="1">
				<tr>
					<th>Année</th>
					<th>1er Trimestre</th>
					<th>2nd Trimestre</th>
					<th>3ème Trimestre</th>
					<th>4ème Trimestre</th>
				</tr>
				<?php
				for($annee = 2016; $annee <= date('Y'); $annee++){
					$contenu .= '<tr><td>'.$annee.'</td>';
					for($mois = 3; $mois <=12; $mois += 3){
						$contenu .=	'<td>';
							$mois_fin = $mois + 2;
							$req = "SELECT sum(prix) as montant_total
							FROM produit
							WHERE id_produit IN
							(SELECT id_produit FROM commande
							WHERE date_enregistrement
							BETWEEN '$annee-$mois-01' AND '$annee-$mois_fin-31')";
							$result = $pdo -> query($req);
							$valeur = $result->fetch(PDO::FETCH_ASSOC);
							$contenu .= ($valeur['montant_total'] != '') ?  $valeur['montant_total']: 0;
						$contenu .= ' €</td>';
					}
					$contenu .= '</tr>';
				}
				$contenu .= '<tr><td colspan = 3> Chiffre d\'affaire total</td>';
				$contenu .= '<td colspan = 2>';
				$result = $pdo -> query("SELECT sum(p.prix) as total FROM produit p, commande c WHERE p.id_produit = c.id_produit");
				$valeur = $result-> fetch(PDO::FETCH_ASSOC);
				$contenu .= $valeur['total'];
				$contenu .= ' €</td></tr>';
				echo $contenu;
				?>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6">
		<h2>Moyenne des trois meilleurs salles</h2>
<?php
			$req = "SELECT s.titre, s.id_salle, AVG(a.note) AS moyenne FROM avis a, salle s WHERE a.id_salle = s.id_salle GROUP BY a.id_salle ORDER BY moyenne DESC LIMIT 0,5";
			echo statistiques($req);
?>
		</div>

		<div class="col-sm-6">
			<h2>Moyenne des trois pires salles</h2>
<?php
			$req = "SELECT s.titre, s.id_salle, AVG(a.note) AS moyenne
			FROM avis a, salle s
			WHERE a.id_salle = s.id_salle
			GROUP BY a.id_salle
			ORDER BY moyenne ASC
			LIMIT 0,5";
			echo statistiques($req);
?>
		</div>
	</div>


	<div class="row">
		<div class="col-sm-6">
		<h2>Salles les plus réservées</h2>
<?php
		$req = "SELECT s.titre, s.id_salle, count(p.id_salle) as nombre_commande
		 FROM salle s
		 LEFT JOIN produit p
		 ON s.id_salle = p.id_salle
		 GROUP BY p.id_salle
		 ORDER BY nombre_commande DESC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
		<div class="col-sm-6">
		<h2>Salles les moins réservées</h2>
<?php
		$req = "SELECT s.titre, s.id_salle, count(p.id_salle) as nombre_commande
		 FROM salle s
		 LEFT JOIN produit p
		 ON s.id_salle = p.id_salle
		 GROUP BY p.id_salle
		 ORDER BY nombre_commande ASC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-6">
		<h2>Membres qui dépensent le plus</h2>
<?php
		$req = "SELECT m.pseudo, sum(p.prix) as montant_total
		 FROM membre m
		 LEFT JOIN commande c
		 ON c.id_membre = m.id_membre
		 LEFT JOIN produit p
		 ON p.id_produit = c.id_produit
		 GROUP BY m.id_membre
		 ORDER BY montant_total DESC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
		<div class="col-sm-6">
		<h2>Membres qui dépensent le moins</h2>
<?php
		$req = "SELECT m.pseudo, sum(p.prix) as montant_total
		 FROM membre m
		 LEFT JOIN commande c
		 ON c.id_membre = m.id_membre
		 LEFT JOIN produit p
		 ON p.id_produit = c.id_produit
		 GROUP BY m.id_membre
		 ORDER BY montant_total ASC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
	</div>	

	<div class="row">
		<div class="col-sm-6">
		<h2>Membres qui commandent le plus</h2>
<?php
		$req = "SELECT m.pseudo, count(c.id_membre) as nombre_commande
		 FROM membre m
		 LEFT JOIN commande c
		 ON c.id_membre = m.id_membre
		 LEFT JOIN produit p
		 ON p.id_produit = c.id_produit
		 GROUP BY m.id_membre
		 ORDER BY nombre_commande DESC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
		<div class="col-sm-6">
		<h2>Membres qui commandent le moins</h2>
<?php
		$req = "SELECT m.pseudo, count(c.id_membre) as nombre_commande
		 FROM membre m
		 LEFT JOIN commande c
		 ON c.id_membre = m.id_membre
		 LEFT JOIN produit p
		 ON p.id_produit = c.id_produit
		 GROUP BY m.id_membre
		 ORDER BY nombre_commande ASC
		 LIMIT 0,5";
		echo statistiques($req);
?>
		</div>
	</div>

 <?php
 	/*--------------------------------------
			FOOTER DU SITE
	--------------------------------------*/ 
	require_once('/../inc/footer.inc.php');
 ?>