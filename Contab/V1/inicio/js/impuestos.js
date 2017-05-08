(function($){

	$( "#registrar_impuesto" ).on( "click", function(e) {
		e.preventDefault();
		$("#mensaje").html('');
		var form = $('#form_impuestos').serialize();
		$.ajax( {
			type: "POST",
			url: '../put/put_impuesto.php',
			data: form,
			dataType: "Json",
			success: function(data) {
				var mensaje = (data["json"] == 1) ? 'Registro exitoso.' : data["json"].replace('"', '').replace('"', '');
				$("#mensaje").fadeIn('slow').html(mensaje);

				if(data["json"] == 1){window.location.href = 'registro_impuestos.php';}

				setTimeout(function(){
				  	$("#mensaje_cuenta").fadeOut('slow').html('');
				}, 4000);
			}
		});
	});

}) (jQuery);
