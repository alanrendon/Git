<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Quejas de Pedidos</title>
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
  <div id="titulo"> Alta de Mensajes </div>
  <div id="captura" align="center">
    <form action="FormularioQuejas.php" method="post" name="Quejas" class="formulario" id="Quejas">
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="70" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo</th>
          <td class="linea_on"><select name="tipo" id="tipo">
            <option value="1" selected="selected">RECADO</option>
            <option value="2">REPORTE</option>
            <option value="3">PEDIDO</option>
          </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Clasificaci&oacute;n</th>
          <td class="linea_off"><select name="idclase" id="idclase">
            <option value="NULL" selected="selected"></option>
			<!-- START BLOCK : clase -->
			<option value="{id}">{concepto}</option>
			<!-- END BLOCK : clase -->
          </select>          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Reporta</th>
          <td class="linea_on"><input name="quejoso" type="text" class="cap toText toUpper" id="quejoso" style="width:100%;" size="50" maxlength="255" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Mensaje</th>
          <td class="linea_off"><textarea name="queja" cols="40" rows="5" class="cap toText toUpper" id="queja" style="width:100%;"></textarea></td>
        </tr>
      </table>
      <p>
        <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('Quejas');
	
	$('num_cia').addEvents({
		change: function() {
			if (this.get('value').getVal() > 0) {
				new Request({
					url: 'FormularioQuejas.php',
					method: 'get',
					data: {
						c: this.get('value')
					},
					onSuccess: function(nombre)
					{
						if (nombre == '') {
							alert('La compañía ' + this.get('value') + ' no se encuentra en el catálogo');
							
							this.set('value', '');
							$('nombre_cia').set('value', '');
						}
						else
							$('nombre_cia').set('value', nombre);
					}
				}).send();
			}
			else {
				this.set('value', '');
				$('nombre_cia').set('value', '');
			}
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				$('quejoso').select();
				e.stop();
			}
		}
	});
	
	$('quejoso').addEvents({
		change: function() {
			this.set('value', this.get('value').clean());
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				$('queja').focus();
				e.stop();
			}
		}
	});
	
	$('queja').addEvents({
		change: function() {
			this.set('value', this.get('value').clean());
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				f.form.num_cia.select();
				e.stop();
			}
		}
	});
	
	$('siguiente').addEvents({
		click: function() {
			if ($('num_cia').get('value').getVal() == 0) {
				alert('Debe especificar la compañía');
				$('num_cia').select();
				return false;
			}
			else if ($('quejoso').get('value').clean().length < 3) {
				alert('Debe escribir el nombre de la persona que se queja');
				$('quejoso').select();
				return false;
			}
			else if ($('queja').get('value').clean().length < 3) {
				alert('Debe escribir el porque de la queja');
				$('queja').focus();
				return false;
			}
			else if (confirm('¿Son correctos los datos?')) {
				f.form.submit();
			}
		}
	});
	
	f.form.num_cia.select();
});
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
