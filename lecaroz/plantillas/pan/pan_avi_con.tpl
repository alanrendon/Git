<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/styles/impresion.css" rel="stylesheet" type="text/css">
<link href="/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Avio</p>
  <form action="./pan_avi_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) fecha.select();" size="3" maxlength="3"></th>
    </tr>
    <tr>
      <th class="vtabla">Fecha</th>
      <th class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) num_cia.select();" value="{fecha}" size="10" maxlength="10"></th>
    </tr>
    <tr>
      <th colspan="2" class="vtabla"><input name="acumulado" type="checkbox" id="acumulado" value="TRUE">
        Acumulado</th>
      </tr>
    <tr>
      <th colspan="2" class="vtabla"><input name="tabla" type="radio" value="virtual" checked>
        Captura de entradas<br>
        <input name="tabla" type="radio" value="real">
        Facturas </th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Existencia y Consumo por Turnos<br>
      del d&iacute;a {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Materia Prima</th>
      <th class="print" scope="col">Existencia</th>
      <th class="print" scope="col">Entrada</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">FD</th>
      <th class="print" scope="col">FN</th>
      <th class="print" scope="col">BD</th>
      <th class="print" scope="col">REP</th>
      <th class="print" scope="col">PIC</th>
      <th class="print" scope="col">GEL</th>
      <th class="print" scope="col">DESP</th>
      <th class="print" scope="col">Consumo Total </th>
      <th class="print" scope="col">Para ma&ntilde;ana </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{codmp}</td>
      <td class="vprint">{nombre}</td>
      <td class="rprint">{existencia}</td>
      <td class="rprint">{entrada}</td>
      <td class="rprint">{total}</td>
      <td class="rprint">{fd}&nbsp;</td>
      <td class="rprint">{fn}&nbsp;</td>
      <td class="rprint">{bd}&nbsp;</td>
      <td class="rprint">{rep}&nbsp;</td>
      <td class="rprint">{pic}&nbsp;</td>
      <td class="rprint">{gel}&nbsp;</td>
      <td class="rprint">{desp}&nbsp;</td>
      <td class="rprint">{consumo}</td>
      <td class="rprint">{manana}</td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
<!-- START BLOCK : acumulado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia}</td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td class="print_encabezado" align="right">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Existencia, Entradas y Consumos Acumulados <br>
    al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Materia Prima</th>
      <th class="print" scope="col">Existencia Inicial </th>
      <th class="print" scope="col">Entradas</th>
      <th class="print" scope="col">Total</th>
      <th class="print" scope="col">FD</th>
      <th class="print" scope="col">FN</th>
      <th class="print" scope="col">BD</th>
      <th class="print" scope="col">REP</th>
      <th class="print" scope="col">PIC</th>
      <th class="print" scope="col">GEL</th>
      <th class="print" scope="col">DESP</th>
      <th class="print" scope="col">Consumo Total </th>
      <th class="print" scope="col">Existencia Final </th>
    </tr>
    <!-- START BLOCK : fila_acu -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vprint">{codmp}</td>
      <td class="vprint">{nombre}</td>
      <td class="rprint">{existencia}</td>
      <td class="rprint">{entrada}</td>
      <td class="rprint">{total}</td>
      <td class="rprint">{fd}&nbsp;</td>
      <td class="rprint">{fn}&nbsp;</td>
      <td class="rprint">{bd}&nbsp;</td>
      <td class="rprint">{rep}&nbsp;</td>
      <td class="rprint">{pic}&nbsp;</td>
      <td class="rprint">{gel}&nbsp;</td>
      <td class="rprint">{desp}&nbsp;</td>
      <td class="rprint">{consumo}</td>
      <td class="rprint">{final}</td>
    </tr>
	<!-- END BLOCK : fila_acu -->
</table>
<!-- END BLOCK : acumulado -->
</body>
</html>
