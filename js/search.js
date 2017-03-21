$(document).ready(function(){
	$( "#capacite-range" ).slider({
        range: true,
        min: 2,
        max: 1000,
        values: [ 2, 1000],
        slide: function( event, ui ) {
            $( "#capacite" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] + " personnes");
        }
    });
    
    $( "#capacite" ).val($( "#capacite-range" ).slider( "values", 0 ) +
      " - " + $( "#capacite-range" ).slider( "values", 1 ) + " personnes");


    $( "#amount-range" ).slider({
        range: true,
        min: 100,
        max: 10000,
        values: [ 100, 10000 ],
        slide: function( event, ui ) {
            $( "#amount" ).val( ui.values[ 0 ] + " € - " + ui.values[ 1 ] + " €");
        }
    });
    
    $( "#amount" ).val($( "#amount-range" ).slider( "values", 0 ) +
      " € - " + $( "#amount-range" ).slider( "values", 1 ) + " €");



    // Formulaire de recherche de salle
    $('#search_form').on('change', function(e){
    	e.preventDefault();

        $.ajax({
            type : $(this).attr('method'),
            url : $(this).attr('action'),
            data : $(this).serialize(),
            dataType : 'json'
        })

        .done(function(data){
            console.log(data);
            $('#droite').empty();

            $.each(data, function(index, salle){
                creationVignette(index, salle);
            })
        })

        .fail(function(result, status, error){
            console.log("Réponse jQuery : " + result);
            console.log("Statut de la requète : " + status);
            console.log("Type d’erreur : " + error);
            console.log(result);
        })
    })

    $('#search_form #capacite-range').on('mouseup', function(e){
        e.preventDefault();

        $.ajax({
            type : $('#search_form').attr('method'),
            url : $('#search_form').attr('action'),
            data : $('#search_form').serialize(),
            dataType : 'json'
        })

        .done(function(data){
            console.log(data);
            $('#droite').empty();

            $.each(data, function(index, salle){
                creationVignette(index, salle);
            })
        })

        .fail(function(result, status, error){
            console.log("Réponse jQuery : " + result);
            console.log("Statut de la requète : " + status);
            console.log("Type d’erreur : " + error);
            console.log(result);
        })
    })

    $('#search_form #amount-range').on('mouseup', function(e){
        e.preventDefault();

        $.ajax({
            type : $('#search_form').attr('method'),
            url : $('#search_form').attr('action'),
            data : $('#search_form').serialize(),
            dataType : 'json'
        })

        .done(function(data){
            console.log(data);
            $('#droite').empty();

            $.each(data, function(index, salle){
                creationVignette(index, salle);
            })
        })

        .fail(function(result, status, error){
            console.log("Réponse jQuery : " + result);
            console.log("Statut de la requète : " + status);
            console.log("Type d’erreur : " + error);
            console.log(result);
        })
    })


    // Création des vignettes.
    function creationVignette(index, salle){
        $('<div>').addClass('col-md-4 col-sm-6 col-xs-12 r-p')
            .append($('<div>').addClass('vignette row')
                .append($('<img>').attr('src', 'photo/'+salle.photo))
                .append($('<div>').addClass('row r-m')
                    .append($('<h3>').addClass('col-md-9 col-sm-8 col-xs-9').text(salle.titre))
                    .append($('<p>').addClass('prix col-md-3 col-sm-4 col-xs-3').text(salle.prix+" €"))
                )
                .append($('<div>').addClass('row r-m')
                    .append($('<p>').addClass('description col-xs-12').text(salle.courte_description))
                )
                .append($('<div>').addClass('row r-m')
                    .append($('<p>').addClass('col-xs-12').html('<span class="glyphicon glyphicon-calendar"></span> Du '+salle.arrivee+' au '+salle.depart))
                )
                .append($('<div>').addClass('row r-m')
                    .append($('<div>').addClass('avis col-xs-8')) // moyenne
                    .append($('<p>').addClass('voir col-xs-3 r-p')
                        .append($('<a>').attr('href', 'fiche_produit.php?id='+salle.id_produit).html('<span class="glyphicon glyphicon-search"></span> Voir'))
                    )
                )
            )
        .appendTo('#droite');

        for(i = 1 ; i <= Math.round(salle.moyenne) ; i++){
            $('<span>').addClass('glyphicon glyphicon-star').appendTo('.avis:last');
        }

        for(i = 1 ; i <= (5 - Math.round(salle.moyenne)) ; i++){
            $('<span>').addClass('glyphicon glyphicon-star-empty').appendTo('.avis:last');
        }

        $(".avis:last").append("("+Math.round(salle.moyenne)+")");
    }
})