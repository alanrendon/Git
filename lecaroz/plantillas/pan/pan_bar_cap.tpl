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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Registro de Venta de   Barredura</p>
  <form action="./pan_bar_cap.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
  <tr>
  <th class="vtabla">Compa&ntilde;&iacute;a</th>
  <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_compania(this,nombre_cia)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) fecha.select();" size="3" maxlength="3">
    <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="40" maxlength="40"></td>
  <th class="vtabla">Fecha</th>
  <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) cantidad11.select();
else if (event.keyCode == 37) num_cia.select();" size="10" maxlength="10"></td>
  </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <td>&nbsp;</td>
      <th class="tabla">Azul</th>
      <th class="tabla">Rosa</th>
      <th class="tabla">Amarillo</th>
      <th class="tabla">Verde</th>
      <th class="tabla">Blanco</th>
    </tr>
    <tr>
      <th class="vtabla">Barredura</th>
      <td class="tabla"><input name="cantidad11" type="text" class="rinsert" id="cantidad11" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad12.select();
else if (event.keyCode == 37) cantidad51.select();
else if (event.keyCode == 38) no_comprobante1.select();
else if (event.keyCode == 39) cantidad21.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad21" type="text" class="rinsert" id="cantidad21" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad22.select();
else if (event.keyCode == 37) cantidad11.select();
else if (event.keyCode == 38) no_comprobante2.select();
else if (event.keyCode == 39) cantidad31.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad31" type="text" class="rinsert" id="cantidad31" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad32.select();
else if (event.keyCode == 37) cantidad21.select();
else if (event.keyCode == 38) no_comprobante3.select();
else if (event.keyCode == 39) cantidad41.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad41" type="text" class="rinsert" id="cantidad41" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad42.select();
else if (event.keyCode == 37) cantidad31.select();
else if (event.keyCode == 38) no_comprobante4.select();
else if (event.keyCode == 39) cantidad51.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad51" type="text" class="rinsert" id="cantidad51" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad52.select();
else if (event.keyCode == 37) cantidad41.select();
else if (event.keyCode == 38) no_comprobante5.select();
else if (event.keyCode == 39) cantidad11.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Costales</th>
      <td class="tabla"><input name="cantidad12" type="text" class="rinsert" id="cantidad12" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad13.select();
else if (event.keyCode == 37) cantidad52.select();
else if (event.keyCode == 38) cantidad11.select();
else if (event.keyCode == 39) cantidad22.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad22" type="text" class="rinsert" id="cantidad22" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad23.select();
else if (event.keyCode == 37) cantidad12.select();
else if (event.keyCode == 38) cantidad21.select();
else if (event.keyCode == 39) cantidad32.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad32" type="text" class="rinsert" id="cantidad32" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad33.select();
else if (event.keyCode == 37) cantidad22.select();
else if (event.keyCode == 38) cantidad31.select();
else if (event.keyCode == 39) cantidad42.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad42" type="text" class="rinsert" id="cantidad42" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad43.select();
else if (event.keyCode == 37) cantidad32.select();
else if (event.keyCode == 38) cantidad41.select();
else if (event.keyCode == 39) cantidad52.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad52" type="text" class="rinsert" id="cantidad52" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad53.select();
else if (event.keyCode == 37) cantidad42.select();
else if (event.keyCode == 38) cantidad51.select();
else if (event.keyCode == 39) cantidad12.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Botes</th>
      <td class="tabla"><input name="cantidad13" type="text" class="rinsert" id="cantidad13" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad14.select();
else if (event.keyCode == 37) cantidad53.select();
else if (event.keyCode == 38) cantidad12.select();
else if (event.keyCode == 39) cantidad23.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad23" type="text" class="rinsert" id="cantidad23" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad24.select();
else if (event.keyCode == 37) cantidad13.select();
else if (event.keyCode == 38) cantidad22.select();
else if (event.keyCode == 39) cantidad33.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad33" type="text" class="rinsert" id="cantidad33" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad34.select();
else if (event.keyCode == 37) cantidad23.select();
else if (event.keyCode == 38) cantidad32.select();
else if (event.keyCode == 39) cantidad43.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad43" type="text" class="rinsert" id="cantidad43" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad44.select();
else if (event.keyCode == 37) cantidad33.select();
else if (event.keyCode == 38) cantidad42.select();
else if (event.keyCode == 39) cantidad53.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad53" type="text" class="rinsert" id="cantidad53" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad54.select();
else if (event.keyCode == 37) cantidad43.select();
else if (event.keyCode == 38) cantidad52.select();
else if (event.keyCode == 39) cantidad13.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Cubetas</th>
      <td class="tabla"><input name="cantidad14" type="text" class="rinsert" id="cantidad14" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad15.select();
else if (event.keyCode == 37) cantidad54.select();
else if (event.keyCode == 38) cantidad13.select();
else if (event.keyCode == 39) cantidad24.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad24" type="text" class="rinsert" id="cantidad24" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad25.select();
else if (event.keyCode == 37) cantidad14.select();
else if (event.keyCode == 38) cantidad23.select();
else if (event.keyCode == 39) cantidad34.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad34" type="text" class="rinsert" id="cantidad34" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad35.select();
else if (event.keyCode == 37) cantidad24.select();
else if (event.keyCode == 38) cantidad33.select();
else if (event.keyCode == 39) cantidad44.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad44" type="text" class="rinsert" id="cantidad44" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad45.select();
else if (event.keyCode == 37) cantidad34.select();
else if (event.keyCode == 38) cantidad43.select();
else if (event.keyCode == 39) cantidad54.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad54" type="text" class="rinsert" id="cantidad54" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad55.select();
else if (event.keyCode == 37) cantidad44.select();
else if (event.keyCode == 38) cantidad53.select();
else if (event.keyCode == 39) cantidad14.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">Otros</th>
      <td class="tabla"><input name="cantidad15" type="text" class="rinsert" id="cantidad15" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante1.select();
else if (event.keyCode == 37) cantidad55.select();
else if (event.keyCode == 38) cantidad14.select();
else if (event.keyCode == 39) cantidad25.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad25" type="text" class="rinsert" id="cantidad25" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante2.select();
else if (event.keyCode == 37) cantidad15.select();
else if (event.keyCode == 38) cantidad24.select();
else if (event.keyCode == 39) cantidad35.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad35" type="text" class="rinsert" id="cantidad35" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante3.select();
else if (event.keyCode == 37) cantidad25.select();
else if (event.keyCode == 38) cantidad34.select();
else if (event.keyCode == 39) cantidad45.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad45" type="text" class="rinsert" id="cantidad45" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante4.select();
else if (event.keyCode == 37) cantidad35.select();
else if (event.keyCode == 38) cantidad44.select();
else if (event.keyCode == 39) cantidad55.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="cantidad55" type="text" class="rinsert" id="cantidad55" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) no_comprobante5.select();
else if (event.keyCode == 37) cantidad45.select();
else if (event.keyCode == 38) cantidad54.select();
else if (event.keyCode == 39) cantidad15.select();" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla">No. de comprobante </th>
      <td class="tabla"><input name="no_comprobante1" type="text" class="rinsert" id="no_comprobante1" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad11.select();
else if (event.keyCode == 37) no_comprobante5.select();
else if (event.keyCode == 38) cantidad15.select();
else if (event.keyCode == 39) no_comprobante2.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="no_comprobante2" type="text" class="rinsert" id="no_comprobante2" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad21.select();
else if (event.keyCode == 37) no_comprobante1.select();
else if (event.keyCode == 38) cantidad25.select();
else if (event.keyCode == 39) no_comprobante3.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="no_comprobante3" type="text" class="rinsert" id="no_comprobante3" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad31.select();
else if (event.keyCode == 37) no_comprobante2.select();
else if (event.keyCode == 38) cantidad35.select();
else if (event.keyCode == 39) no_comprobante4.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="no_comprobante4" type="text" class="rinsert" id="no_comprobante4" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad41.select();
else if (event.keyCode == 37) no_comprobante3.select();
else if (event.keyCode == 38) cantidad45.select();
else if (event.keyCode == 39) no_comprobante5.select();" size="10" maxlength="10"></td>
      <td class="tabla"><input name="no_comprobante5" type="text" class="rinsert" id="no_comprobante5" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) cantidad51.select();
else if (event.keyCode == 37) no_comprobante4.select();
else if (event.keyCode == 38) cantidad55.select();
else if (event.keyCode == 39) no_comprobante1.select();" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Capturar" onClick="valida_registro(form)">
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_compania(num_cia, nombre) {
		cia = new Array();// Materias primas
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
				
		if (num_cia.value > 0) {
			if (cia[num_cia.value] == null) {
				alert("Compañía "+num_cia.value+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
			}
			else {
				nombre.value   = cia[num_cia.value];
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
		}
	}
	
	function valida_registro(form) {
		if (form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			form.num_cia.select();
			return false;
		}
		else if (form.fecha.value == "") {
			alert("Debe especificar la fecha");
			form.fecha.select();
			return false;
		}
		else
			if (confirm("¿Son correctos los datos?"))
				form.submit();
			else
				form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script> 
<!-- END BLOCK : captura -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas MOLLENDO S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comprobantes de Barredura <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table class="print">
    <tr>
      <th class="print" scope="col">Comprobante</th>
      <th class="print" scope="col">Importe</th>
      <th colspan="2" class="print" scope="col">N&uacute;mero y Nombre de la Compa&ntilde;&iacute;a </th>
      </tr>
    <!-- START BLOCK : color -->
	<tr>
      <td colspan="4" class="print">Comprador: {color} </td>
      </tr>
    <!-- START BLOCK : comprador -->
	<tr>
      <td class="print">{comprobante}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{num_cia}</td>
      <td class="vprint">{nombre_cia}</td>
    </tr>
	<!-- END BLOCK : comprador -->
    <tr>
      <th class="print">&nbsp;</th>
      <th class="rprint_total">{total}</th>
      <th colspan="2" class="print">&nbsp;</th>
      </tr>
	  <!-- END BLOCK : color -->
    <tr>
      <td colspan="4">&nbsp;</td>
      </tr>
  </table>
</td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
