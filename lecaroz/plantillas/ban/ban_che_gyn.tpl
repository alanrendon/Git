<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Gastos y N&oacute;minas</p>
  <form action="ban_che_gyn.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <!-- <option value="0" selected="selected">AMBOS BANCOS</option> -->
        <option value="1">BANORTE</option>
        <option value="2">SANTANDER</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)fecha2.select()" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)fecha1.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Incluir</th>
      <td class="vtabla"><input name="opt" type="checkbox" id="opt" value="1" checked="checked" />
        Cancelados</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.fecha1.value.length < 8) {
		alert('Debe especificar el periodo de consulta');
		f.fecha1.select();
	}
	else if (f.fecha2.value.length < 8) {ç
		alert('Debe especificar el periodo de consulta');
		f.fecha2.select();
	}
	else
		f.submit();
}

window.onload = f.fecha1.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<style media="print">
.noDisplay {
	display: none;
}
</style>
<table width="100%">
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Cheques emitidos del {fecha1} al {fecha2} <br />
    Gastos y N&oacute;minas </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Importe</th>
    <th width="30" class="print" scope="col">&nbsp;</th>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Folio</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia0} {nombre0} </td>
    <td class="rprint">{folio0}</td>
    <td class="rprint">{importe0}</td>
    <td class="print">&nbsp;</td>
    <td class="vprint">{num_cia1} {nombre1} </td>
    <td class="rprint">{folio1}</td>
    <td class="rprint">{importe1}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="4" class="rprint">Total</th>
    <th colspan="3" class="print" style="font-size:12pt;">{total}</th>
  </tr>
  <tr>
    <th colspan="4" class="rprint">No. Cheques </th>
    <th colspan="3" class="print" style="font-size:12pt;">{cheques}</th>
  </tr>
</table>
<p class="noDisplay" align="center">
  <input type="button" class="boton" value="Regresar" onclick="document.location='ban_che_gyn.php'" />
&nbsp;&nbsp;
<input name="exportar" type="button" class="boton" id="exportar" value="Exportar" onclick="exportar('{f1}','{f2}',{c},{o})" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Generar Ingresos" onclick="generar('{f1}','{f2}',{c},{o})"{disabled} />
</p>
<script language="javascript" type="text/javascript">
<!--
function generar(f1, f2, c, o) {
	var url = 'ban_che_gyn.php';
	var opt = '?generar=1&f1=' + f1 + '&f2=' + f2 + '&c=' + c + '&o=' + o;

	if (confirm('¿Desea generar los ingresos extraordinarios?'))
		document.location = url + opt;
}

function exportar(f1, f2, c, o) {
	var url = 'ban_che_gyn.php';
	var opt = '?exportar=1&f1=' + f1 + '&f2=' + f2 + '&c=' + c + '&o=' + o;

	document.location = url + opt;
}
//-->
</script>
<!-- END BLOCK : listado -->
</body>
</html>
