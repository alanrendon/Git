<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta Anual de Pagos de Agua</title>
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
  <div id="titulo">Consulta Anual de Pagos de Agua </div>
  <div id="captura" align="center">
    <form action="ConsultaAnualPagosAgua.php" method="post" name="Datos" target="listado" class="formulario" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td align="left"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Administrador</th>
          <td align="left"><select name="idadmin" id="idadmin">
            <option value="-" selected="selected">-</option>
            <!-- START BLOCK : admin -->
			<option value="{id}">{admin}</option>
			<!-- END BLOCK : admin -->
          </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">A&ntilde;o</th>
          <td align="left"><input name="anio" type="text" class="cap toPosInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Bimestre</th>
          <td align="left"><input name="bimestre" type="text" class="cap toPosInt alignCenter" id="bimestre" value="{bimestre}" size="1" maxlength="1" /></td>
        </tr>
      </table>
      <p>
        <input name="listado" type="button" class="boton" id="listado" value="Listado" />
      </p>
    </form>
  </div>
</div>
<!-- START IGNORE -->
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
				$('anio').focus();
			}
		}
	});
	
	$('anio').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('bimestre').focus();
			}
		}
	});
	
	$('bimestre').addEvents({
		change: function() {
			if (this.get('value').getVal() > 6) {
				alert('El valor del bimestre debe estar comprendido entre 1 y 6');
				$('bimestre').set('value', '');
				$('bimestre').focus();
			}
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').focus();
			}
		}
	});
	
	$('listado').addEvent('click', function() {
		if ($('anio').get('value').getVal() == 0) {
			alert('Debe especificar el año');
			$('anio').focus();
		}
		else if ($('bimestre').get('value').getVal() == 0) {
			alert('Debe especificar el bimestre');
			$('bimestre').focus();
		}
		else {
			var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768';
			var win = window.open('', 'listado', opt);
			
			f.form.submit();
			win.focus();
		}
	});
	
	$('num_cia').focus();
});

var cambiaCia = function() {
	var num_cia = $('num_cia').get('value').getVal();
	
	if (num_cia == 0) {
		$('num_cia').set('value', '');
		$('nombre').set('value', '');
	}
	else {
		new Request({
			url: 'ConsultaAnualPagosAgua.php',
			method: 'post',
			data: {
				accion: 'retrieveCia',
				num_cia: num_cia
			},
			onSuccess: function(result) {
				if (result == '') {
					alert('La compañía no se encuentra en el catálogo');
					
					$('num_cia').set('value', '');
					$('nombre').set('value', '');
				}
				else
					$('nombre').set('value', result);
			}
		}).send();
	}
}
//-->
</script>
<!-- END IGNORE -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
