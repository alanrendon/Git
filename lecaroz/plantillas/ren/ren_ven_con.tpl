<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Rentas por Fecha de Vencimiento</p>
  <form action="./ren_ven_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Arrendador</th>
      <td class="vtabla"><input name="arr" type="text" class="insert" id="arr" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) local.select()" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Local</th>
      <td class="vtabla"><input name="local" type="text" class="insert" id="local" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) arr.select()" value="{fecha2}" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.fecha1.value.length < 8) {
		alert("Debe especificar la fecha inicial");
		f.fecha1.select();
		return false;
	}
	else if (f.fecha2.value.length < 8) {
		alert("Debe especificar la fecha final");
		f.fecha2.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.arr.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Rentas por Fecha de Vencimiento </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Local</th>
    <th class="print" scope="col">Inmobiliaria</th>
    <th class="print" scope="col">Renta</th>
    <th class="print" scope="col">Fecha de Vencimiento </th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num} {nombre} </td>
    <td class="vprint">{arr}</td>
    <td class="rprint">{renta}</td>
    <td class="print">{fecha_ven}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
  </tr>
  <tr>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
    <td class="print">&nbsp;</td>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
