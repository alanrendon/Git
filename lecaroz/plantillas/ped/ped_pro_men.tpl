<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Provisi&oacute;n Mensual de Pedidos </p>
  <form action="./ped_pro_men.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
        <option value="-1" style="color: #FF0000;">TODOS LOS ADMINISTRADORES</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) dias.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Consumo para </th>
      <td class="vtabla"><input name="dias" type="text" class="insert" id="dias" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="2" maxlength="2">
        D&iacute;as</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="0" checked>
        Todos<br>
        <input name="tipo" type="radio" value="1">
        Sobrante<br>
        <input name="tipo" type="radio" value="2">
        Faltante</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.anio.value <= 2000) {
		alert("Debe especificar el año de consulta");
		form.anio.select();
		return false;
	}
	else
		form.submit();
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre}</td>
    <td class="rprint_encabezado">Cia: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Provisi&oacute;n Mensual de Pedidos <br>
    {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th class="print" scope="col">Existencia<br>
    Inicial</th>
    <th class="print" scope="col">Compras</th>
    <th class="print" scope="col">Consumo<br>
    Promedio</th>
    <th class="print" scope="col">Pedido</th>
    <th class="print" scope="col">Diferencia</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="rprint">{codmp}</td>
    <td class="vprint">{nombre}</td>
    <td class="rprint">{existencia}</td>
    <td class="rprint">{compras}</td>
    <td class="rprint">{consumo}</td>
    <td class="rprint">{pedido}</td>
    <td class="rprint">{dif}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
