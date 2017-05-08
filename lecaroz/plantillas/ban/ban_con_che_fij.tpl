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
<td align="center" valign="middle"><p class="title">Consulta de Pagos Fijos</p>
  <form action="./ban_con_che_fij.php" method="get" name="form">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="3" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><select name="codgastos" class="insert" id="codgastos">
	    <option value="" selected></option>
        <!-- START BLOCK : gasto -->
		<option value="{codgastos}">{codgastos} {descripcion}</option>
		<!-- END BLOCK : gasto -->
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Pagos Fijos</p>
  <form action="" method="post" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="num_pro" type="hidden" id="num_pro" value="{num_pro}"> 
    <input name="codgastos" type="hidden" id="codgastos" value="{codgastos}">   
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col"><input type="checkbox" onClick="checkall(this)"></th>
      <th class="tabla" scope="col">Mod.</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Gasto</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">IVA</th>
      <th class="tabla" scope="col">Ret IVA </th>
      <th class="tabla" scope="col">ISR</th>
      <th class="tabla" scope="col">Total</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="tabla"><input type="button" class="boton" onClick="mod({id}, {i})" value="..."></td>
      <td class="tabla"><input name="num_cia[{i}]" type="text" disabled="true" class="vnombre" id="num_cia" value="{num_cia}" size="20"></td>
      <td class="tabla"><input name="num_pro[{i}]" type="text" disabled="true" class="vnombre" id="num_pro" value="{num_pro}" size="20"></td>
      <td class="tabla"><input name="codgastos[{i}]" type="text" disabled="true" class="vnombre" id="codgastos" value="{codgastos}" size="20"></td>
      <td class="tabla"><input name="concepto[{i}]" type="text" disabled="true" class="vnombre" id="concepto" value="{concepto}" size="20"></td>
      <td class="tabla"><input name="importe[{i}]" type="text" disabled="true" class="rnombre" id="importe" value="{importe}" size="10"></td>
      <td class="tabla"><input name="iva[{i}]" type="text" disabled="true" class="rnombre" id="iva" value="{iva}" size="8"></td>
      <td class="tabla"><input name="ret_iva[{i}]" type="text" disabled="true" class="rnombre" id="ret_iva" value="{ret_iva}" size="8"></td>
      <td class="tabla"><input name="isr[{i}]" type="text" disabled="true" class="rnombre" id="isr" value="{isr}" size="8"></td>
      <td class="tabla"><input name="total[{i}]" type="text" disabled="true" class="rnombre" id="total" value="{total}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input name="" type="button" class="boton" onClick="document.location='./ban_con_che_fij.php'" value="Regresar">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Borrar" onClick="borrar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function borrar() {
	var count = 0;
	
	if (form.id.length == undefined) {
		count += form.id.checked ? 1 : 0;
	}
	else {
		for (i = 0; i < form.id.length; i++) {
			count += form.id[i].checked ? 1 : 0;
		}
	}
	
	if (count > 0) {
		if (confirm("¿Desea borrar los registros seleccionados?")) {
			form.submit();
		}
		else {
			return false;
		}
	}
	else {
		alert("Debe seleccionar al menos un registro");
	}
}

function mod(id, i) {
	var ven =window.open("./ban_mod_che_fij.php?id=" + id + "&i=" + i,"","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=300"); 
}

function checkall(check) {
	if (form.id.length == undefined) {
		form.id.checked = check.checked;
	}
	else {
		for (i = 0; i < form.id.length; i++) {
			form.id[i].checked = check.checked;
		}
	}
}
-->
</script>
<!-- START BLOCK : listado -->
</body>
</html>
