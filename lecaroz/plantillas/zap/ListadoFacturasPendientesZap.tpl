<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de Facturas Pendientes </title>
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
</head>

<body>
<div id="contenedor">
  <div id="titulo">
    Listado de Facturas Pendientes </div>
  <div id="captura" align="center">
    <form action="ListadoFacturasPendientesZap.php" method="get" name="Datos" target="listado" class="formulario" id="Datos">
      <table class="tabla_captura">
	    <tr>
          <th scope="row">Compa&ntilde;&iacute;a</th>
          <td><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th scope="row">Periodo</th>
          <td><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" /> al <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
        </tr>
      </table>
      <p>
        <input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: function() {
			if (this.value.toInt() == 0) {
				this.set('value', '');
				f.form.nombre.set('value', '');
			}
			else {
				new Request({
					url: 'ListadoFacturasPendientesZap.php',
					method: 'get',
					data: {
						c: this.value
					},
					onSuccess: function(nombre) {
						if (nombre == '') {
							alert('La compañía no se encuentra en el catálogo');
							$('num_cia').set('value', '');
							$('nombre').set('value', '');
						}
						else
							$('nombre').set('value', nombre);
					}
				}).send();
			}
		},
		keydown: function(e) {
			if (e.key == 'enter')
				$('fecha1').select();
		}
	});
	
	$('fecha1').addEvent('keydown', function(e) {
		if (e.key == 'enter')
			$('fecha2').select();
	});
	
	$('fecha2').addEvent('keydown', function(e) {
		if (e.key == 'enter')
			$('num_cia').select();
	});
	
	$('consultar').addEvent('click', function() {
		
		var win = window.open('', 'listado', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		f.form.submit();
		win.focus();
	});
	
	$('num_cia').select();
});
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
