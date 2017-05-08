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
<td align="center" valign="middle"><p class="title">Modificar Gasto</p>
  <form action="./ros_gas_mod_v2.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <input name="id" type="hidden" id="id" value="{id}">    
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia} {nombre} </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto()" onKeyDown="if (event.keyCode == 13) fecha.select()" value="{codgastos}" size="3"{readonly}>
        <input name="desc" type="text" class="vnombre" id="desc" value="{desc}" size="30" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) codgastos.select()" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <!-- START BLOCK : bturno -->
	  <th class="vtabla" scope="row">Turno</th>
      <td class="vtabla"><select name="cod_turno" class="insert" id="cod_turno">
        <option value=""></option>
		<!-- START BLOCK : turno -->
        <option value="{cod}"{selected}>{cod} {nombre}</option>
		<!-- END BLOCK : turno -->
      </select></td>
	  <!-- END BLOCK : bturno -->
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla">{concepto}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla">{importe}</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()">
  </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
var f = document.form, gasto = new Array();
<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->

function cambiaGasto() {
	if (f.codgastos.value == "" || f.codgastos.value == "0") {
		f.codgastos.value = "";
		f.desc.value = "";
	}
	else if (gasto[get_val(f.codgastos)] != null)
		f.desc.value = gasto[get_val(f.codgastos)];
	else {
		alert("El código no esta en el catálogo");
		f.codgastos.value = f.tmp.value;
		f.codgastos.select();
	}
}

function validar() {
	if (get_val(f.codgastos) == 0) {
		alert("Debe especificar el código de gasto");
		f.codgastos.select();
		return false;
	}
	else if (f.fecha.value.length < 8) {
		alert("Debe especificar la fecha");
		f.fecha.select();
		return false;
	}
	else if (get_val(f.codgastos) == 23 && get_val(f.cod_turno) == 0) {
		alert("Debe especificar el turno para las mercancias");
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.codgastos.select();
}

window.onload = f.codgastos.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	f.eval(campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : valid -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	top.window.opener.document.location = top.window.opener.document.location + "#{codgastos}";
	top.window.opener.document.location.reload();
	top.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
