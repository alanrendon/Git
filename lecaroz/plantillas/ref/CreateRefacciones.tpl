<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Refacción</title>
<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/styles/Popups.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
<script type="text/javascript">
	window.addEvent('domready', function() {
		$('alta').addEvent('click', Alta);
		$('borrar').addEvent('click', Limpiar);
	});
	
	var Limpiar = function () {
		$('Datos').reset();
	}


	var Alta = function() {

		new Request({
			'url': 'RefaccionesCreate.php',
			'data': {
				'accion': 'verif_label',
				'num_part': $('num_part').get('value')
			} ,
			'onSuccess': function(result) {
				if (result == -1) {
					alert("El numero de parte: "+$('num_part').get('value')+" ya existe.");
				}else if ($('num_part').get('value').clean() == '') {
					alert('Debe expecificar el campo "No. Parte"');
					$('num_part').focus();
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
								alert(result);
								$('Datos').reset();
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
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos" action="ss">
			<table class="tabla_captura">
				<tbody>
				<tr>
					<th colspan="2" align="left" scope="row"><img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General</th>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">No. Parte</th>
					<td><input name="num_part" type="text" class="valid onlyText cleanText toUpper" id="num_part" size="20" maxlength="50"></td>
				</tr>
				<tr class="linea_off">
					<th align="left" scope="row">Precio</th>
					<td>
						<input name="price" type="text" class="cap numPosFormat2 alignRight" id="price" value="0.00" size="5" align="left" maxlength="20">
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
				<input type="button" name="borrar" id="borrar" value="Borrar Información">
				&nbsp;&nbsp;
				<input type="button" name="alta" id="alta" value="Alta de Refacción">
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
