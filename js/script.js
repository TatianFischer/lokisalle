$(document).ready(function(){
      // Initialisation des popover : infobulles;
    $('[data-toggle="popover"]').popover();


    $("#form_inscription").on('submit', function(e){
        e.preventDefault();

        $.ajax({
            type : 'post',
            url : $(this).attr('action'),
            data : $(this).serialize(),
            dataType : 'json',

            success: function(data){
                if(data.type == 'danger' || data.type == 'warning' || data.type == 'success'){   
                    $('div.alert').show().addClass('alert-'+data.type).text(data.msg);
                    if(data.type == "success"){
                        $("fieldset").hide();
                        window.location.reload();
                    }
                    data.msg = '';
                    data.type = '';
                    //console.log(data);
                }
            } 
        })
    });

    $("#form_connexion").on('submit', function(e){
        e.preventDefault();

        $.ajax({
            type : 'post',
            url : $(this).attr('action'),
            data : $(this).serialize(),
            dataType : 'json',

            success: function(data){
                if(data.type == 'danger' || data.type == 'warning' || data.type == 'success'){   
                    $('div.alert').show().addClass('alert-'+data.type).text(data.msg);
                    if(data.type == "success"){
                        $("fieldset").hide();
                        window.location.reload();
                    }
                    data.msg = '';
                    data.type = '';
                    //console.log(data);
                }
            },

            error: function(result, status, error){
                console.log("Réponse jQuery : " + result);
                console.log("Statut de la requète : " + status);
                console.log("Type d’erreur : " + error);
                console.log(result);
            }
        })
    });
    


    var map;

    function initMap() {
        $.post({
            type: "get",
            url: "js/googlemap.php",
            dataType : 'json',
            data : {id : $('#id_produit').val()}
        })
        .done(function(data){
            
            // Adresse
            var adresse = data.adresse +", "+data.cp+", "+data.ville;
            console.log(adresse);

            map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: 48.579400, lng: 7.7519},
                    zoom: 15
            });

            // Géolocalisation de l'adresse
            geocoder = new google.maps.Geocoder();

            geocoder.geocode({'address': adresse}, function(results, status){
                /* Si l'adresse a pu être géolocalisée */
                if (status == google.maps.GeocoderStatus.OK) {
                    /* Récupération de sa latitude et de sa longitude */
                    $lat = results[0].geometry.location.lat();
                    $lng = results[0].geometry.location.lng();
                    map.setCenter(results[0].geometry.location);
                    /* Affichage du marker */
                    var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                } 
            });
        })
        .fail(function(result, status, error){
            console.log("Réponse jQuery : " + result);
            console.log("Statut de la requète : " + status);
            console.log("Type d’erreur : " + error);
            console.log(result);
        })
    }

});