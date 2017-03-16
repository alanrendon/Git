<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">

<p class="title">Impresi&oacute;n de Recibos de Infonavit </p>
<form name="form" method="get" action="./fac_inf_rec.php">
<input name="temp" type="hidden">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row"><input name="tipo" type="radio" onClick="form.folio1.disabled = true;
form.folio2.disabled = true;
form.fecha_mov.disabled = false;
form.fecha_mov.select();" value="fecha" checked>
      Fecha de movimiento </th>
    <td class="vtabla"><input name="fecha_mov" type="text" class="insert" id="fecha_mov" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) form.enviar.focus();" value="{fecha}" size="10">
    <font size="-2">(ddmmaa)</font></td>
  </tr>
  <tr>
    <th class="vtabla" scope="row"><input name="tipo" type="radio" onClick="form.fecha_mov.disabled = true;
form.folio1.disabled = false;
form.folio2.disabled = false;
form.folio1.select();" value="folio">
      Folio</th>
    <td class="vtabla">de 
      <input name="folio1" type="text" disabled="true" class="insert" id="folio1" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.folio2.select();" size="5" maxlength="5"> 
      al 
      <input name="folio2" type="text" disabled="true" class="insert" id="folio2" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39 || event.keyCode == 37) form.folio1.select();" size="5" maxlength="5"></td>
  </tr>
</table>
<p>
  <input name="enviar" type="button" class="boton" id="enviar" onClick="valida_registro()" value="Siguiente">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.tipo[0].checked) {
			if (document.form.fecha_mov.value == "") {
				alert("Debe especificar la fecha");
				document.form.fecha_mov.select();
				return false;
			}
			else
				document.form.submit();
		}
		else if (document.form.tipo[1].checked) {
			if (document.form.folio1.value <= 0 || document.form.folio2.value <= 0) {
				alert("Debe especificar el folio inicial y el folio final");
				document.form.folio1.select();
				return false;
			}
			else
				document.form.submit();
		}
	}
	
	window.onload = document.form.fecha_mov.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : recibos -->
<!-- START BLOCK : recibo -->
<style type="text/css">
	td.folio {
		border: 3px solid;
	}
	td.firma {
		border-bottom: 1px solid;
	}
</style>
<table width="100%" height="49%">
  <tr>
    <td width="15%" rowspan="4" align="left" valign="top"><img src="./imagenes/escudo_lecaroz.jpg" width="83" height="132"></td>
    <td width="70" colspan="2" align="center" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif" size="+2"><strong>{cia}</strong></font></td>
    <td align="right" valign="top" width="15%"><table width="100%"><tr><td><font face="Geneva, Arial, Helvetica, sans-serif"><strong>Folio&nbsp;&nbsp;&nbsp;</strong></font></td><td class="folio" width="70%" align="center"><font face="Geneva, Arial, Helvetica, sans-serif"><strong>{folio}</strong></font></td></tr></table>
  </tr>
  <tr>
    <td colspan="2" align="right" valign="top"><strong><font face="Geneva, Arial, Helvetica, sans-serif">M&eacute;xico D.F., a {dia_actual} de {mes_actual} de {anio_actual}</font> </strong></td>
    <td rowspan="3" align="right" valign="top">  
  </tr>
  <tr>
    <td colspan="2" align="justify" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="justify" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif">Recib&iacute; de <strong>{nombre}</strong> la cantidad de <strong>{importe} PESOS ({importe_escrito} PESOS {centavos}/100 M.N.)</strong> correspondiente al pago del mes de <strong>{mes}</strong> por el concepto de pago de <strong>INFONAVIT</strong>. </font></td>
  </tr>
  <tr>
    <td widthalign="left" valign="top" height="40">&nbsp;</td>
    <td align="center" valign="top" width="40%">&nbsp;</td>
    <td align="center" valign="top" width="30%" class="firma">&nbsp;</td>
    <td align="right" valign="top">  
  </tr>
  <tr>
    <td widthalign="left" valign="top">&nbsp;</td>
    <td align="center" valign="top">&nbsp;</td>
    <td align="center" valign="top"><font face="Geneva, Arial, Helvetica, sans-serif"><strong>{cia}</strong></font></td>
    <td align="right" valign="top">  
  </tr>
</table>
{br}
<!-- END BLOCK : recibo -->
<script language="javascript" type="text/javascript">
	function imprimir() {
		window.print();
		//document.location = "./fac_inf_rec.php";
	}
	
	//window.onload = /*imprimir()*/document.location = "./fac_inf_rec.php";
	//window.onunload = window.print();
</script>
<!-- END BLOCK : recibos -->
</body>
</html>
