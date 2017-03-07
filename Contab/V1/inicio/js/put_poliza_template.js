function put_poliza(id_form){
	$.ajax({
		url: "../put/put_poliza_template.php",
		type: 'POST',
		data:$("#frm_AltaPoliza").serialize(),
		dataType: 'json',
		beforeSend: function (e) {
			//
		},
		success: function(data) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else {
				alert('Se ha agregado la platilla');
				location.reload(); 
			}
		},
		error: function (e) {
		}

	});
	return false;
}

 function mostrarCheque() {
        var tipoPliza = $("#met_payment").val();
        if (tipoPliza == 7) {
            $(".cheques").show('fast');
        } else {
            $(".cheques").hide('fast');
        }
    }