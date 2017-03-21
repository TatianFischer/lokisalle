<!Doctype html>
<html>
    <head>
        <title>Lokisalle - <?= $page ?></title>
        <!--Pour Internet Explorer : s'assurer qu'il utilise la dernière version du moteur de rendu-->
    	<meta http-equiv="X-UA-Compatible" content="IE-edge">
    
	    <!--Affichage sans zoom sur les mobiles-->
	    <meta name="viewport" content="width=device-width, initial-scale=1">
    
	    <!-- Bootstrap CDN : Latest compiled and minified CSS. http://getbootstrap.com/getting-started/-->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	    <link rel="stylesheet" type="text/css" href="<?php echo RACINE_SITE ?>css/jquery.fancybox.min.css">
    
    	<link rel="stylesheet" href="<?php echo RACINE_SITE ?>css/style.css"/>
  
	    <!-- HTML 5 Shiv-min script tag with SRI - Polyfill (voir p8 du cahier)-->
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" integrity="sha256-3Jy/GbSLrg0o9y5Z5n1uw0qxZECH7C6OQpVBgNFYa0g=" crossorigin="anonymous"></script>    
    </head>
    <body>    
        <header>
			<div class="conteneur-full">                      
				<nav class="navbar navbar-inverse">
					<div class="container-fluid">
						<!-- Brand and toggle get grouped for better mobile display -->
					    <div class="navbar-header">
					      	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
						        <span class="sr-only">Toggle navigation</span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
					      	</button>
					      	<a class="navbar-brand" href="<?php echo RACINE_SITE ?>" title="Mon Site">Lokisalle.com</a>
					      	
					    </div> <!-- Fin de la nav hamburger -->
						
						<!-- Collect the nav links, forms, and other content for toggling -->
    					<div class="collapse navbar-collapse" id="navbar-collapse-1">
    						<ul class="nav navbar-nav navbar-left">
					      		<li class="<?= ($page=='Contact') ? ' active' : ''?>"><a href="<?php echo RACINE_SITE ?>contact.php">Contact</a></li>
					      	</ul>
							<ul class="nav navbar-nav navbar-right">
								<li class="navbar-text">Espace Membre :</li>
								<?php if(userConnecte()) : ?>
									<li <?= ($page=='Profil') ? 'class="active"' : ''?> >
										<a href="<?php echo RACINE_SITE ?>profil.php">Profil</a>
									</li>
									<li>
										<a href="<?php echo RACINE_SITE ?>connexion.php?action=deconnexion">Déconnexion</a>
									</li>
								<?php else: ?>
									<li <?= ($page=='Connexion') ? 'class="active"' : ''?> >
										<a data-toggle="modal" data-target="#modal_connexion">Connexion</a>
									</li>
									<li <?= ($page=='Inscription') ? 'class="active"' : ''?> >
										<a data-toggle="modal" data-target="#modal_inscription">Inscription</a>
									</li>
								<?php endif;?>
							</ul>
						</div>
					</div>

					<?php if(userAdmin()) : ?>
					<div class="container-fluid">
						<div class="navbar-header">
					      	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-2" aria-expanded="false">
						        <span class="sr-only">Toggle navigation</span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
					      	</button>
					      	<a class="navbar-brand"></a>
					    </div> <!-- Fin de la nav hamburger -->
						<div class="collapse navbar-collapse" id="navbar-collapse-2">
							<ul class="nav navbar-nav navbar-right" id="admin">
								<li class="navbar-text">
									Espace Admin :
								</li>
								<li <?= ($page=='Gestion Membres') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/gestion_membre.php">Gestion Membres</a>
								</li>
								<li <?= ($page=='Gestion Commandes') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/gestion_commandes.php">Gestion Commandes</a>
								</li>
								<li <?= ($page=='Gestion Produits') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/gestion_produit.php">Gestion Produits</a>
								</li>
								<li <?= ($page=='Gestion Salles') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/gestion_salle.php">Gestion Salles</a>
								</li>
								<li <?= ($page=='Gestion Avis') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/gestion_avis.php">Gestion Avis</a>
								</li>
								<li <?= ($page=='Statistiques') ? 'class="active"' : ''?> >
									<a href="<?php echo RACINE_SITE ?>admin/statistiques.php">Statistiques</a>
								</li>
							</ul>
    					</div><!-- Fin de la navbar collapsed -->
					</div> <!-- Fin du container-fluid -->
					<?php endif; ?>
				</nav>		
			</div>
        </header>
        <div class="container">


        <!-- Modal : CONNEXION -->
			<div class="modal fade" id="modal_connexion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				 <div class="modal-dialog" role="document">
				    <div class="modal-content">
				    	<form class="form-horizontal" id="form_connexion" method="post" action="connexion.php">
						    <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title" id="myModalLabel">Connexion</h4>
						    </div>
				      		<div class="modal-body">
				      			<!-- MESSAGE D' ALERTE -->
				      			<div class="alert" hidden></div>

				      			<fieldset>
				      			<!-- PSEUDO -->
				      				<div class="form-group">
				      					<label class="col-md-4 control-label" for="pseudo">Pseudo :</label>
				      					<div class="col-md-5">
											<input id="pseudo" type="text" name="pseudo" value="<?php if(isset($_POST['pseudo'])) {echo $_POST['pseudo'];} ?>" class="form-control input-md"/>
				      					</div>
									</div>
								<!-- MOT DE PASSE -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="mdp">Mot de passe :</label>
										<div class="col-md-5">
											<input id="mdp" type="password" name="mdp" value="<?php if(isset($_POST['mdp'])) {echo $_POST['mdp'];} ?>" class="form-control input-md"/>
										</div>
									</div>
				      			</fieldset>				
				      		</div>
						    <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal" class="annuler">Annuler</button>
						        <button type="submit" class="btn btn-primary">Enregistrer</button>
						    </div>
				      	</form>
				    </div>
				</div>
			</div> <!-- Fin de la connexion -->

		<!-- Modal : INSCRIPTION -->
			<div class="modal fade" id="modal_inscription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  	<div class="modal-dialog" role="document">
				    <div class="modal-content">
				    	<form class="form-horizontal" id="form_inscription" method="post" action="inscription.php">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title" id="myModalLabel">Inscription</h4>
				      		</div>
				      		<div class="modal-body">
								<!-- MESSAGE D'ALERTE -->
								<div class="alert" hidden></div>
								<fieldset>
									<!-- PSEUDO -->
				      				<div class="form-group">
				      					<label class="col-md-4 control-label" for="pseudo2">Pseudo :</label>
				      					<div class="col-md-5">
											<input id="pseudo2" type="text" name="pseudo" value="<?php if(isset($_POST['pseudo'])) {echo $_POST['pseudo'];} ?>" class="form-control input-md"/>
				      					</div>
									</div>

									<!-- MOT DE PASSE -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="mdp">Mot de passe :</label>
										<div class="col-md-5">
											<input id="mdp" type="password" name="mdp" value="<?php if(isset($_POST['mdp'])) {echo $_POST['mdp'];} ?>" class="form-control input-md"/>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-4 control-label" for="mdp2">Mot de passe :</label>
										<div class="col-md-5">
											<input id="mdp2" type="password" name="mdp2" class="form-control input-md"/>
										</div>
									</div>

									<!-- NOM -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="nom">Nom :</label>
										<div class="col-md-5">
											<input id="nom" type="text" name="nom" value="<?php if(isset($_POST['nom'])) {echo $_POST['nom'];} ?>" class="form-control input-md"/>
										</div>
									</div>
									
									<!-- PRENOM -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="prenom">Prénom :</label>
										<div class="col-md-5">
											<input type="text" name="prenom" value="<?php if(isset($_POST['prenom'])) {echo $_POST['prenom'];} ?>" class="form-control input-md"/>
										</div>
									</div>

									<!-- EMAIL -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="email">Email :</label>
										<div class="col-md-5">
											<input type="text" name="email" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];} ?>" class="form-control input-md"/>
										</div>
									</div>

									<!-- CIVILITE -->
									<div class="form-group">
										<label class="col-md-4 control-label" for="civilite">Civilité :</label>
										<div class="col-md-5">
											<select name="civilite" class="form-control"/><br><br>
												<option value="m">Homme</option>
												<option value="f">Femme</option>
											</select>
										</div>
									</div>
								</fieldset>
				      		</div>
						    <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal" class="annuler">Annuler</button>
						        <button type="submit" class="btn btn-primary">Enregistrer</button>
						    </div>
				      	</form>
				    </div>
			  	</div>
			</div> <!-- Fin de l'inscription -->