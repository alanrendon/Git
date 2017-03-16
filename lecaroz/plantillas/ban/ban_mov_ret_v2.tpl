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
<td align="center" valign="middle"><p class="title">Conciliaci&oacute;n Manual de Retiros </p>
  <form action="./ban_mov_ret_v2.php" method="post" name="form">
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="num_ban" type="hidden" id="num_ban" value="{num_ban}">
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
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
	<!-- START BLOCK : movban -->
	<tr>
	  <td class="tabla"><input name="idban{i}" type="checkbox" id="idban{i}" value="{id}" onClick="totalBan()" checked></td>
	  <td class="tabla"><input name="inv{i}" type="checkbox" id="inv{i}" value="1" {disabled}></td>
      <td class="tabla"><input name="fecha{i}" type="text" class="nombre" id="fecha{i}" value="{fecha}" size="10"></td>
      <td class="tabla"><input name="cod_banco{i}" type="hidden" id="cod_banco{i}" value="{cod_banco}">
        {cod_banco}</td>
      <td class="tabla"><input name="folio{i}" type="text" class="nombre" id="folio{i}" value="{folio}" size="8"></td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla"><input name="importe_ban{i}" type="text" class="rnombre" id="importe_ban{i}" value="{importe_ban}" size="10" readonly="true"></td>
      <td class="rtabla"><input type="button" class="boton" value="Mod" onClick="mod({id}, cuenta.value)"></td>
	</tr>
	<!-- END BLOCK : movban -->
	<tr>
	  <th colspan="6" class="rtabla">Total</th>
	  <th class="rtabla"><input name="total_ban" type="text" disabled class="rnombre" id="total_ban" value="{total_ban}" size="10"></th>
	  <th class="rtabla">&nbsp;</th>
	  </tr>
  </table>  
  <!-- START BLOCK : movs -->
  <p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold;">Movimientos Equivalentes</p>
  <input name="num_lib" type="hidden" id="num_lib" value="{num_lib}">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : movlib -->
	<tr>
      <td class="tabla"><input name="idlib{i}" id="idlib{i}" type="checkbox" value="{id}" onClick="totalLib()"></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{cod_mov} {descripcion} </td>
      <td class="tabla"><strong>{folio}</strong></td>
      <td class="tabla">{concepto}</td>
      <td class="rtabla"><input name="importe_lib{i}" type="text" class="rnombre" id="importe_lib{i}" value="{importe_lib}" size="10" readonly="true"></td>
      </tr>
	<!-- END BLOCK : movlib -->
	<tr>
	  <th colspan="5" class="rtabla">Total</th>
	  <th class="rtabla"><input name="total_lib" type="text" disabled class="rnombre" id="total_lib" value="0.00" size="10"></th>
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
var f = document.form;

function mod(id, cuenta) {
	var win = window.open("./ban_ret_mov_minimod_v2.php?id=" + id + "&cuenta=" + cuenta, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=900,height=300");
}

function totalBan() {
	var total = 0;
	for (var i = 0; i < get_val(f.num_ban); i++)
		total += eval('f.idban' + i + '.checked') ? get_val(eval('f.importe_ban' + i)) : 0;
	f.total_ban.value = numberFormat(total, 2);
}

function totalLib() {
	var total = 0;
	for (var i = 0; i < get_val(f.num_lib); i++)
		total += eval('f.idlib' + i + '.checked') ? get_val(eval('f.importe_lib' + i)) : 0;
	f.total_lib.value = numberFormat(total, 2);
}

function validar() {
	if (f.idlib0) {
		if (get_val(f.total_lib) > 0) {
			if (get_val(f.total_ban) != get_val(f.total_lib)) {
				alert("Los importes totales no coinciden");
				return false;
			}
			else if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
				f.submit();
			}
			else {
				return false;
			}
		}
		else {
			if (get_val(f.total_ban) <= 0) {
				alert("Debe seleccionar al menos un movimiento");
				return false;
			}
			else {
				form.submit();
			}
		}
		/*
		
		var count = 0;
		
		if (f.idlib.length == undefined) {
			if (f.idlib.checked == true)
				count++;
		}
		else {
			for (i=0; i<f.idlib.length; i++)
				if (f.idlib[i].checked == true)
					count++;
		}
		
		if (count == 0) {
			alert("Debe seleccionar alguno de los movimientos");
			return false;
		}
		else
			f.submit();*/
	}
	else {
		if (get_val(f.total_ban) <= 0) {
			alert("Debe seleccionar al menos un movimiento");
			return false;
		}
		else if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
			f.submit();
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
