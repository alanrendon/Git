(function($){
    $.get( "../get/get_cuentas.php", function( data ) {
        $("#cuentas").html(data);
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
    
    $( "#guardar_ctastock" ).on( "click", function(e) {
		e.preventDefault();
		$("#mensaje").html('');
		//var form = $('#form_ctamovimientos').serialize();
		var cuenta=document.getElementsByName("cuenta")[0].value;
		var tpmov=document.getElementsByName("tpmovimiento")[0].value;
		var form= "tipo="+tpmov+"&datos[]="+tpmov+"&cuenta="+cuenta;
		//alert(form);
		$.ajax( {
			type: "POST",
			url: '../get/carga_cuenta_rel.php',
			data: form,
			dataType: "Json",
			success: function(data) {
				var mensaje = (data["json"] == 1) ? 'La cuenta se registró correctamente.' : 'Ocurrió un problema, posiblemente la cuenta ya este registrada.';
				$("#mensaje").html(mensaje);
				$('#form_ctamovimientos').trigger("reset");
				setTimeout(function(){ location.reload(); }, 2000);
			}
		});
	});
    
    $.get( "../get/get_stock_cuentas.php", function( data ) {
		$( "#listado_cuentastock" ).html(data).fadeIn('slow');
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

    $( document ).on( "click", ".delete-link", function() {
		var id = $(this).attr('id');
		var tr = $(this).parent().parent().parent().parent();
		if (confirm("¿Está seguro de eliminar la relación de este tipo de movimiento de Stock?")) {
			$.ajax({
				type: "POST",
				url: '../get/eliminar_relacion_cuenta.php',
				data: "id="+id,
				success: function(data) {
					tr.remove();
					setTimeout(function(){ location.reload(); }, 2000);
				}
			});
		}
	});

}) (jQuery);