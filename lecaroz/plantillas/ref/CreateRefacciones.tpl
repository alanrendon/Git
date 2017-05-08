<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Refacción</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript">
	window.addEvent('domready', function() {
		
		f = new Formulario('Datos');
		new FormValidator($('Datos'), {
			showErrors: true,
			selectOnFocus: true
		});

		$('label').select();
		$('alta').addEvent('click', Alta);
		$('borrar').addEvent('click', Limpiar);


		$('label').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('price').select();
					e.stop();
				}
			}
		});
		$('price').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('fecha').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('label').select();
					e.stop();
				}
			}
		});
		$('fecha').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('description').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('price').select();
					e.stop();
				}
			}
		});
		$('description').addEvents({
			'keydown': function(e) {
				if (e.key == 'down') {
					$('observations').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('fecha').select();
					e.stop();
				}
			}
		});
		$('observations').addEvents({
			'keydown': function(e) {
				if (e.key == 'up') {
					$('description').select();
					e.stop();
				}
			}
		});

	});
	
	var Limpiar = function () {
		document.getElementById("cre").innerHTML="";
		$('Datos').reset();
	}


	var Alta = function() {

		new Request({
			'url': 'RefaccionesCreate.php',
			'data': {
				'accion': 'verif_label',
				'label': $('label').get('value')
			} ,
			'onSuccess': function(result) {
				if (result == -1) {
					alert("El numero de parte: "+$('label').get('value')+" ya existe.");
				}else if ($('label').get('value').clean() == '') {
					alert('Debe expecificar el campo "No. Parte"');
					$('label').focus();
				}
				else if ($('price').get('value').getVal() < 0) {
					alert('Debe expecificar el campo "Precio"');
					$('price').focus();
				}else if ($('description').get('value').clean() == '') {
					alert('Debe expecificar el campo "Descripción"');
					$('description').focus();
				}else if ($('observations').get('value').clean() == '') {
					alert('Debe expecificar el campo "Observaciones"');
					$('observations').focus();
				}
				else {
					if (confirm('¿Son correctos todos los datos?')) {
						new Request({
							'url': 'RefaccionesCreate.php',
							'data': 'accion=alta&' + $('Datos').toQueryString(),
							'onSuccess': function(result) {
								document.getElementById("cre").innerHTML=result;
								$('Datos').reset();
								
								new Request({
									'url': 'RefaccionesCreate.php',
									'data': 'accion=get_ref',
									'onSuccess': function(result) {
										
										document.getElementById("label").value = result;
									}
								}).send();
							}
						}).send();
					}
				}
			}
		}).send();

		
	}
	
</script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Alta de Refacciones</div>
	<div id="cre" align="center">

	</div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<table class="tabla_captura">
				<tbody>
				<tr>
					<th colspan="2" align="left" scope="row">
						<img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General
					</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">No de Refacción</th>
					<td class="linea_on">
						<input name="label" type="text" class="cap toText toUpper alignLeft bold fontSize12pt" id="label" size="20" maxlength="50" value="{ref}" >
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Precio</th>
					<td>
						<input name="price" type="text" class="cap numPosFormat2 alignLeft bold" id="price" value="0.00" size="5" align="left" maxlength="20">
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Fecha</th>
					<td class="linea_on">
						<input name="fecha" type="text" class="valid Focus toDate center" id="fecha" size="20" maxlength="20" value="" >
					</td>
				</tr>
				<tr>
					<th colspan="2" align="left" scope="row">&nbsp;</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Descripción</th>
					<td>
						<textarea name="description" cols="40" rows="5" class="cap toText toUpper clean" id="description" style="width:98%;"></textarea>
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Observaciones</th>
					<td>
						<textarea name="observations" cols="50" rows="5" class="cap toText toUpper clean" id="observations" style="width:98%;"></textarea>
					</td>
				</tr>
			</tbody></table>
			<p>
				<input type="button" name="borrar" id="borrar" class="boton" value="Borrar Información">
				&nbsp;&nbsp;
				<input type="button" name="alta" id="alta" class="boton" value="Alta de Refacción">
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
