$(function() {

	$( "#builddoc_generatebutton" ).attr("type","button");
	
	$( "#builddoc_generatebutton" ).click(function() {
		var loc = window.location;
		var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf("/") + 1);
		var url = loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
		loc = String(loc);
		loc = loc.split("=");
		url = url + "product/pdf/index.php?id="+loc[1];
		window.location.href = url;
	});

	$( ".validar_almacen" ).click(function() {
		var id = $(this).attr('id');
		$.ajax({
			data:"id="+id ,
			url: "validEntrepot.php",
			type: "POST",
			success: function(data) {
				location.reload(true);
			}
		});
	});

	$( "input[name=\"generar_series\"]" ).click(function() {
		var numero_inicial = $( "input[name=\"numero_inicial_consecutivo\"]" ).val();
		var nbToBuild = $( "input[name=\"nbToBuild\"]" ).val();
		var d = new Date();
		var month = d.getMonth()+1;
		var year = d.getYear();
		month = (month < 10) ? '0'+month : month;
		year = year.toString().substr(1);
		var respuesta = '';
		do {
			respuesta += '['+year+month+'-'+numero_inicial+']';
			nbToBuild--;
			numero_inicial++;
		}while( nbToBuild > 0 );
		$( "textarea[name=\"no_serie\"]" ).val(respuesta);
		var no_serie = $( "textarea[name=\"no_serie\"]" ).val();
		if( no_serie.length > 0 ) {
			$( "#div_alerta" ).fadeOut();
		}
		else {
			$( "#div_alerta" ).fadeIn();	
		}
	});

	$( "input[name=\"generar\"]" ).click(function() {
		var numero_inicial = $( "input[name=\"numero_inicial_consecutivo\"]" ).val();
		var qtymade = $( "input[name=\"qtymade\"]" ).val();
		var d = new Date();
		var month = d.getMonth()+1;
		var year = d.getYear();
		month = (month < 10) ? '0'+month : month;
		year = year.toString().substr(1);
		var respuesta = '';
		do {
			respuesta += '['+year+month+'-'+numero_inicial+']';
			qtymade--;
			numero_inicial++;
		}while( qtymade > 0 );
		$( "textarea[name=\"no_serie\"]" ).val(respuesta);
		var no_serie = $( "textarea[name=\"no_serie\"]" ).val();
		if( no_serie.length > 0 ) {
			$( "#div_alerta" ).fadeOut();
		}
		else {
			$( "#div_alerta" ).fadeIn();	
		}
	});	

	$( "textarea[name=\"no_serie\"]" ).keyup(function() {
		var no_serie = $(this).val();
		if( no_serie.length > 0 ) {
			$( "#div_alerta" ).fadeOut();
		}
		else {
			$( "#div_alerta" ).fadeIn();	
		}
	});	

	/*$( "input[name=\"verifyof\"]" ).click(function(e) {
		e.preventDefault();
		var no_serie = $( "textarea[name=\"no_serie\"]" ).val();
		if( no_serie.length <= 0 ) {
			console.log('hola-'+no_serie.length);
		}
		else {
			console.log('hola2-'+no_serie.length);
			$( "form[name=\"createof\"]" ).submit();
		}
	});	*/

});