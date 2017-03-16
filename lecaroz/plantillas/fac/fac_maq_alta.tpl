<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de M&aacute;quina 
  
</p>
  <form action="./fac_maq_alta.php" method="post" name="form" target="validar">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero</th>
      <td class="vtabla"><input name="num_maquina" type="text" class="insert" id="num_maquina" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) marca.select()" value="{num_maquina}" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Marca</th>
      <td class="vtabla"><input name="marca" type="text" class="vinsert" id="marca" onkeydown="if (event.keyCode == 13) descripcion.select()" value="{marca}" size="30" maxlength="100" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><textarea name="descripcion" cols="30" rows="3" class="insert" id="descripcion">{descripcion}</textarea></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Capacidad</th>
      <td class="vtabla"><input name="capacidad" type="text" class="insert" id="capacidad" onfocus="tmp.value=this.value;this.select()" onchange="isFloat(this,2,tmp)" onkeydown="if (event.keyCode == 13) num_serie.select()" value="{capacidad}" size="6" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">No. de Serie </th>
      <td class="vtabla"><input name="num_serie" type="text" class="vinsert" id="num_serie" onkeydown="if (event.keyCode == 13) fecha.select()" value="{num_serie}" size="30" maxlength="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha Compra </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onchange="actualiza_fecha(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_maquina.select()" value="{num_cia}" size="3" />
        <input name="nombre" type="text" class="vnombre" id="nombre" value="{nombre}" size="30" readonly="true" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Turno</th>
      <td class="vtabla"><select name="cod_turno" class="insert" id="cod_turno">
        <!-- START BLOCK : turno -->
		<option value="{cod}">{turno}</option>
		<!-- END BLOCK : turno -->
      </select></td>
    </tr>
  </table>  
    <p>
    <input name="" type="button" class="boton" onclick="validar()" value="Alta" />
  </p></form></td>
</tr>
</table>
<iframe name="validar" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia() {
	if (f.num_cia.value == "0" || f.num_cia.value == "") {
		f.num_cia.value = "";
		f.nombre.value = "";
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia.value = f.tmp.value;
	}
}

function validar() {
	if (get_val(f.num_maquina) == 0) {
		alert("Debe especificar el número para la máquina");
		f.num_maquina.select();
		return false;
	}
	/*else if (f.marca.length < 1) {
		alert("Debe especificar la marca de la máquina");
		f.marca.select();
		return false;
	}*/
	else if (f.descripcion.length < 3) {
		alert("Debe escribir la descripción de la máquina");
		f.descripcion.select();
		return false;
	}
	else if (f.num_cia.value <= 0) {
		alert("Debe especificar la compañía a la que pertenece la máquina");
		f.num_cia.select();
		return false;
	}
	else if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.num_maquina.select();
}

window.onload = f.num_maquina.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : validar -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame.document.form;

function validar() {
	alert("El número de máquina ya se encuentra en el catálogo");
	f.num_maquina.select();
}

window.onload = validar();
//-->
</script>
<!-- END BLOCK : validar -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	top.mainFrame.location = "./fac_maq_alta.php";
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
