$.get( "../get/get_proveedor_societe.php", function( data ) {
		$("#proveedores").html(data);
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

	$.ajax({
        url: '../get/get_selects_operation_societe.php',
        dataType: 'html',
        type: 'post',
        data: {type:'get_list_typesociete'},
        success: function (data) {
            $('#div_dibuja').html(data);
            $(".select2_single").select2({
                placeholder: "Seleccione una opción"
            });
            $.ajax({
                url: '../get/get_selects_operation_societe.php',
                dataType: 'html',
                data: {type:'type_societe', 'fk_type_societe': $('#id_typeoperation').val(), 'idp':$('#idp').val(),'primera':'1'},
                type: 'post',
                success: function (data) {
                    $('#get_list_typeoperation').html(data);
                    $(".select2_single").select2({
                        placeholder: "Seleccione una opción"
                    });
                    }
                });
        }

    });

     $('#tipo_doc').on('change',function (e){
        $('#div_dibuja').html('');
        if($('#tipo_doc').val() == 'cfdixml'){
            $.ajax({
                url: '../get/get_selects_operation_societe.php',
                dataType: 'html',
                data: {type:'get_list_typesociete', 'idp':$('#idp').val()},
                type: 'post',
                success: function (data) {
                    $('#div_dibuja').html(data);
                    $(".select2_single").select2({
                        placeholder: "Seleccione una opción"
                    });
                     $('#get_list_typeoperation').html('');
                        
                    
                }
            });
        }
    });

    function get_list_typeoperation(){
        $('#get_list_typeoperation').html('');         
        $.ajax({
            url: '../get/get_selects_operation_societe.php',
            dataType: 'html',
            data: {type:'type_societe', 'fk_type_societe': $('#id_typeoperation').val(), 'idp':$('#idp').val()},
            type: 'post',
            success: function (data) {
                 $('#get_list_typeoperation').html(data);
                $(".select2_single").select2({
                    placeholder: "Seleccione una opción"
                });
            }
        });
    }

    $('#registrar_tiposociedad_operacion').on('submit',function(event) {
    	event.preventDefault();
    	$.ajax({
    		url:'../put/put_societe_operation_fourn.php',
    		dataType: 'json',
    		data:$('#registrar_tiposociedad_operacion').serialize(),
    		type:'POST',
    		success: function(data){
    			if (data.correcto) {
    				alert(data.correcto);
    				location.reload(); 
    			}else{
    				alert(data.error);
    			}
    		}
    	});
    });

    $.get( "../get/get_proveedores_sociedad.php", function( data ) {
		$( "#listado_proveedores" ).html(data).fadeIn('slow');
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
        if (confirm("¿Está seguro de eliminar la relación de este proveedor?")) {
            $.ajax({
                type: "POST",
                url: '../update/eliminar_relacion_sociedad.php',
                data: "id="+id,
                success: function(data) {
                    tr.remove();
                    setTimeout(function(){ location.reload(); }, 1000);
                }
            });
        }
    });
