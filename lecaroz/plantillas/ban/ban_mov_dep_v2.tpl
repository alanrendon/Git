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
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Manual de Dep&oacute;sitos </p>
  <form action="./ban_mov_dep_v2.php" method="post" name="form">
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}"> 
    <input name="num_mov_ban" type="hidden" id="num_mov_ban" value="{num_mov_ban}">
	<table class="tabla">
      <tr>
        <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      </tr>
      <tr>
        <th class="tabla" style="font-size:12pt; ">{num_cia} - {nombre} </th>
      </tr>
    </table>   
    <br>   
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Inv</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : movban -->
	<tr>
      <td class="tabla"><input name="idban{i}" type="checkbox" id="idban{i}" onClick="totalBan()" value="{id}" checked></td>
      <td class="tabla"><input name="inv{i}" type="checkbox" id="inv{i}" value="{id}" onClick="inverso(this,'{num_doc}')"></td>
      <td class="tabla"><input name="num_doc{i}" type="hidden" id="num_doc{i}" value="{num_doc}"><input name="fecha{i}" type="hidden" id="fecha{i}" value="{fecha}">{fecha}</td>
      <td class="tabla"><input name="cod_banco{i}" type="hidden" id="cod_banco{i}" value="{cod_banco}">{cod_banco}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><input name="importe_ban{i}" type="text" class="rnombre" id="importe_ban{i}" value="{importe}" size="10" readonly="true"></td>
      <td class="tabla"><input type="button" class="boton" value="Mod" onClick="mod({id}, cuenta.value)"></td>
    </tr>
	<!-- END BLOCK : movban -->
    <tr>
      <th colspan="5" class="rtabla">Total</th>
      <th class="tabla"><input name="total_ban" type="text" disabled="true" class="rnombre" id="total_ban" value="{total_ban}" size="10"></th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>  
  <!-- START BLOCK : movs -->
  <p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold;">Movimientos Equivalentes</p>
  <input name="num_mov_lib" type="hidden" value="{num_mov_lib}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col"><input type="checkbox" onClick="checkall(this)"></th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : movlib -->
	<tr>
      <td class="tabla"><input name="idlib{i}" type="checkbox" id="idlib{i}" onClick="totalLib()" value="{id}"></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{cod_mov} {descripcion} </td>
      <td class="tabla">{concepto}</td>
      <td class="rtabla"><input name="importe_lib{i}" type="text" class="rnombre" id="importe_lib{i}" value="{importe}" size="10" readonly="true"></td>
      <td class="tabla"><input name="Button" type="button" class="boton" value="Div" onClick="div({id})"></td>
    </tr>
	<!-- END BLOCK : movlib -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="tabla"><input name="total_lib" type="text" disabled="true" class="rnombre" id="total_lib" value="0.00" size="10"></th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table>
  <!-- END BLOCK : movs -->
  <!-- START BLOCK : no_movs -->
  <p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #CC0000;">No hay movimientos equivalentes</p>
  <!-- END BLOCK : no_movs -->
  <p>
    <input type="button" class="boton" value="Cerrar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar" onClick="validar()"> 
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function mod(id, cuenta) {
	var win = window.open("./ban_dep_mov_minimod_v2.php?id=" + id + "&cuenta=" + cuenta, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=900,height=400");
}

function div(id) {
	var win = window.open("./ban_dep_div_v2.php?id=" + id, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=800,height=600");
}

function inverso(check, num_doc) {
	if (num_doc == "") {
		alert("No se puede hacer un proceso inverso del movimiento porque no tiene un número de folio o referencia");
		check.checked = false;
	}
}

function totalBan() {
	var tmp = 0;
	
	for (i = 0; i < parseInt(form.num_mov_ban.value); i++) {
		tmp += eval("form.idban" + i).checked ? parseFloat(eval("form.importe_ban" + i).value.replace(/\,/g, '')) : 0;
	}
	tmp = new oNumero(tmp);
	form.total_ban.value = tmp.formato(2, true);
}

function totalLib() {
	var tmp = 0;
	
	for (i = 0; i < parseInt(form.num_mov_lib.value); i++) {
		tmp += eval("form.idlib" + i).checked ? parseFloat(eval("form.importe_lib" + i).value.replace(/\,/g, '')) : 0;
	}
	tmp = new oNumero(tmp);
	form.total_lib.value = tmp.formato(2, true);
}

function checkall(check) {
	for (i = 0; i < parseInt(form.num_mov_lib.value); i++) {
		eval("form.idlib" + i).checked = check.checked;
	}
	
	totalLib();
}

function validar() {
	if (form.idlib0) {
		if (parseFloat(form.total_lib.value.replace(/\,/g, '')) > 0) {
			if (parseFloat(form.total_ban.value.replace(/\,/g, '')) != parseFloat(form.total_lib.value.replace(/\,/g, ''))) {
				alert("Los importes totales no coinciden");
				return false;
			}
			else if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
				form.submit();
			}
			else {
				return false;
			}
		}
		else {
			if (parseFloat(form.total_ban.value.replace(/\,/g, '')) <= 0) {
				alert("Debe seleccionar al menos un movimiento");
				return false;
			}
			else if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
				form.submit();
			}
			else {
				return false;
			}
		}
	}
	else {
		if (parseFloat(form.total_ban.value.replace(/\,/g, '')) <= 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		else if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
			form.submit();
		}
		else {
			return false;
		}
	}
}
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	var doc = window.opener.document;
	
	doc.location = doc.location + "#{num_cia}";
	doc.location.reload();
	self.close();
}

window.onload = cerrar();
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
