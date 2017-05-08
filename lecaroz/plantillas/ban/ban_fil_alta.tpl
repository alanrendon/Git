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
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Alta de Filiales</p>
  <form action="./ban_fil_alta.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Filial</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia_primaria[]" type="text" class="insert" id="num_cia_primaria" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia_pri[{i}])" onKeyDown="if (event.keyCode == 13 || event.keyCode == 39) num_cia[{i}].select();
else if (event.keyCode == 38) num_cia_primaria[{back}].select();
else if (event.keyCode == 40) num_cia_primaria[{next}].select();" value="{num_cia_primaria}" size="3">
        <input name="nombre_cia_pri[]" type="text" class="vnombre" id="nombre_cia_pri" value="{nombre_cia_pri}" size="30" readonly="true"></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) num_cia_primaria[{next}].select();
else if (event.keyCode == 37) num_cia_primaria[{i}].select();
else if (event.keyCode == 38) num_cia[{back}].select();
else if (event.keyCode == 40) num_cia[{next}].select();" value="{num_cia}" size="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" value="{nombre_cia}" size="30" readonly="true"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_corto}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "" || num.value == "0") {
		num.value = "";
		nombre.value = "";
	}
	else if (cia[get_val(num)] != null)
		nombre.value = cia[get_val(num)];
	else {
		alert("La compañía no se encuentra en el catálogo");
		num.value = f.tmp.value;
		num.select();
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.num_cia_primaria[0].select();
}

window.onload = f.num_cia_primaria[0].select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame ? top.mainFrame.document.form : top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	f.eval(campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : valid -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	if (top.mainFrame)
		top.mainFrame.location = './ban_fil_alta.php';
	else
		top.location = './ban_fil_alta.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
