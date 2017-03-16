<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Cheques</p>
 {mensaje}
  <form action="./ban_che_imp_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Ultimo Folio </th>
      <td class="vtabla"><input name="ultimo_folio" type="text" class="insert" id="ultimo_folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" size="8" maxlength="8">
        <input name="orden" type="radio" value="asc" checked>
        Ascendente
        <input name="orden" type="radio" value="desc">
        Descendente</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Solo P&oacute;lizas </th>
      <td class="vtabla"><input name="poliza" type="checkbox" id="poliza" value="1">
        Si</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente >>" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function validar() {
		if (!form.poliza.checked && form.ultimo_folio.value <= 0) {
			alert("Debe especificar el último folio");
			form.ultimo_folio.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = form.ultimo_folio.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : error_folio -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">El &uacute;ltimo folio capturado no es el consecutivo del &uacute;ltimo cheque generado</p>
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_imp_v2.php'">
</p></td>
</tr>
</table>
<!-- END BLOCK : error_folio -->
<!-- START BLOCK : polizas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Existen pagos por transferencia electr&oacute;nica pendientes. &iquest;Desea imprimir las p&oacute;lizas? </p>
  <p>
    <input type="button" class="boton" value="No" onClick="document.location='./ban_che_imp_v2.php?no_pol=1'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Si" onClick="document.location='./ban_che_imp_v2.php?pol=1'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : polizas -->
<!-- START BLOCK : alert -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-size: 12pt; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">Por favor, inserte las polizas en la impresora (cambiar toner normal). Luego presione el bot&oacute;n Terminar.</p>
  <p>
    <input type="button" class="boton" value="Terminar" onClick="document.location='./ban_che_imp_v2.php'"> 
    </p></td>
</tr>
</table>
<!-- END BLOCK : alert -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Cheques</p>
  <form action="./ban_che_imp_v2.php" method="post" name="form">
  <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
  <input name="ultimo_folio" type="hidden" value="{ultimo_folio}">
  <input name="orden" type="hidden" value="{orden}">
  <table class="tabla">
    <!-- START BLOCK : proveedor -->
	<tr>
      <th colspan="8" class="vtabla" scope="col">{num_proveedor} &#8212; {nombre_proveedor} </th>
      </tr>
    <tr>
      <th class="tabla"><input type="checkbox" onClick="checkBlock(this,{ini},{fin})" checked></th>
      <th class="tabla">Cia.</th>
      <th class="tabla">Nombre</th>
      <th class="tabla">Cuenta</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Folio</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" onClick="contador()" checked></td>
      <td class="rtabla">{num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla"><strong>{cuenta}</strong></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{folio}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><strong>{importe}</strong></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="7" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
    </tr>
    <tr>
      <td colspan="8">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : proveedor -->
	 <tr>
      <th colspan="7" class="rtabla">N&uacute;mero de cheques a imprimir</th>
      <th class="tabla"><input name="num_cheques" type="text" disabled="true" class="nombre" id="num_cheques" value="{num_cheques}" size="4" maxlength="4"></th>
	 </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_imp_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente >>" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	var form = document.form;
	
	function contador() {
		var count = 0;
		
		if (form.id.length == undefined) {
			count += form.id.checked ? 1 : 0;
		}
		else {
			for (i = 0; i < form.id.length; i++) {
				count += form.id[i].checked ? 1 : 0;
			}
		}
		
		form.num_cheques.value = count;
	}
	
	function checkBlock(checkblock, ini, fin) {
		if (form.id.length == undefined) {
			form.id.checked = checkblock.checked == true ? true : false;
		}
		else {
			for (i = ini; i <= fin; i++) {
				form.id[i].checked = checkblock.checked == true ? true : false;
			}
		}
		
		contador();
	}
	
	function validar() {
		if (form.id.length == undefined) {
			if (form.id.checked == false) {
				alert("Debe seleccionar al menos un cheque a imprimir");
				return false;
			}
			else if (confirm("¿Desea generar los cheques seleccionados?")) {
				form.submit();
			}
		}
		else {
			var count = 0;
			
			for (i = 0; i < form.id.length; i++) {
				count += form.id[i].checked == true ? 1 : 0;
			}
			
			if (count == 0) {
				alert("Debe seleccionar al menos un cheque a imprimir");
				return false;
			}
			else if (confirm("¿Desea generar los cheques seleccionados?")) {
				form.submit();
			}
		}
	}
</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : num_cheque -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p>{mensaje}</p>
  <form action="./ban_che_imp_v2.php" method="post" name="form">
  <input name="cuenta" type="hidden" value="{cuenta}">
  <input name="imp" type="hidden" value="1">
  <input name="ultimo_folio" type="hidden" value="{ultimo_folio}">
  <input name="orden" type="hidden" value="{orden}">
  <!-- START BLOCK : id -->
  <input name="id[]" id="cheque" type="hidden" value="{id}">
  <!-- END BLOCK : id -->
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./ban_che_imp_v2.php?ultimo_folio={ultimo_folio}&orden={orden}&cuenta={cuenta}'">
    &nbsp;&nbsp;
<input type="button" class="boton" value="Imprimir" onClick="imprimir(this.form)"> 
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function imprimir(form) {
		form.submit();
	}
</script>
<!-- END BLOCK : num_cheque -->
<!-- START BLOCK : no_result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">No hay cheques por imprimir </p>
  <p><input name="button" type="button" class="boton" onClick="document.location='./ban_che_imp_v2.php'" value="<< Regresar"></p></td>
</tr>
</table>
<!-- END BLOCK : no_result -->
</body>
</html>
