<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Reparación</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript">
	window.addEvent('domready', function() {
		
		f = new Formulario('Datos');

		
		$('label').select();
		$('alta').addEvent('click', Alta);
		$('borrar').addEvent('click', Limpiar);


		$('label').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('proveedor').select();
					e.stop();
				}
			}
		});

		$('proveedor').addEvents({
			'change': validarPro,
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('price').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('label').select();
					e.stop();
				}
			}
		});



		$('price').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('description').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('proveedor').select();
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
					$('price').select();
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
			'url': 'ReparacionesCreate.php',
			'data': {
				'accion': 'verif_label',
				'label': $('label').get('value'),
				'proveedor': $('proveedor').get('value'),
			} ,
			'onSuccess': function(result) {
				if (result != -1) {
					alert(result);
				}else if ($('label').get('value').clean() == '') {
					alert('Debe expecificar el campo "Etiqueta"');
					$('label').focus();
				}
				else if ($('proveedor').get('value').getVal() == "" ) {
					alert('Especifique un numero de proveedor valido');
					$('price').focus();
				}else if ($('price').get('value').getVal() < 0) {
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
							'url': 'ReparacionesCreate.php',
							'data': 'accion=alta&' + $('Datos').toQueryString(),
							'onSuccess': function(result) {
								document.getElementById("cre").innerHTML=result;
								$('Datos').reset();
							}
						}).send();
					}
				}
			}
		}).send();
	}

	var validarPro = function() {
		if ($('proveedor').get('value') > 0) {
			new Request({
				'url': 'ReparacionesCreate.php',
				'data': 'accion=validarPro&num_pro=' + $('proveedor').get('value'),
				'onRequest': function() {
				},
				'onSuccess': function(result) {
					if (result != '') {
						var data = JSON.decode(result);
						$('nombre_pro').set('value', data.nombre_pro);
					}else{
						$('nombre_pro').set('value', "");
					}
				}
			}).send();
		}else{
			$('nombre_pro').set('value', "");
		}
	}


	
</script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Alta de Servicios de Reparación</div>
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
					<th align="left" scope="row">No. Reparación</th>
					<td class="linea_on">
						<input name="label" type="text" class="cap toText toUpper alignLeft bold fontSize12pt" id="label" size="20" maxlength="50" >
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Nombre del proveedor</th>
					<td class="linea_on">
						<input type="text" id="proveedor" name="proveedor" size="5" class="cap toPosInt alignLeft bold fontSize12pt" >
						<input name="nombre_pro" type="text" disabled="disabled" id="nombre_pro" size="20">
					</td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Precio del Proveedor</th>
					<td>
						<input name="price" type="text" class="cap numPosFormat2 alignLeft" id="price" value="0.00" size="5" align="left" maxlength="20">
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
				<input type="button" name="alta" id="alta" class="boton" value="Alta de Reparación">
			</p>
		</form>
		<div name="formulario" id="formulario" >
			
		</div>
	</div>

</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
