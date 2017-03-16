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
<td align="center" valign="middle"><p class="title">Consulta de Gastos Obligatorios</p>
  <form action="./bal_gas_obl_con.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codgastos.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="3"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Gastos Obligatorios</p>
  <form action="./bal_gas_obl_con.php" method="post" name="form">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="codgastos" type="hidden" id="codgastos" value="{codgastos}">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Gasto</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"></td>
      <td class="vtabla">{num_cia} {nombre} </td>
      <td class="vtabla">{cod} {desc} </td>
      </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_gas_obl.php'">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Borrar" onClick="borrar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function borrar() {
	var cont = 0;
	
	if (f.id.length == undefined)
		cont += f.id.checked ? 1 : 0;
	else
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	
	if (cont == 0)
		alert("Debe seleccionar al menos un registro");
	else if (confirm("¿Desea borrar los registros seleccionados?"))
		f.submit();
	else
		return false;
}
//-->
</script>
<!-- END BLOCK : listado -->
</body>
</html>
