  function eliminarPoliza(){
	var idPoliza = $(this).attr('idpoliza');
	$etiqueta = $(this);

	if (confirm("¿Está seguro de eliminar la póliza con id: "+ idPoliza+"?")) {
		$.ajax({
			url: "../edit/edit_poliza.php",
			type: 'POST',
			data:{'eliminar':idPoliza},
			dataType: 'json', //Tipo de Respuesta
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					$parent = $etiqueta.parent().parent().parent().parent().parent();
					$parent.remove();
					alert("Se ha borrado la póliza");
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}

function recuerentePoliza(){
	var idPoliza = $(this).attr('idpoliza');
	$etiqueta = $(this);

	if (confirm("¿Está seguro de volver recurrente la póliza con id: "+ idPoliza+"?")) {
		$.ajax({
			url: "../edit/edit_poliza.php",
			type: 'POST',
			data:{'recurente':idPoliza},
			dataType: 'json', //Tipo de Respuesta
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					alert("Se ha convertido la póliza a recurrente");
					location.reload(); 
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}
function removerRecurrente(){
    var idPoliza = $(this).attr('idpoliza');
	$etiqueta = $(this);

	if (confirm("¿Está seguro de remover lo recurrente a la póliza con id: "+ idPoliza+"?")) {
		$.ajax({
			url: "../edit/edit_poliza.php",
			type: 'POST',
			data:{'removerRecurente':idPoliza},
			dataType: 'json', //Tipo de Respuesta
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					alert("Se ha cambiado la póliza");
					location.reload(); 
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}

function clonarPoliza(){
    var idPoliza = $(this).attr('idpoliza');
	$etiqueta = $(this);

	if (confirm("¿Está de clonar la póliza: "+ idPoliza+"?")) {
		$.ajax({
			url: "../edit/edit_poliza.php",
			type: 'POST',
			data:{'clonar':idPoliza},
			dataType: 'json', //Tipo de Respuesta
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					alert("Se ha clonado la póliza");
					location.reload(); 
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}


function cascaronPoliza(){
    var idPoliza = $(this).attr('idpoliza');
	$etiqueta = $(this);

	if (confirm("¿Está de clonar la póliza: "+ idPoliza+"?")) {
		$.ajax({
			url: "../edit/edit_poliza.php",
			type: 'POST',
			data:{'clonar':idPoliza},
			dataType: 'json', //Tipo de Respuesta
			success: function (data) {
				if (data.mensaje) {
					alert(data.mensaje);
				}
				else {
					alert("Se ha clonado la póliza");
					location.reload(); 
				}
			},
			error: function (e) {
			}

		});
	}
	  return false;
}
