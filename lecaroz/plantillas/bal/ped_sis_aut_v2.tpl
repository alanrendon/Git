<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Sistema de Pedidos Autom&aacute;tico</p>
  <form action="./ped_sis_aut_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <input name="generar" type="hidden" value="1">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) codmp.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Materia Prima </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) dias.select();
else if (event.keyCode == 38) num_cia.select();" size="3" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de d&iacute;as </th>
      <td class="vtabla"><input name="dias" type="text" class="insert" id="dias" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) num_cia.select();
else if (event.keyCode == 38) codmp.select();" size="2" maxlength="2"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td class="print_encabezado">Cia.: {num_cia} </td>
    <td class="print_encabezado" align="center">{nombre_cia}</td>
    <td align="right" class="print_encabezado">Cia.: {num_cia} </td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Pedido Autom&aacute;tico <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th colspan="2" class="print" scope="col">Producto</th>
    <th colspan="2" class="print" scope="col">Consumo</th>
    <th colspan="2" class="print" scope="col">Inventario</th>
    <th colspan="2" class="print" scope="col">Pedido</th>
    <th class="print" scope="col">Comentarios</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="rprint">{cod}</td>
    <td class="vprint">{nombre}</td>
    <td class="rprint">{consumo}</td>
    <td class="vprint">{unidad_consumo}</td>
    <td class="rprint">{inventario}</td>
    <td class="vprint">{unidad_consumo}</td>
    <td class="rprint">{pedido}</td>
    <td class="vprint">{unidad_pedido}</td>
    <td class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
