function put_poliza(){
	$.ajax({
		url: "../put/put_poliza_cuentas_predeterminadas.php",
		type: 'POST',
		data:$("#frm_AltaPoliza").serialize(),
		dataType: 'json',
		success: function(data) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else {
                alert('Se ha agregado la póliza');
				location.reload(); 
			}
		},
		error: function (e) {
		}

	});
	return false;
}

function mostrarCheque() {
	var tipoPliza = $('#met_payment').val();
	if( tipoPliza == 7 ) {
		$(".cheques").show('fast');
	}
	else {
		$(".cheques").hide('fast');
	}
}