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
<td align="center" valign="middle">
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Banco</th>
    <th class="tabla" scope="col">Cuenta</th>
    </tr>
  <tr>
    <td class="tabla" scope="row"><font size="+2" color="#0066FF"><strong>{num_cia} - {nombre_cia} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{banco} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{clabe_cuenta}</strong></font></td>
    </tr>
</table>
<br>
<table width="30%" class="tabla">
  <tr>
    <th width="50%" class="tabla" scope="col">Saldo Inicial </th>
    <th width="50%" class="tabla" scope="col"><input name="saldo_ini" type="text" class="nombre" id="saldo_ini" style="width: 100%; font-size: 12pt;" value="{saldo_ini}" size="10" readonly="true"></th>
  </tr>
</table>
<br>
<form action="./ban_res_con_v2.php" method="post" name="form">
<input name="num_cia" type="hidden" id="num_cia" value="{num_cia_next}">
<input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
<input name="fecha" type="hidden" id="fecha" value="{fecha}">
<input name="accion" type="hidden" id="accion" value="{accion}">
<!-- START BLOCK : mov -->
<table width="20%" class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">{mov}</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td width="50%" class="tabla"><input name="id[]" type="hidden" id="id" value="{id}"><input name="fecha_con[]" type="text" class="insert" id="fecha_con" style="width: 100%;" onFocus="this.select()" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) {
if (fecha_con.length == undefined) this.blur();
else fecha_con[{next}].select();
}" value="{fecha_con}" size="10" maxlength="10"></td>
    <td width="50%" class="rtabla" style="font-weight: bold; color: #{color};">{importe}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th class="vtabla">Total</th>
    <th class="rtabla">{total}</th>
  </tr>
</table>
<br>
<!-- END BLOCK : mov -->
<table width="30%" class="tabla">
  <tr>
    <th width="50%" class="tabla" scope="col">Saldo Final</th>
    <th width="50%" class="tabla" scope="col"><input name="saldo_fin" type="text" class="nombre" id="saldo_fin" style="width: 100%; font-size: 12pt;" value="{saldo_fin}" size="10" readonly="true"></th>
  </tr>
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;  
<input type="button" class="boton" value="Conciliar" onClick="validar()">
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function validar() {
	var cont = 0;
	if (form.fecha_con.length == undefined) {
		if (form.fecha_con.value.length < 8) {
			alert("Debe capturar la fecha de conciliación");
			form.fecha_con.select();
			return false;
		}
	}
	else {
		for (i = 0; i < form.fecha_con.length; i++) {
			if (form.fecha_con[i].value.length < 8) {
				alert("Debe capturar la fecha de conciliación");
				form.fecha_con[i].select();
				return false;
			}
		}
	}
	
	if (confirm("¿Desea conciliar los movimientos seleccionados?")) {
		form.submit();
	}
	else {
		return false;
	}
}

window.onload = form.fecha_con.length == undefined ? form.fecha_con.select() : form.fecha_con[0].select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar(accion) {
	if (accion.value == "next") {
		window.opener.document.location = "./ban_con_man_v2.php?num_cia{cuenta}={num_cia}&cuenta={cuenta}&fecha={fecha}&accion=" + accion;
		self.close();
	}
	else {
		window.opener.document.location = "./ban_con_man_v2.php?num_cia{cuenta}={num_cia}&cuenta={cuenta}&fecha={fecha}&accion=" + accion;
		self.close();
	}
}

window.onload = cerrar("{accion}");
-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
