<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Listado de Diferencias de Inventario </title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Listado de Diferencias de Inventario </div>
  <div id="captura" align="center">
    <form action="ListadoDiferenciasInventario.php" method="get" name="Datos" target="listado" class="formulario" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td align="left" class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Administrador</th>
          <td align="left" class="linea_on"><select name="idadmin" id="idadmin">
            <option value=""></option>
			<!-- START BLOCK : admin -->
            <option value="{id}">{admin}</option>
			<!-- END BLOCK : admin -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">A&ntilde;o</th>
          <td align="left" class="linea_off"><input name="anio" type="text" class="cap toPosInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Mes</th>
          <td align="left" class="linea_on"><select name="mes" id="mes">
              <option value="1"{1}>ENERO</option>
              <option value="2"{2}>FEBRERO</option>
              <option value="3"{3}>MARZO</option>
              <option value="4"{4}>ABRIL</option>
              <option value="5"{5}>MAYO</option>
              <option value="6"{6}>JUNIO</option>
              <option value="7"{7}>JULIO</option>
              <option value="8"{8}>AGOSTO</option>
              <option value="9"{9}>SEPTIEMBRE</option>
              <option value="10"{10}>OCTUBRE</option>
              <option value="11"{11}>NOVIEMBRE</option>
              <option value="12"{12}>DICIEMBRE</option>
            </select>		  </td>
        </tr>
        <tr>
          <th align="left" scope="row">Consultar</th>
          <td align="left" class="linea_off"><input name="tipo" type="radio" class="checkbox" checked="checked" value="" /> Todos<br />
            <input name="tipo" type="radio" class="checkbox" value="TRUE" /> Controlados<br />
            <input name="tipo" type="radio" class="checkbox" value="FALSE" /> No controlados </td>
        </tr>
        <tr>
          <th align="left" scope="row">Opciones</th>
          <td align="left" class="linea_on"><input name="gas" type="checkbox" class="checkbox" id="gas" value="1" /> Incluir Gas </td>
        </tr>
        <tr>
          <th align="left" scope="row">Impresi&oacute;n</th>
          <td align="left" class="linea_on"><input name="doble_cara" type="checkbox" class="checkbox" id="doble_cara" value="1" /> Doble Cara </td>
        </tr>
      </table>
      <br />
      <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaCia,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('anio').select();
			}
		}
	});
	
	$('anio').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').select();
			}
		}
	});
	
	$('siguiente').addEvent('click', function() {
		if ($('anio').get('value').getVal() == 0) {
			alert('Debe especificar el año');
			$('anio').focus();
			return false;
		}
		else {
			var win = window.open('', 'listado', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
			win.focus();
			
			f.form.submit();
		}
	});
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	if ($('num_cia').get('value').toInt() > 0) {
		new Request({
			url: 'ListadoDiferenciasInventario.php',
			method: 'get',
			data: {
				c: $('num_cia').get('value')
			},
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('La compañía ' + $('num_cia').get('value') + ' no se encuentra en el catálogo');
					
					$('num_cia').set('value', '');
					$('nombre').set('value', '');
				}
				else
					$('nombre').set('value', nombre);
			}
		}).send();
	}
	else {
		$('num_cia').set('value', '');
		$('nombre').set('value', '');
	}
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
