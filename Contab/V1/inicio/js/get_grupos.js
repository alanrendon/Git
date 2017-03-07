(function($){
	$( "#asignacion_grupos_padre" ).html('<div align="center"><img src="../images/ajax-loader.gif" /></div>').fadeIn('slow');
	$.get( "../get/get_gpadres_balance.php", function( data ) {
		$( "#asignacion_grupos_padre" ).html(data).fadeIn('slow');
		$( '#datatable' ).DataTable({
			"iDisplayLength": 20,
			"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ 2 ] }
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
		if ( confirm("¿Está seguro de eliminar este grupo padre?") ) {
			$.ajax({
				type: "POST",
				url: '../get/eliminar_grupo_padre.php',
				data: "id="+id,
				success: function(data) {
					tr.remove();
				}
			});
		}
	});

}) (jQuery);
