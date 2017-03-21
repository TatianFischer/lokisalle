<?php
// Connexion à la BDD
$pdo = new PDO('mysql:host=localhost;dbname=lokisalle', 'root','', 
	array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	));






//Session
session_start();

//Chemin
//define('RACINE_SITE', '/htdocs/lokisalle/');
define('RACINE_SITE', '/PHP/atelier/lokisalle/');


//Variables
$msg = '';
$msg_error = '';
$msg_success = '';
$page = '';// Rend la page plus dynamique et améliore le référencement
$contenu = '';
//Autres inclusions
require_once('fonctions.inc.php');




?>