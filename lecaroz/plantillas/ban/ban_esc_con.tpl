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
<td align="center" valign="middle"><p class="title">Estados de Cuentas de Bancos </p>
<form name="form" method="get" action="./ban_esc_con.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="col"><input name="listado" type="radio" onClick="form.num_cia.disabled = false;" value="cia" checked>
      Compa&ntilde;&iacute;a 
      <input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.fecha1.select();" size="3" maxlength="3"></th>
    <th class="vtabla" scope="col">Fecha inicial (ddmmaa) </th>
    <th class="vtabla" scope="col"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha2.select();
else if (event.keyCode == 37) form.num_cia.select();" value="{fecha1}" size="10" maxlength="10"></th>
  </tr>
  <tr>
    <th class="vtabla">
      <input name="listado" type="radio" onClick="form.num_cia.disabled = true;" value="todas">
Todas las cias. </th>
    <th class="vtabla">Fecha de corte (ddmmaa) </th>
    <th class="vtabla"><input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) form.fecha1.select();
else if (event.keyCode == 37) form.num_cia.select();" value="{fecha2}" size="10" maxlength="10"></th>
  </tr>
  <tr>
    <th class="vtabla">Cuenta</th>
    <th colspan="2" class="vtabla">      <select name="cuenta" class="insert" id="cuenta">
      <option value="1" selected>BANORTE</option>
      <option value="2">SANTANDER SERFIN</option>
      </select></th>
    </tr>
  <tr>
    <td class="rtabla" valign="top">Mostrar:</td>
    <td colspan="2" class="vtabla"><input name="tipo" type="radio" value="todos" checked onClick="cod_mov.disabled=true">      
      Todos<br>
      <input name="tipo" type="radio" value="depositos" onClick="cod_mov.disabled=true"> 
      Dep&oacute;sitos<br>
      <input name="tipo" type="radio" value="retiros" onClick="cod_mov.disabled=true"> Retiros <br>
      <input name="tipo" type="radio" value="concepto" onClick="cod_mov.disabled=false">
      Concepto&nbsp;&nbsp;
      <select name="cod_concepto" disabled="disabled" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}">{cod_mov} - {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
    </tr>
</table>

  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.fecha1.value == "") {
			alert("Debe especificar la fecha inicial");
			document.form.fecha1.select();
			return false;
		}
		else if (document.form.fecha2.value == "") {
			alert("Debe especificar la fecha de corte");
			document.form.fecha2.select();
			return false;
		}
		else if (document.form.listado.value == "cia" && document.form.num_cia.value <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top"><table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Estados de Cuenta de Bancos<br>
      del {dia1} de {mes1} al {dia2} de {mes2} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <!-- START BLOCK : concepto -->
  <table class="print">
  <tr>
    <th class="print" scope="col"><font size="+1">{cod_mov} - {descripcion}</font></th>
  </tr>
</table>
<br>
  <!-- END BLOCK : concepto -->
  <!-- START BLOCK : cia -->
  <table width="100%" class="print">
    <tr>
      <th width="10%" class="print" scope="col"><font size="+1">Cia.: {num_cia}</font> </th>
      <th colspan="2" class="print" scope="col"><font size="+1">Cuenta.: {cuenta}</font> </th>
      <th colspan="4" class="print" scope="col"><font size="+1">{nombre_cia} ({nombre_corto})</font> </th>
      </tr>
    <!-- START BLOCK : saldo_anterior -->
	<tr>
      <th class="print" scope="col">Saldo Anterior Libros </th>
      <th colspan="2" class="print_total" scope="col">{saldo_anterior}</th>
      <th class="print" scope="col">Saldo Anterior Bancos </th>
      <th class="print_total" scope="col">{saldo_anterior_bancos}</th>
      <th colspan="2" class="print_total" scope="col">{banco}</th>
      </tr>
	  <!-- END BLOCK : saldo_anterior -->
    <tr>
      <th width="10%" class="print" scope="col">Fecha</th>
      <th width="10%" class="print" scope="col">Dep&oacute;sito</th>
      <th width="10%" class="print" scope="col">Retiro</th>
      <th width="10%" class="print" scope="col">Cheque</th>
      <th width="30%" class="print" scope="col">Beneficiario</th>
      <th width="20%" class="print" scope="col">Concepto</th>
      <th class="print" scope="col">Fecha conciliaci&oacute;n </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="10%" class="print">{fecha}</td>
      <td width="10%" class="rprint">{deposito}</td>
      <td width="10%" class="rprint">{retiro}</td>
      <td width="10%" class="print">{folio}</td>
      <td width="30%" class="vprint">{beneficiario}</td>
      <td width="20%" class="vprint">{concepto}</td>
      <td class="print">{fecha_con}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th width="10%" class="print">Total Cuenta </th>
      <th width="10%" class="rprint_total">{total_depositos}</th>
      <th width="10%" class="rprint_total">{total_retiros}</th>
      <th colspan="4" class="print">&nbsp;</th>
      </tr>
   <!-- START BLOCK : saldo_actual -->
    <tr>
      <th class="print">Saldo Actual Libros</th>
      <th colspan="2" class="print_total">{saldo_actual}</th>
      <th class="print">Saldo Actual Bancos </th>
      <th class="print_total">{saldo_actual_bancos}</th>
      <th class="print">Diferencia</th>
      <th class="print_total">{diferencia}</th>
    </tr>
	<!-- END BLOCK : saldo_actual -->
  </table>
  <br>
  <!-- END BLOCK : cia -->
  <!-- START BLOCK : regresar -->
  <input type="button" class="boton" onClick="imprimir()" value="Versión Imprimible">&nbsp;&nbsp;
  <input type="button" class="boton" onClick="history.back()" value="Regresar">
  <!-- END BLOCK : regresar -->
  <!-- START BLOCK : cerrar -->
  <input type="button" class="boton" onClick="self.close()" value="Cerrar">
  <!-- END BLOCK : cerrar -->
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.open(document.location + "&impresion=1","imp","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768");
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
