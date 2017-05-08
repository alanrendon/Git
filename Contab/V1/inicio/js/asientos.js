function put_Asiento(){
	$form = $(this);
	$.ajax({
		url: "../put/put_asiento.php",
		type: 'POST',
		data:$(this).serialize(),
		dataType: 'json', 
		beforeSend: function (e) {
			//
		},
		success: function (data) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else {
				$form.trigger("reset");
				location.reload(); 
			}
		},
		error: function (e) {
		}
	});
	return false;
}

function eliminarAsiento(){
	var idAsiento = $(this).attr('idasiento');
	$etiqueta = $(this);

	if (confirm("¿Está seguro de eliminar el asiento: "+ idAsiento+"?")) {
		$.ajax({
			url: "../put/put_asiento.php",
			type: 'POST',
			data:{'eliminar':idAsiento},
			dataType: 'json', //Tipo de Respuesta
			beforeSend: function (e) {
				//("#respuestaImportacion").html("<img src='img/loading.gif' width=100><br>Subiendo archivo a servidor");
			},
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					$parent = $etiqueta.parent().parent().parent().parent();
					$parent.remove();
                    location.reload(); 
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}

function consultaAsiento(idAsiento){
	$etiqueta=$(this); 
	$.ajax({
		 url: "../get/get_asiento.php",
		 type: 'POST',
		 data:{'idAsiento':idAsiento},
		 dataType: 'json', //Tipo de Respuesta
		 beforeSend: function (e) {
				//("#respuestaImportacion").html("<img src='img/loading.gif' width=100><br>Subiendo archivo a servidor");
		 },
		success: function (data) {
			if (data.mensaje) {
					alert(data.mensaje);
			}else{
				$('#txt_descripcion').val(data[0]['descripcion']);
				$('#txt_debe').val(data[0]['debe']);
				$('#txt_haber').val(data[0]['haber']);
				option = $('#txt_cuenta').find("[value='" + data[0]['cuenta'] + "']");
				option.attr('selected', 'selected');

			}
		 },
		 error: function (e) {
		}

	});
	return false;
}

function updateAsiento(){
	$form = $("#frm_putAsiento");
	$.ajax({
		url: "../update/update_asiento.php",
		type: 'POST',
		data: $form.serialize(),
		dataType: 'json',
		success: function (data) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else{
                 alert('Se han guardado los cambios al asiento');
			     location.reload(); 
			}
		},
		error: function (e) {
		}

	});
	return false;
}