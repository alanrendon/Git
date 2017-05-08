<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Dispersi&oacute;n de Gastos de Caja</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Dispersi&oacute;n de Gastos de Caja </div>
  <div id="captura" align="center">
    <form action="DispersionGastosCaja.php" method="post" name="DispersionGastosCaja" class="formulario" id="DispersionGastosCaja">
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Fecha</th>
          <td align="left"><input name="fecha" type="text" class="cap toDate alignCenter" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Gasto</th>
          <td align="left"><select name="cod_gastos" id="cod_gastos">
            <!-- START BLOCK : cod -->
			<option value="{id}">{desc}</option>
			<!-- END BLOCK : cod -->
          </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Balance</th>
          <td align="left"><select name="clave_balance" id="clave_balance" style="color:#00C;">
            <option value="TRUE" selected="selected" style="color:#00C;">SI</option>
            <option value="FALSE" style="color:#C00;">NO</option>
          </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Tipo</th>
          <td align="left"><select name="tipo_mov" id="tipo_mov" style="color:#C00;">
            <option value="FALSE" selected="selected" style="color:#C00;">EGRESOS</option>
            <option value="TRUE" style="color:#00C;">INGRESOS</option>
          </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Importe</th>
          <td align="left"><input name="importe_total" type="text" class="cap numPosFormat2 alignRight" id="importe_total" size="12" /></td>
        </tr>
      </table>
      <p>
        <input name="generar" type="button" class="boton" id="generar" value="Generar Gastos" />
      </p>
      <div id="Resultado"></div>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;

window.addEvent('domready', function() {
	f = new Formulario('DispersionGastosCaja');
	
	f.form.fecha.addEvents({
		keydown: function(e) {
			if (e.key == 'enter')
				f.form.importe_total.select();
		},
		change: buscarEfectivos
	});
	
	f.form.clave_balance.addEvent('change', function() {
		if (this.value == 'FALSE')
			this.setStyle('color', '#C00');
		else
			this.setStyle('color', '#00C');
	});
	
	f.form.tipo_mov.addEvent('change', function() {
		if (this.value == 'FALSE')
			this.setStyle('color', '#C00');
		else
			this.setStyle('color', '#00C');
	});
	
	f.form.importe_total.addEvents({
		keydown: function(e) {
			if (e.key == 'enter')
				f.form.fecha.select();
		},
		change: buscarEfectivos
	});
	
	f.form.generar.addEvent('click', function() {
		if (confirm('¿Desea generar los gastos?'))
			f.form.submit();
		else
			f.form.fecha.select();
	});
	
	f.form.generar.set('disabled', true);
	f.form.fecha.select();
	
	new Request({
		url: 'DispersionGastosCaja.php',
		method: 'get',
		data: {
			list: 1
		},
		onSuccess: function(result)
		{
			if (result != '') {
				var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
				win.document.writeln(result);
				win.focus();
			}
		}
	}).send();
});

var buscarEfectivos = function() {
	if (f.form.fecha.value.length < 8 || f.form.importe_total.value.getVal() == 0) {
		$('Resultado').set('html', '');
		f.form.generar.set('disabled', true);
		return false;
	}
	
	new Request({
		url: 'DispersionGastosCaja.php',
		method: 'get',
		data: {
			f: f.form.fecha.value,
			i: f.form.importe_total.value.getVal()
		},
		onSuccess: function(result)
		{
			if (result.getVal() == -1) {
				alert('No hay resultados para el periodo dado');
				f.form.generar.set('disabled', true);
			}
			else {
				$('Resultado').set('html', result);
				f.form.generar.set('disabled', false);
			}
		}
	}).send();
}
-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
