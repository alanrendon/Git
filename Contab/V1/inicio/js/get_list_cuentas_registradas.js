(function($){
	$( "#cuentas_registradas" ).html('<div align="center"><img src="../images/ajax-loader.gif" /></div>').fadeIn('slow');
	$.get( "../get/get_cuentas_registradas.php", function( data ) {
		$( "#cuentas_registradas" ).fadeOut('slow');
		$( "#cuentas_registradas" ).html(data).fadeIn('slow');
		$( '#datatable' ).DataTable({
			"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': [ 6 ] }
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

	$( document ).on( "click", ".edit-link", function() {
		var id = $(this).attr('id');

		$.ajax({
			type: "POST",
			url: '../get/editar_form_cuentas.php',
			data: "id="+id,
			success: function(data) {
				$(".x_title").html('<h2>Edición</h2><div class="clearfix"></div>');
				$( "#cuentas_registradas" ).html(data);
                iniciaSelect();
                $('#demo-form2').submit(UpdateCuenta);
			}
		});
	});

	$( document ).on( "click", ".delete-link", function() {
		var id = $(this).attr('id');
		var tr = $(this).parent().parent().parent().parent();
		if (confirm("¿Está seguro de eliminar esta cuentas?")) {
			//tr.remove();
			$.ajax({
				type: "POST",
				url: '../get/eliminar_cuenta.php',
				data: "id="+id,
                dataType: 'JSON',
				success: function(data) {
					if(data.eliminar == true){
						tr.remove();
					}else{
						alert("No puede eliminar una cuenta que este asignada a pólizas");
					}
				}
			});
		}
	});

	$( document ).on( "click", ".regresar", function() {
		location.reload();
	});

    function UpdateCuenta(){
        $form = $(this);
        $.ajax({
				type: "POST",
				url: '../update/update_cuenta.php',
				data: $form.serialize(),
				dataType: 'json',
				success: function(data) {
					alert(data.msg);
				}
			});
        return false;
    }

}) (jQuery);
