<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Metodo de Pago</title>



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
		$('date_range1').set("value","");
		$('date_range2').set("value","");
		$('label').set("value","");
		
	}
	var Eliminar = function () {
		var id= this.get("atrib");
		if (confirm('¿Esta seguro de borrar el registro?')) {
			new Request({
				'url': 'CatpView.php',
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
			'url': 'CatpView.php',
			'data': {
				'accion': 'buscar',
				'label': $('label').get('value'),
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

		if ($('observations').get('value').clean() == '') {
			alert('Debe expecificar el campo "Observaciones"');
			$('observations').focus();
		}
		else {
			if (confirm('¿Son correctos todos los datos?')) {
				new Request({
					'url': 'CatpView.php',
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
			'url': 'CatpView.php',
			'data': {
				'accion': 'actualizar_view',
				'id': id
			} ,
			'onSuccess': function(result) {
				document.getElementById("contenedor").innerHTML=result;
				


				f = new Formulario('Datos');
				new FormValidator($('Datos'), {
					showErrors: true,
					selectOnFocus: true
				});
				$('modificar').addEvent('click', Modificar);
				$('price').select();
				$('price').addEvents({
					'keydown': function(e) {
						if (e.key == 'enter' || e.key == 'down') {
							$('fecha').select();
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
			}
		}).send();
	}

	var View_updates = function() {
		var id= this.get("atrib");

		new Request({
			'url': 'CatpView.php',
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
	<div id="titulo">Lista de Métodos de Pago</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos" action="">
			
			<table class="tabla_captura">
				<tbody>
					<tr>
						<th colspan="4" align="left" scope="row"><img src="/lecaroz/iconos/magnify.png" width="16" height="16"> Filtros de Busqueda</th>
					</tr>
					
					<tr class="linea_on">
						<td align="left" >Fecha</td>
						<td>
							<input name="date_range1" type="text" class="valid Focus toDate center" id="date_range1" size="20" maxlength="20" value="{date_range1}" >
						</td>
						<td>
							-
						</td>
						<td>
							<input name="date_range2" type="text" class="valid Focus toDate center" id="date_range2" size="20" maxlength="10" value="{date_range2}" >
						</td>
					</tr>
					<tr class="linea_off">
						<td align="left" >Nombre</td>
						<td colspan="2">
							<input name="label" type="text" class="valid cleanText toUpper" id="label" size="16" >
						</td>
						<td>
							<input type="button" name="buscar" class="boton" id="buscar" value="Buscar">
							<input type="button" name="limpiar" class="boton" id="limpiar" value="Limpiar">
							
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<table class="tabla_captura" style="min-width: 800px; max-width: 1300px;">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Clave</th>
					<th>Observaciones</th>
					<th>Fecha</th>
					
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
