<?php

require_once('inc/init.inc.php');
/*--------------------------------------
			HEADER DU SITE
	--------------------------------------*/
$page = 'Fiche Produit';
require_once('inc/header.inc.php');

/*--------------------------------------
		INFORMATIONS DU PRODUIT
	--------------------------------------*/
if(isset($_GET['id']) && $_GET['id'] != ''){
	$resultat = $pdo->prepare("SELECT * FROM produit p, salle s WHERE s.id_salle = p.id_salle AND p.id_produit = :id");
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();
	$informations = $resultat -> fetch(PDO::FETCH_ASSOC);

	$resultat = $pdo->prepare("SELECT avg(a.note) as moyenne from avis a, produit p WHERE p.id_salle = a.id_salle and p.id_produit = :id");
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();
	$note = $resultat -> fetch(PDO::FETCH_ASSOC);
}


$titreHeader = 'Salle '.$informations['titre'].' ';
for($star = 1 ; $star <= round($note['moyenne']); $star++ ){
	$titreHeader .= '<span class="glyphicon glyphicon-star"></span>';
}

for($star = 1 ; $star <= round(5 - $note['moyenne']); $star++){
	$titreHeader .= '<span class="glyphicon glyphicon-star-empty"></span>';
}

$date_arrivee = date_create($informations['date_arrivee']);
$date_depart = date_create($informations['date_depart']);
?>
<!-- Portfolio Item Heading -->
<div class="row">
    <div class="col-lg-12 page-header">
        <h1><?= $titreHeader ?>
        <?php if(userConnecte()) : ?>
        	<button type="button" class="btn btn-lg btn-primary col-xs-offset-6">Réserver</button>
        <?php else :?>
        	<button type="button" class="btn btn-lg btn-primary col-xs-offset-6" data-toggle="modal" data-target="#modal_connexion">Connexion</button>
        <?php endif ?>
        </h1>     
    </div>
</div>
<!-- /.row -->


<!-- Portfolio Item Row -->
<div class="row">
    <div class="col-md-7">
        <img class="img_poster" src="photo/<?= $informations['photo']?>">
    </div>

    <div class="col-md-5 info_map">
        <h3>Description :</h3>
        <p><?= $informations['description'] ?></p>
        <h3>Localisation :</h3>
        <div id="map"></div>    
    </div>
</div>
<!-- /.row -->
<div class="row">
	<div class="col-xs-12">
		<h3 class="page-header">Informations complémentaires :</h3>
	</div>
	<ul>
		<div class="col-md-4">
			<li>
				<span class="glyphicon glyphicon-calendar"></span> Arrivée : <?= date_format($date_arrivee, 'd/m/Y') ?>
			</li>
			<li>
				<span class="glyphicon glyphicon-calendar"></span> Départ : <?= date_format($date_depart, 'd/m/Y') ?>
			</li>
		</div>
		<div class="col-md-4">
			<li>
				<span class="glyphicon glyphicon-blackboard"></span> Capacité : <?= $informations['capacite'] ?>
			</li>
			<li>
				<span class="glyphicon glyphicon glyphicon-bookmark"></span> Catégorie : <?= $informations['categorie'] ?>
			</li>
		</div>
		<div class="col-md-4">
			<li>
				<span class="glyphicon glyphicon-map-marker"></span> Adresse : <?= 
			$informations['adresse'].', '.$informations['cp'].', '.$informations['ville']	?>
			</li>
			<li>
				<span class="glyphicon glyphicon-eur"></span> Tarif : <?= $informations['prix'].' €' ?>
			</li>
		</div>
	</ul>
</div>
<!-- /.row -->

<!-- Related Projects Row -->
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">Autres produits :</h3>
    </div>

    <?php
    	$resultat = $pdo -> prepare("SELECT p.id_produit, s.photo FROM produit p, salle s WHERE p.id_salle = s.id_salle AND etat = 'libre' AND p.id_produit != :id ORDER BY rand(p.id_produit) LIMIT 0,4");
    	$resultat -> BindParam(':id', $_GET['id'], PDO::PARAM_INT);
    	$resultat -> execute();
    	$otherProduits = $resultat -> fetchAll(PDO::FETCH_ASSOC);

    	foreach($otherProduits as $otherProduit) :
    	?>
	    <div class="col-sm-3 col-xs-6">
	        <a href="fiche_produit.php?id=<?= $otherProduit['id_produit'] ?>">
	        	<img src="<?= RACINE_SITE.'photo/'.$otherProduit['photo'] ?>" class="img-responsive portfolio-item">
	        </a>
	    </div>
    <?php endforeach; ?>
</div>
<!-- /.row -->

<div class="row">
	<div class="col-md-7">
		<?php if(userConnecte()) : ?>
			<a>Déposer un commentaire et une note</a>
		<?php else : ?>
			<a data-toggle="modal" data-target="#modal_connexion">Pour déposer une note ou un commentaire, veuillez vous connecter.</a>
		<?php endif; ?>
	</div>
	<div class="col-md-5 text-right">
		<a href="index.php">Retour vers le catalogue</a>
	</div>
</div>
<!-- /.row -->

<input type="text" id="id_produit" value="<?= $_GET['id'] ?>" hidden>

<?php

require_once('inc/footer.inc.php');

?>