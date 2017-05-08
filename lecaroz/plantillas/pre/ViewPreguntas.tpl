<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Pregunta</title>



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
		$('departamento').select();
		$('buscar').addEvent('click', Buscar);
		$('limpiar').addEvent('click', Limpiar);
		$$('.elim').addEvent('click', Eliminar);
		$$('.mod').addEvent('click', Update_line);
		$('pdf').addEvent('click', Pdf);
		$$('.updates').addEvent('click', View_updates);

		$('departamento').addEvents({
			'keydown': function(e) {

				if (e.key == 'down') {
					$('correo').select();
					e.stop();
				}
			}
		});
		$('correo').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('pregunta').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('departamento').select();
					e.stop();
				}
			}
		});

	});



	var Pdf =function() {
		var departamento="";
		if ($('departamento').get('value')!="") {
			departamento='&departamento='+ $('departamento').get('value');
		}
		var peri="";
		if ($('peri').get('value')!="") {
			peri='&peri='+$('peri').get('value');
		}

		var correo='&correo='+document.getElementById("correo").checked;
		window.open("PDF.php?mot=pre"+departamento+peri+correo);
	}


	var Limpiar = function () {
		$('Datos').reset();
		$('date_range1').set("value","");
		$('date_range2').set("value","");
	}
	var Eliminar = function () {
		var id= this.get("atrib");
		if (confirm('¿Esta seguro de borrar el registro?')) {
			new Request({
				'url': 'PreguntasView.php',
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
			'url': 'PreguntasView.php',
			'data': {
				'accion': 'buscar',
				'departamento': $('departamento').get('value'),
				'peri': $('peri').get('value'),
				'correo': document.getElementById("correo").checked
			} ,
			'onSuccess': function(result) {
				document.getElementById("list").innerHTML=result;
				$$('.elim').addEvent('click', Eliminar);
			}
		}).send();
		
	}
	var Modificar = function() {

		if ($('departamento').get('value').clean() == '') {
			alert('Debe expecificar el campo "Departamento"');
			$('departamento').focus();
		}else if ($('observations').get('value').clean() == '') {
			alert('Debe expecificar el campo "Observaciones"');
			$('observations').focus();
		}
		else {
			if (confirm('¿Son correctos todos los datos?')) {
				new Request({
					'url': 'PreguntasView.php',
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
			'url': 'PreguntasView.php',
			'data': {
				'accion': 'actualizar_view',
				'id': id
			} ,
			'onSuccess': function(result) {
				document.getElementById("contenedor").innerHTML=result;
				


				f = new Formulario('Datos');
				$('modificar').addEvent('click', Modificar);
				$('departamento_ref').select();


				$('departamento_ref').addEvents({
					'change': validarDep,
					'keydown': function(e) {
						if (e.key == 'enter' || e.key == 'down') {
							$('correo').select();
							e.stop();
						}
					}
				});
				$('correo').addEvents({
					'keydown': function(e) {
						if (e.key == 'down') {
							$('observations').select();
							e.stop();
						}
						if (e.key == 'up') {
							$('departamento_ref').select();
							e.stop();
						}
					}
				});
				
				$('observations').addEvents({
					'keydown': function(e) {
						if (e.key == 'up') {
							$('correo').select();
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
			'url': 'PreguntasView.php',
			'data': {
				'accion': 'view_updates',
				'id': id
			} ,
			'onSuccess': function(result) {

				document.getElementById("contenedor").innerHTML=result;
			}
		}).send();
	}

	var validarDep = function() {

		if ($('departamento_ref').get('value') > 0) {
			new Request({
				'url': 'PreguntasCreate.php',
				'data': 'accion=validarDep&departamento=' + $('departamento_ref').get('value'),
				'onRequest': function() {
				},
				'onSuccess': function(result) {
					if (result != '') {
						var data = JSON.decode(result);
						$('nombre_departamento').set('value', data.nombre_pro);
						$('departamento').set('value', data.id_depto);
					}else{
						$('nombre_departamento').set('value', "");
						$('departamento').set('value', "");
					}
				}
			}).send();
		}else{
			$('nombre_departamento').set('value', "");
			$('departamento').set('value', "");
		}
	}
	
</script>
</head>

<body>
<div id="contenedor">
	<div id="titulo">Lista de Preguntas</div>
	<div id="captura" align="center">
		<form name="Datos" class="FormValidator FormStyles" id="Datos" >
			
			<table class="tabla_captura">
				<tbody>
					<tr>
						<th colspan="4" align="left" scope="row"><img src="/lecaroz/iconos/magnify.png" width="16" height="16"> Filtros de Busqueda</th>
					</tr>
					
					<tr class="linea_off">
						<td align="left" >Departamento</td>
						<td colspan="2">
							<input name="departamento" type="text" class="valid onlyText cleanText toUpper" id="departamento" size="16" >
						</td>
					</tr>
					<tr class="linea_off">
						<td align="left" >Periocidad</td>
						<td colspan="2">
							<select name="peri" id="peri" class="">
							  <option value="0" style="background-color:#EEE;" selected>Todas</option>
					          <option value="1">DIARIO</option>
					          <option value="2" style="background-color:#EEE;">SEMANAL</option>
					          <option value="3">MARZO</option>
					          <option value="4" style="background-color:#EEE;">MENSUAL</option>
					          <option value="5">MAYO</option>
					          <option value="6" style="background-color:#EEE;">ANUAL</option>

					        </select>
						</td>
					</tr>
					<tr class="linea_off">
						<td align="left" >Enviar Correo al Contestar?</td>
						<td>
							<input type="checkbox" name="correo" id="correo" >
							
						</td>
					</tr>
					<tr class="linea_off">
						
						<td align="center" colspan="2">
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
					<th>Pregunta</th>
					<th>Departamento</th>
					<th>Periodicidad</th>
					<th>Enviar Correo?</th>
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
