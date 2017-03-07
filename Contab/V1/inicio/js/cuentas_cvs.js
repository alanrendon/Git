(function($){
	$( "#carga_cvs" ).on( "click", function(e) {
         var eliminar =0;
		e.preventDefault();
		$("#mensaje_carga").html('');

    if($('#eliminar_ctas').is(":checked")){
        eliminar = 1;
    }

    var file = $('#file').prop('files')[0];
		var form = new FormData();

		if( file ) {
			form.append('file', file);
			form.append('eliminar_ctas', eliminar);
			$.ajax({
				url: '../get/cargar_cvs.php',
				dataType: 'text',
				cache: false,
				contentType: false,
				processData: false,
				data: form,
				type: 'post',
				success: function(data) {
					$("#mensaje_carga").html('<span class="label label-warning">'+data+'</span>');
				}
			});
		}
		else {
			$("#mensaje_carga").html('<div>Debe agregar un archivo CVS</div>');
		}
	});

	$( "#carga_cuenta" ).on( "click", function(e) {
		e.preventDefault();
		$("#mensaje_cuenta").html('');
		var form = $('#demo-form2').serialize();
		$.ajax( {
			type: "POST",
			url: '../put/put_cuenta.php',
			data: form,
			dataType: "Json",
			success: function(data) {
				var mensaje = (data["json"] == 1) ? 'La cuenta se registró correctamente.' : data["json"].replace('"', '').replace('"', '');
				$("#mensaje_cuenta").fadeIn('slow').html(mensaje);
				//$('#demo-form2').trigger("reset");
				if(data["json"] == 1){window.location.href = 'consulta.php';}
				//console.log(data["json"]);
				setTimeout(function(){
				  	$("#mensaje_cuenta").fadeOut('slow').html('');
				}, 4000);
			}
		});
	});

}) (jQuery);
