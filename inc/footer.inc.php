		</div> <!-- Fin du container -->
		<footer class="text-center">
			<div>    
				<?= date('Y'); ?> - Mentions légales. Conditions générales de ventes.
			</div>
        </footer>
		<!-- jQuery -->
	    <script  src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	   
	    <!-- Latest compiled and minified JavaScript. http://getbootstrap.com/getting-started/ -->
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	    <script src="<?php echo RACINE_SITE ?>js/jquery.fancybox.min.js"></script>
	    <script src="<?php echo RACINE_SITE ?>js/script.js"></script>
		
		<?php if($page == 'Accueil') : ?>
			<script type="text/javascript" src="js/search.js"></script>
	    <?php elseif($page == 'Fiche Produit') : ?>
		    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDhQ4wdK5sx1_gwICfLVrIlnFmbJ392gNM&callback=initMap"></script>
		<?php endif ?>
    </body>
</html>