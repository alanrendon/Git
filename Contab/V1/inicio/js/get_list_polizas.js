(function($){

  	$.get( "../get/get_polizas.php", function( data ) {
        $("#dv_dibujarPolizas").html(data);
    });

}) (jQuery);