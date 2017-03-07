(function($){

	var datos = {
		fun: 'get_tipos_pagos_credito'
	};
	$.get("../get/get_tipos_pagos.php", datos, function(data) {
		$("#creditos").html(data);
		$(".select2_single").select2({
			placeholder: "Seleccione una opción",
			allowClear: true
		});
		$(".select2_group").select2({});
		$(".select2_multiple").select2({
			placeholder: "Seleccione uno o más",
			allowClear: true
		});
	});

	var datos = {
		fun: 'get_tipos_pagos'
	};
	$.get("../get/get_tipos_pagos.php", datos, function(data) {
		$("#pagos").html(data);
		$(".select2_single").select2({
			placeholder: "Seleccione una opción",
			allowClear: true
		});
		$(".select2_group").select2({});
		$(".select2_multiple").select2({
			placeholder: "Seleccione uno o más",
			allowClear: true
		});
	});

	$("#guardar_pago").on("click", function(e) {
		e.preventDefault();
		$("#mensaje").html('');
		var form = $('#form_pago').serialize();
		$.ajax({
			type: "POST",
			url: '../put/put_condicion_pago.php',
			data: form,
			dataType: "Json",
			success: function(data) {
				var mensaje = (data["json"] == 1) ? 'Se registró correctamente.' : 'Ocurrió un problema, posiblemente ya esté asignado.';
				$("#mensaje").html(mensaje);
				$('#form_pago').trigger("reset");
				setTimeout(function() {
					location.reload();
				}, 2000);
			}
		});
	});

	var datos={ fun:'get_condiciones_asignadas'};
  	$.get( "../get/get_tipos_pagos.php", datos, function( data ) {   
		$( "#listado_pagos" ).html(data).fadeIn('slow');
		$( '#datatable' ).DataTable({
			"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ 3 ] }
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

	$(document).on("click", ".delete-link", function() {
		var id = $(this).attr('id');
		var tr = $(this).parent().parent().parent().parent();
		if (confirm("¿Está seguro de eliminar la relación?")) {
			$.ajax({
				type: "POST",
				url: '../edit/edit_paiment_term.php',
				data:{
						'eliminar':id
					},
				success: function(data) {
					tr.remove();
					setTimeout(function() {
						location.reload();
					}, 2000);
				}
			});
		}
	});

}) (jQuery);