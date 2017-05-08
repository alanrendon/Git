function putGrupo() {
	$form = $(this);
	$.ajax({
		url: "../put/put_grupo.php",
		type: 'POST',
		data: $(this).serialize(),
		dataType: 'json',
		success: function( data ) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else {
				$form.trigger("reset");
				location.reload();
			}
		},
		error: function( e ) { }
	});
	return false;
}

function consultaGrupo( idGrupo ) {
	$etiqueta = $(this);
	$.ajax({
		url: "../get/get_grupo.php",
		type: 'POST',
		data:{'idGrupo':idGrupo},
		dataType: 'json',
		success: function( data ) {
			if( data.mensaje ) {
				alert(data.mensaje);
			}
			else {
				option_padre = $('#grupo_padre').find("[value='" + data[0]['fk_grupo'] + "']");
				option_padre.attr('selected', 'selected');
				//$("#select2-grupo_padre-container").html('<span class="select2-selection__clear">×</span>'+data[0]['fk_grupo']);
				$('#grupo').val(data[0]['grupo']);
				option_cod_agr = $('#codigo_agrupador').find("[value='" + data[0]['fk_codagr_rel'] + "']");
				option_cod_agr.attr('selected', 'selected');
				option_cod_ini = $('#cuenta_inicial').find("[value='" + data[0]['fk_codagr_ini'] + "']");
				option_cod_ini.attr('selected', 'selected');
				option_cod_fin = $('#cuenta_final').find("[value='" + data[0]['fk_codagr_fin'] + "']");
				option_cod_fin.attr('selected', 'selected');
				$('#rowid').val(idGrupo);
			}
		},
		error: function( e ) { }
	});
	return false;
}

function updateGrupo() {
	$form = $("#frm_putGrupo");
	$.ajax({
		url: "../update/update_grupo.php",
		type: 'POST',
		data: $form.serialize(),
		dataType: 'json',
		beforeSend: function( e ) { },
		success: function( data ) {
			if (data.mensaje) {
				alert(data.mensaje);
			}
			else {
				location.reload();
			}
		},
		error: function( e ) { }

	});
	return false;
}

(function($){
	$( "#asignacion_balance" ).html('<div align="center"><img src="../images/ajax-loader.gif" /></div>').fadeIn('slow');
	$.get( "../get/get_agrupacion_balance.php", function( data ) {
		$( "#asignacion_balance" ).html(data).fadeIn('slow');
		$( '#datatable' ).DataTable({
			"iDisplayLength": 20,
			"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ 5 ] }
			],
			"language": {
				"lengthMenu": "Mostrar _MENU_ resultados",
				"zeroRecords": "No se encontraron registros",
				"info": "Página _PAGE_ de _PAGES_",
				"infoEmpty": "No hay datos para mostrar",
				"infoFiltered": "(filtrados de _MAX_ resultados totales)",
				"oPaginate": {
					"sFirst": "Primero",
					"sLast": "Último",
					"sNext": "Siguiente",
					"sPrevious": "Anterior"
				},
				"search": "Buscar "
			}
		});
	});

	$( document ).on( "click", ".delete-link", function() {
		var id = $(this).attr('id');
		var tr = $(this).parent().parent().parent().parent();
		if ( confirm("¿Está seguro de eliminar este grupo?") ) {
			$.ajax({
				type: "POST",
				url: '../get/eliminar_grupo.php',
				data: "id="+id,
				success: function(data) {
					tr.remove();
				}
			});
		}
	});

	/*$( document ).on( "click", ".regresar", function() {
		location.reload();
	});*/

}) (jQuery);
