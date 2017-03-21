<?php
require_once('../inc/init.inc.php');

$resultat = $pdo->prepare("SELECT * FROM produit p, salle s WHERE s.id_salle = p.id_salle AND p.id_produit = :id");
$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$resultat -> execute();
$informations = $resultat -> fetch(PDO::FETCH_ASSOC);

echo json_encode($informations);