<?php

require_once('inc/init.inc.php');

if ($_POST) {
 
  // (1) Code PHP pour traiter l'envoi de l'email
 
  // Récupération des variables et sécurisation des données
  $nom     = htmlentities($_POST['nom']); // htmlentities() convertit des caractères "spéciaux" en équivalent HTML
  $email   = htmlentities($_POST['email']);
  $message = htmlentities($_POST['message']);
 
  // Variables concernant l'email
 
  $destinataire = 'lehenaff.olivier@hotmail.fr'; // Adresse email du webmaster (à personnaliser)
  $sujet = 'Titre du message'; // Titre de l'email
  $contenu = '<html><head><title>Titre du message</title></head><body>';
  $contenu .= '<p>Bonjour, vous avez reçu un message à partir de votre site web.</p>';
  $contenu .= '<p><strong>Nom</strong>: '.$nom.'</p>';
  $contenu .= '<p><strong>Email</strong>: '.$email.'</p>';
  $contenu .= '<p><strong>Message</strong>: '.$message.'</p>';
  $contenu .= '</body></html>'; // Contenu du message de l'email (en XHTML)
 
  // Pour envoyer un email HTML, l'en-tête Content-type doit être défini
  $header = 'MIME-Version: 1.0'."\r\n";
  $header .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
 
  // Envoyer l'email
  mail($destinataire, $sujet, $contenu, $header); // Fonction principale qui envoi l'email
  echo '<h2>Message envoyé!</h2>'; // Afficher un message pour indiquer que le message a été envoyé
  // (2) Fin du code pour traiter l'envoi de l'email
}


$page = 'Contact';
require_once('inc/header.inc.php');


?>

  <head>
    <title>Contact</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  </head>
  <body>
    <h1>Contacter le webmaster</h1>
    <!-- Ceci est un commentaire HTML. Le code PHP devra remplacé cette ligne -->
    <form method="post" action="" class="form-horizontal">
      <p>Votre nom et prénom: <input type="text" name="nom" size="30" /></p>
      <p>Votre email: <span style="color:#ff0000;">*</span>: <input type="text" name="email" size="30" /></p>
      <p>Message <span style="color:#ff0000;">*</span>:</p>
      <textarea name="message" cols="60" rows="10"></textarea>
      <!-- Ici pourra être ajouté un captcha anti-spam (plus tard) -->
      <p><input type="submit" name="submit" value="Envoyer" /></p>
    </form>
  </body>
<!--
<form id="contact" method="post" action="">
	<fieldset><legend>Vos coordonnées</legend>
		<p><label>Nom :</label><input type="text" id="nom" name="nom"/></p>
		<p><label>Prénom :</label><input type="text" id="nom" name="nom"/></p>
		<p><label>Email :</label><input type="text" id="email" name="email"/></p>
	</fieldset>

	<fieldset><legend>Votre message :</legend>
		
		<p><label>Message :</label><textarea id="message" name="message"></textarea></p>
	</fieldset>

	<div style="text-align:center;"><input type="submit" name="envoi" value="Envoyer le formulaire !" /></div>
</form>
-->

<?php

require_once('inc/footer.inc.php');

?>
