function listaDoctosRelacionados(tipo,id_contenedor){
	$.ajax({
		url: "../get/get_doctosRelacionados.php",
            type: 'POST',
            data:{ "societe_type": tipo},
            dataType: 'json', //Tipo de Respuesta
            beforeSend: function (e) {
                //("#respuestaImportacion").html("<img src='img/loading.gif' width=100><br>Subiendo archivo a servidor");
            },
            success: function (data) {
            	dibuja = "";
            	dibuja += '<label class="">Seleccione</label>';
		        dibuja += '<select class="select2_single form-control" id="" name="slc_facture" tabindex="-1">';
             	if ( data!=false) {
             		 $.each(data, function (i, item) {
	                    dibuja += '<option value="'+data[i].rowid+'">'+data[i].facnumber+' '+data[i].nom+' '+data[i].total_ttc+'</option>';
                	});
		           
             	}
             	dibuja += '</select>';
             	$("#"+id_contenedor).html(dibuja);
             	if (data==false) {
             		$("#slc_facture").attr("disabled","disabled");
             	}
             	iniciaSelect();
            },
            error: function (e) {
            }

	});
}