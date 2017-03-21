<?php 
require_once('inc/init.inc.php');

$page = 'Accueil';
/*--------------------------------------
		HEADER DU SITE
--------------------------------------*/
require_once('inc/header.inc.php');

/*--------------------------------------
	RECUPERATION DES CATEGORIES
--------------------------------------*/
// On récupère toutes les infos de toutes les catégories de salle :
$resultat = $pdo -> query("SELECT distinct categorie FROM salle s , produit p WHERE p.id_salle = s.id_salle AND p.etat = 'libre'");
$categorie = $resultat -> fetchAll(PDO::FETCH_ASSOC);
//debug($categorie);


/*--------------------------------------
	RECUPERATION DES VILLES 
--------------------------------------*/
// On récupère toutes les infos de toutes les catégories de salle :
$resultat = $pdo -> query("SELECT distinct ville FROM salle s , produit p WHERE p.id_salle = s.id_salle AND p.etat = 'libre'");
$ville = $resultat -> fetchAll(PDO::FETCH_ASSOC);
//debug($ville);

/*--------------------------------------
	RECUPERATION DES CAPACITE 
--------------------------------------*/
// On récupère toutes les infos de toutes les catégories de salle :
$resultat = $pdo -> query("SELECT distinct capacite FROM salle s , produit p WHERE p.id_salle = s.id_salle AND p.etat = 'libre' ORDER BY capacite");
$capacite = $resultat -> fetchAll(PDO::FETCH_ASSOC);
//debug($capacite);


/*--------------------------------------
	AFFICHAGE PAR DEFAUT
--------------------------------------*/
if(empty($_GET['categorie']) && empty($_GET['public'])){
	$req = "SELECT s.id_salle, p.id_produit, s.photo, s.description, p.prix, s.titre, 
		date_format(p.date_arrivee, '%d/%m/%Y') AS arrivee, 
		date_format(p.date_depart, '%d/%m/%Y') as depart
		FROM salle s, produit p
		WHERE p.id_salle = s.id_salle
		AND p.etat = 'libre'";
	$resultat = $pdo -> prepare($req);
	$resultat -> execute();
	$salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
	//debug($salles);
}

?>

<!-- ############################## -->
<!-- ############################## -->
<!-- ############################## -->
<!-- ############################## -->

<div class="row">
	<div class="col-lg-2 col-md-3 col-lg-offset-1" id="gauche">
		<form class="form-horizontal" method="post" action="accueil_search.php" id="search_form">
			<h3>Catégorie</h3>
			<div class="list-group">
				<div data-toggle="buttons">
					<?php foreach ($categorie as $cat) : ?>
						<label class="list-group-item btn btn-block" for="<?= $cat['categorie'] ?>">
							<input id="<?= $cat['categorie'] ?>" value="<?= $cat['categorie'] ?>" name="categorie[]" type="checkbox">
							<?= $cat['categorie'] ?>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<h3>Ville</h3>
			<div class="list-group">
				<div data-toggle="buttons">
					<?php foreach ($ville as $city) : ?>
						<label class="list-group-item btn btn-block" for="<?= $city['ville'] ?>">
							<input id="<?= $city['ville'] ?>" value="<?= $city['ville'] ?>" name="ville[]" type="checkbox">
							<?= $city['ville'] ?>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<h3>Capacité</h3>
			<div class="form-group">
			  	<div class="col-md-12">
			  		<div id="capacite-range"></div>
					<input type="text" id="capacite" readonly class="text-center form-control input-md" name="capacite">
			  	</div>
			</div>

			<h3>Prix</h3>
			<div class="form-group">
				<div class="col-md-12">
					<div id="amount-range"></div>
					<input type="text" id="amount" readonly class="text-center form-control input-md" name="prix">
				</div>
			</div>
			
			<h3>Periode</h3>
			<h4>Date d'arrivée</h4>
			<div class="form-group">
	  			<div class="col-md-12">
	      			<input id="date_arrivee" name="date_arrivee" class="form-control" type="date">
	  			</div>
			</div>
			<h4>Date de départ</h4>
			<div class="form-group">
	  			<div class="col-md-12">
	      			<input id="date_depart" name="date_depart" class="form-control" type="date">
	  			</div>
			</div>
		</form>
	</div> <!-- Fin du côté gauche -->

	<div class="col-lg-8 col-md-9" id="droite">
	<?php foreach ($salles as $salle): ?>
		<!-- Vignette -->
		<div class="col-md-4 col-sm-6 col-xs-12 r-p">
			<div class="vignette row">
				<img src="photo/<?= $salle['photo'] ?>">
				<div class="row r-m">
					<h3 class="col-lg-9 col-sm-8 col-xs-9"><?= $salle['titre'] ?></h3>
					<p class="prix col-lg-3 col-sm-4 col-xs-3"><?= $salle['prix'] ?> €</p>
				</div>
				<div class="row r-m">
					<p class="description col-xs-12">
					<?php 
						echo coupure_texte(60, $salle['description']);
					?>
					</p>
				</div>
				<div class="row r-m">
					<p class="col-xs-12">
						<span class="glyphicon glyphicon-calendar"></span> Du <?= $salle['arrivee'] ?> au <?= $salle['depart'] ?>
					</p>
				</div>
				<div class="row r-m">
					<div class="avis col-xs-8">
						<?php
							$moyenne = moyenneSalle($salle['id_salle']);

							for ($i = 1; $i <= round($moyenne); $i++): ?>
								<span class="glyphicon glyphicon-star"></span>
							<?php endfor;

							for ($i = 1; $i <= (5-round($moyenne)); $i++): ?>
								<span class="glyphicon glyphicon-star-empty"></span>
							<?php endfor;
							echo '('.round($moyenne,1).')';?>
					</div>
					<p class="voir col-xs-3 r-p">
						<a href="fiche_produit.php?id=<?= $salle['id_produit'] ?>">
							<span class="glyphicon glyphicon-search"></span> Voir
						</a>
					</p>
				</div>
			</div>
		</div>
		<!-- Fin de vignette -->
	<?php endforeach; ?>
		
	</div> <!-- Fin du côté droit -->
	
</div> <!-- Fin de la row de la page -->

<?php
 	/*--------------------------------------
			FOOTER DU SITE
	--------------------------------------*/ 
	require_once('inc/footer.inc.php');
?>