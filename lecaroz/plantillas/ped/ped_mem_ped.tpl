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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Memorandum de Pedidos</p>
  <form action="./ped_mem_ped.php" method="get" name="form" target="memo_ped">
    <input name="temp" type="hidden" id="temp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadministrador" class="insert" id="idadministrador">
        <option value="" selected>-</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Pedido complementario</th>
      <td class="vtabla"><input name="com" type="checkbox" id="com" value="1">
        Si</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)")>
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.anio.value <= 0) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else {
		window.open("","memo_ped","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,width=800,height=600");
		form.submit();
	}
}

window.onload = document.form.anio.select();
-->
</script>
</body>
</html>
