function putGrupo(){
	console.log($(this).serialize());
	$form = $(this);
	$.ajax({
		url: "../put/put_grupo.php",
		type: 'POST',
		data: $(this).serialize(),
		dataType: 'json',
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

(function($){
	$( "#asignacion_resultado" ).html('<div align="center"><img src="../images/ajax-loader.gif" /></div>').fadeIn('slow');
	$.get( "../get/get_agrupacion_resultado.php", function( data ) {
		$( "#asignacion_resultado" ).html(data).fadeIn('slow');
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

	$( document ).on( "click", ".regresar", function() {
		location.reload();
	});

}) (jQuery);
