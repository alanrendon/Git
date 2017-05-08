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
<link href="/lecaroz/styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />


<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>


<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>

<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<script type="text/javascript">
	window.addEvent('domready', function() {
		new FormValidator($('Datos'), {
			showErrors: true,
			selectOnFocus: true
		});
		$('date_range1').select();
		$('pdf').addEvent('click', Pdf);
		$('buscar').addEvent('click', Buscar);
		$('limpiar').addEvent('click', Limpiar);
		$$('.elim').addEvent('click', Eliminar);
		$$('.mod').addEvent('click', Update_line);
		$$('.updates').addEvent('click', View_updates);
		$('date_range1').addEvents({
			'keydown': function(e) {
				
				if (e.key == 'enter' || e.key == 'right') {
					$('date_range2').select();
					e.stop();
				}
				if (e.key == 'down') {
					$('label').select();
					e.stop();
				}
			}
		});
		$('date_range2').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('label').select();
					e.stop();
				}
				if (e.key == 'left') {
					$('date_range1').select();
					e.stop();
				}
			}
		});
		$('label').addEvents({
			'keydown': function(e) {
				if (e.key == 'up') {
					$('date_range1').select();
					e.stop();
				}
			}
		});
	});






	var Limpiar = function () {
		$('Datos').reset();
		$('date_range2').set("value","");
		$('date_range1').set("value","");
	}
	var Eliminar = function () {
		var id= this.get("atrib");
		if (confirm('¿Esta seguro de borrar el registro?')) {
			new Request({
				'url': 'ReparacionesView.php',
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

	var Pdf =function() {

		var date_range1= $('date_range1').get('value');
		var date_range2= $('date_range2').get('value');
		var label= $('label').get('value');

		if (date_range1!="") {
			date_range1="&date_range1="+date_range1;
		}

		if (date_range2!="") {
			date_range2="&date_range2="+date_range2;
		}

		if (label!="") {
			label="&label="+label;
		}

		window.open("PDF.php?type=0"+date_range1+date_range2+label);
	}

	var Buscar = function() {
		
		new Request({
			'url': 'ReparacionesView.php',
			'data': {
				'accion': 'buscar',
				'label': $('label').get('value'),
				'date_range1': $('date_range1').get('value'),
				'date_range2': $('date_range2').get('value')
			} ,
			'onSuccess': function(result) {

				document.getElementById("list").innerHTML=result;
				$$('.elim').addEvent('click', Eliminar);
				$$('.mod').addEvent('click', Update_line);
				$$('.updates').addEvent('click', View_updates);
			}
		}).send();
		
	}
	var Modificar = function() {

		if ($('nombre_pro').get('value') == "") {
			alert('Especifique un numero de proveedor valido');
			$('proveedor').focus();
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
					'url': 'ReparacionesView.php',
					'data': 'accion=modif&' + $('Datos').toQueryString(),
					'onSuccess': function(result) {
						document.getElementById("cre").innerHTML=result;
					}
				}).send();
			}
		}
	}

	var Update_line = function() {
		var id= this.get("atrib");
		new Request({
			'url': 'ReparacionesView.php',
			'data': {
				'accion': 'actualizar_view',
				'id': id
			} ,
			'onSuccess': function(result) {
				document.getElementById("contenedor").innerHTML=result;
				


				f = new Formulario('Datos');
				$('modificar').addEvent('click', Modificar);
				$('proveedor').select();

				$('proveedor').addEvents({
					'change': validarPro,
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
			}
		}).send();
	}

	var View_updates = function() {
		var id= this.get("atrib");

		new Request({
			'url': 'ReparacionesView.php',
			'data': {
				'accion': 'view_updates',
				'id': id
			} ,
			'onSuccess': function(result) {

				document.getElementById("contenedor").innerHTML=result;
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
	<div id="titulo">Lista de Reparaciones</div>
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
							<input name="date_range1" type="text" class="valid Focus toDate center" id="date_range1" size="20" maxlength="20" value="{date_range1}"  >
						</td>
						<td>
							-
						</td>
						<td>
							<input name="date_range2" type="text" class="valid Focus toDate center" id="date_range2" size="20" maxlength="10" value="{date_range2}"  >
						</td>
					</tr>
					<tr class="linea_off">
						<td align="left" >No. Reparación</td>
						<td colspan="2">
							<input name="label" type="text" class="valid cleanText toUpper" id="label" size="16" >
						</td>
						<td>
							<input type="button" name="buscar" class="boton" id="buscar" value="Buscar">
							<input type="button" name="limpiar" class="boton" id="limpiar" value="Limpiar">
							<input type="button" name="pdf" class="boton" id="pdf" value="PDF">
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<table class="tabla_captura" style="min-width: 800px; max-width: 1300px;">
			<thead>
				<tr>
					<th>No. Reparación</th>
					<th>Proveedor</th>
					<th>Precio del Proveedor</th>
					<th>Descripción</th>
					<th>Descripción</th>
					<th>Observaciones</th>
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
