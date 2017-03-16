<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores</p>
<form name="form" method="get" action="./ban_pma_pro.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla"><input name="tipo" type="radio" onClick="form.num_cia.disabled = false" value="cia">
        Compa&ntilde;&iacute;a 
          <input name="num_cia" type="text" disabled="true" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onBlur="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) form.num_proveedor.select();
else if (event.keyCode == 37) form.fecha_corte.select();" size="3" maxlength="3">          <br>          <input name="tipo" type="radio" onClick="form.num_cia.disabled = true" value="todas" checked>
        Todas</th>
      <th class="vtabla"><input name="fac" type="radio" onClick="form.num_proveedor.disabled = false" value="proveedor">
        Por proveedor 
          <input name="num_proveedor" type="text" disabled="true" class="insert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40 || event.keyCode == 38) form.fecha_corte.select();" size="5" maxlength="5">
        <br>
        <input name="fac" type="radio" onClick="form.num_proveedor.disabled = true" value="antiguedad" checked>
        Por antig&uuml;edad </th>
      </tr>
    <tr>
      <th class="vtabla">Cuenta</th>
      <th class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER</option>
      </select></th>
    </tr>
    <tr>
      <th class="vtabla">Fecha de corte </th>
      <th class="vtabla"><input name="fecha_corte" type="text" class="insert" id="fecha_corte" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.fecha_cheque.select();
else if (event.keyCode == 38) form.num_proveedor.select();" value="{fecha_corte}" size="10" maxlength="10"></th>
    </tr>
    <tr>
      <th class="vtabla">Fecha de cheque</th>
      <th class="vtabla"><input name="fecha_cheque" type="text" class="insert" id="fecha_cheque" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_proveedor.select();
else if (event.keyCode == 38) form.fecha_corte.select();" value="{fecha_cheque}" size="10" maxlength="10"></th>
    </tr>
  </table>  
  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="valida_registro()">
  </p>
 </form>
 </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.fac[0].checked && document.form.num_proveedor.value <= 0) {
			alert("Debe especificar un proveedor");
			document.form.num_proveedor.select();
			return false;
		}
		else if (document.form.tipo[0].checked && document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else if (document.form.fecha_corte.value == "") {
			alert("Debe especificar la fecha de corte");
			document.form.fecha_corte.select();
			return false;
		}
		else if (document.form.fecha_cheque.value == "") {
			alert("Debe especificar la fecha del cheque");
			document.form.fecha_cheque.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.fecha_corte.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : facturas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores </p>  <table width="70%" class="tabla">
  <tr>
    <th class="tabla" scope="col">Proveedor</th>
  </tr>
  <tr>
    <th class="tabla"><font size="+1">{num_proveedor} - {nombre_proveedor}</font> </th>
  </tr>
</table>
  <br>
 <form name="form" method="post" action="./ban_pma_pro.php">
 <input name="accion" type="hidden">
 <input name="numfilas" type="hidden" value="{numfilas}">
 <input name="temp" type="hidden">
  <table class="tabla" width="70%">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Fecha de pago </th>
      <th class="tabla" scope="col">N&uacute;m. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="2" class="tabla">Cia.: {num_cia}</th>
      <th class="tabla">Cuenta: {cuenta} </th>
      <th colspan="2" class="tabla">{nombre_cia}</th>
      </tr>
    <!-- START BLOCK : fila_1 -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}" checked></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{num_fact}</td>
      <td class="vtabla">{concepto}</td>
      <th class="rtabla">{importe}</th>
      </tr>
	<!-- END BLOCK : fila_1 -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="rtabla"><font size="+1">{total}</font></th>
      </tr>
    <tr>
      <th colspan="5">&nbsp;</th>
      </tr>
	<!-- END BLOCK : cia -->
  </table>
  <br>
  <table>
    <tr>
  <td class="tabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" onFocus="form.temp.value=this.value" onChange="if (isInt(this,form.temp)) actualiza_proveedor(this,form.nombre_proveedor);" size="5" maxlength="5"> 
<input name="nombre_proveedor" type="text" disabled="true" class="vnombre" id="nombre_proveedor" size="50"></td>
<td><input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente >>" onClick="siguiente_pro()"></td>
</tr></table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="cancelar()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Terminar" onClick="terminar()">
</p>
 </form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function siguiente_pro() {
		if (confirm("Se generarán los cheques para el proveedor. ¿Está seguro de que desea continuar?")) {
			document.form.accion.value = "siguiente";
			document.form.submit();
		}
		else
			return false;
	}
	
	function terminar() {
		if (confirm("Se generarán los cheques para el proveedor y se terminará el proceso. ¿Está seguro de que desea continuar?")) {
			document.form.accion.value = "terminar";
			document.form.submit();
		}
		else
			return false;
	}
	
	function cancelar() {
		if (confirm("No se generaran los cheques para este proveedor y se terminará el proceso. ¿Está seguro de que desea continuar?")) {
			document.form.accion.value = "cancelar";
			document.form.submit();
		}
		else
			return false;
	}
	
	function actualiza_proveedor(num_proveedor, nombre) {
		pro = new Array();
		<!-- START BLOCK : nombre_proveedor_ini -->
		pro[{num_proveedor}] = '{nombre_proveedor}';
		<!-- END BLOCK : nombre_proveedor_ini -->
		
		if (parseInt(num_proveedor.value) > 0) {
			if (pro[parseInt(num_proveedor.value)] == null) {
				alert("Proveedor "+parseInt(num_proveedor.value)+" no tiene facturas pendientes por pagar");
				num_proveedor.value = "";
				nombre.value  = "";
				num_proveedor.focus();
				return false;
			}
			else {
				num_proveedor.value = parseFloat(num_proveedor.value);
				nombre.value  = pro[parseInt(num_proveedor.value)];
				return;
			}
		}
		else if (num_proveedor.value == "") {
			num_proveedor.value = "";
			nombre.value  = "";
			return false;
		}
	}
</script>
<!-- END BLOCK : facturas -->
<!-- START BLOCK : all_fac -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Pago Manual de Proveedores</p>
   <form method="post" name="form" action="./ban_pma_pro.php">
   <input name="accion" type="hidden">
 <input name="numfilas" type="hidden" value="{numfilas}">
  <!-- START BLOCK : cia_all -->
   <table width="70%" class="tabla">
    <tr>
      <th class="tabla" scope="col"><font size="+1">{num_cia} - {nombre_cia}</font> </th>
      <th class="tabla" scope="col"><font size="+1">Cuenta.: {cuenta}</font> </th>
    </tr>
  </table>  
  <br>
  <table width="70%" class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Fecha de Pago </th>
      <th class="tabla" scope="col">N&uacute;m. Factura </th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : bloque_proveedor -->
	<tr>
      <th colspan="5" class="tabla">{num_proveedor} - {nombre_proveedor}</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}" checked></td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{num_fact}</td>
      <td class="vtabla">{concepto}</td>
      <th class="rtabla">{importe}</th>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th class="tabla">&nbsp;</th>
      <th class="tabla">&nbsp;</th>
      <th class="tabla">&nbsp;</th>
      <th class="rtabla">Total</th>
      <th class="rtabla"><font size="+1">{total}</font></th>
    </tr>
    <tr>
      <td colspan="5">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : bloque_proveedor -->
  </table>
  <br>
<hr>
  <!-- END BLOCK : cia_all -->
      <p>
    <input type="button" class="boton" value="Cancelar" onClick="cancelar()">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Terminar" onClick="terminar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function terminar() {
		if (confirm("Se generarán los cheques y se terminará el proceso. ¿Está seguro de que desea continuar?")) {
			document.form.accion.value = "terminar";
			document.form.submit();
		}
		else
			return false;
	}
	
	function cancelar() {
		if (confirm("No se generaran los cheques. ¿Está seguro de que desea continuar?")) {
			document.form.accion.value = "cancelar";
			document.form.submit();
		}
		else
			return false;
	}
</script>
<!-- END BLOCK : all_fac -->
</body>
</html>
