<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Chache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<title>Crear Pregunta</title>
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


		$('departamento_ref').select();


		$('alta').addEvent('click', Alta);
		$('borrar').addEvent('click', Limpiar);

		$('departamento_ref').addEvents({
			'change': validarDep,
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
					$('departamento_ref').select();
					e.stop();
				}
			}
		});
		$('pregunta').addEvents({
			'keydown': function(e) {
				if (e.key == 'enter' || e.key == 'down') {
					$('observations').select();
					e.stop();
				}
				if (e.key == 'up') {
					$('correo').select();
					e.stop();
				}
			}
		});
		
		$('observations').addEvents({
			'keydown': function(e) {
				if (e.key == 'up') {
					$('pregunta').select();
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
			'url': 'PreguntasCreate.php',
			'data': {
				'accion': 'verif_label',
				'pregunta': $('pregunta').get('value')
			} ,
			'onSuccess': function(result) {
				if ($('departamento').get('value').clean() == '') {
					alert('Elija un Departamento Válido');
					$('departamento').focus();
				}else if ($('pregunta').get('value').clean() == '') {
					alert('Debe expecificar el campo "Pregunta"');
					$('pregunta').focus();
				}else if ($('observations').get('value').clean() == '') {
					alert('Debe expecificar el campo "Observaciones"');
					$('observations').focus();
				}
				else {
					if (confirm('¿Son correctos todos los datos?')) {
						new Request({
							'url': 'PreguntasCreate.php',
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
	<div id="titulo">Alta de Pregunta</div>
	<div id="cre" align="center">

	</div>
	<div id="captura" align="center">
		<form name="Datos" class="formulario" id="Datos" >
			<input type="hidden" id="departamento" name="departamento" >
			<table class="tabla_captura">
				<tbody>
				<tr>
					<th colspan="2" align="left" scope="row">
						<img src="/lecaroz/imagenes/info.png" width="16" height="16"> Información General
					</th>
				</tr>
				
				<tr class="linea_on">
					<th align="left" scope="row">Departamento</th>

					<td class="linea_on">
						<input type="text" id="departamento_ref" name="departamento_ref" size="5" class="cap toPosInt alignLeft bold fontSize12pt" >
						<input name="nombre_departamento" type="text" disabled="disabled" id="nombre_departamento" size="20">
					</td>
				</tr>
				
				

				<tr class="linea_off">
					<th align="left" scope="row">Periodicidad</th>
					<td>
						<select name="peri" id="peri" class="">
				          <option value="1">DIARIO</option>
				          <option value="2" style="background-color:#EEE;">SEMANAL</option>
				          <option value="3">MARZO</option>
				          <option value="4" selected="" style="background-color:#EEE;">MENSUAL</option>
				          <option value="5">MAYO</option>
				          <option value="6" style="background-color:#EEE;">ANUAL</option>
				        </select>
					</td>
				</tr>


				
				<tr class="linea_off">
					<th align="left" scope="row">Enviar Correo al Contestar?</th>
					<td>
						<input type="checkbox" name="correo" id="correo" > 
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Pregunta</th>
					<td class="linea_on">
						
						<textarea name="pregunta" cols="50" rows="5" class="cap toText clean" id="pregunta" style="width:98%;"></textarea>
					</td>
				</tr>
				<tr class="linea_on">
					<th align="left" scope="row">Observaciones</th>
					<td>
						<textarea name="observations" cols="50" rows="5" class="cap toText clean" id="observations" style="width:98%;"></textarea>
					</td>
				</tr>
				
			</tbody></table>
			<p>
				<input type="button" name="borrar" id="borrar" class="boton" value="Borrar Pregunta">
				&nbsp;&nbsp;
				<input type="button" name="alta" id="alta" class="boton" value="Alta de Pregunta">
			</p>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
