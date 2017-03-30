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
		$('buscar').addEvent('click', Buscar);
		$('limpiar').addEvent('click', Limpiar);
		$$('.elim').addEvent('click', Eliminar);
		$$('.mod').addEvent('click', Update_line);
		$$('.updates').addEvent('click', View_updates);
		
	});
	var Limpiar = function () {
		$('Datos').reset();
	}
	var Eliminar = function () {
		var id= this.get("atrib");
		if (confirm('¿Esta seguro de borrar el registro?')) {
			new Request({
				'url': 'RefaccionesView.php',
				'data': {
					'accion': 'eliminar',
					'id': id
				} ,
				'onSuccess': function(result) {
					alert("Registro Eliminado");
					document.getElementById("list").innerHTML=result;
					$$('.elim').addEvent('click', Eliminar);
					
				}
			}).send();
		}
	}

	var Buscar = function() {
		
		new Request({
			'url': 'RefaccionesView.php',
			'data': {
				'accion': 'buscar',
				'num_part': $('num_part').get('value'),
				'date_range1': $('date_range1').get('value'),
				'date_range2': $('date_range2').get('value')
			} ,
			'onSuccess': function(result) {
				document.getElementById("list").innerHTML=result;
				$$('.elim').addEvent('click', Eliminar);
			}
		}).send();
		
	}
	var Modificar = function() {
		if ($('price').get('value').getVal() < 0) {
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
					'url': 'RefaccionesView.php',
					'data': 'accion=modif&' + $('Datos').toQueryString(),
					'onSuccess': function(result) {
						alert(result);
					}
				}).send();
			}
		}
	}

	var Update_line = function() {
		var id= this.get("atrib");
		new Request({
			'url': 'RefaccionesView.php',
			'data': {
				'accion': 'actualizar_view',
				'id': id
			} ,
			'onSuccess': function(result) {
				document.getElementById("contenedor").innerHTML=result;
				$('modificar').addEvent('click', Modificar);
			}
		}).send();
	}

	var View_updates = function() {
		var id= this.get("atrib");

		new Request({
			'url': 'RefaccionesView.php',
			'data': {
				'accion': 'view_updates',
				'id': id
			} ,
			'onSuccess': function(result) {
				document.getElementById("contenedor").innerHTML=result;
			}
		}).send();
	}
	
</script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Lista de Refacciones</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos" action="ss">
			
			<table class="tabla_captura">
				<tbody>
					<tr>
						<th colspan="4" align="left" scope="row"><img src="/lecaroz/iconos/magnify.png" width="16" height="16"> Filtros de Busqueda</th>
					</tr>
					
					<tr class="linea_on">
						<td align="left" >Fecha</td>
						<td>
							<input name="date_range1" type="date" class="valid onlyText cleanText toUpper" id="date_range1" size="20" >
						</td>
						<td>
							-
						</td>
						<td>
							<input name="date_range2" type="date" class="valid onlyText cleanText toUpper" id="date_range2" size="20" >
						</td>
					</tr>
					<tr class="linea_off">
						<td align="left" >No. Parte</td>
						<td colspan="2">
							<input name="num_part" type="text" class="valid onlyText cleanText toUpper" id="num_part" size="16" >
						</td>
						<td>
							<input type="button" name="buscar" id="buscar" value="Buscar">
							<input type="button" name="limpiar" id="limpiar" value="Limpiar">
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<table class="tabla_captura" style="min-width: 800px; max-width: 1300px;">
			<thead>
				<tr>
					<th>No. Parte</th>
					<th>Precio</th>
					<th>Fecha de creación</th>
					<th><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16"></th>
				</tr>
			</thead>
			<tbody id="list" name="list">
				{list}
			</tbody>
		</table>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
