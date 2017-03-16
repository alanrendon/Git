<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Gastos Pendientes de Autorizar</p>
  <form action="./adm_val_gas.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="vtabla" scope="col">{num_cia} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla"><img src="../../menus/insert.gif" width="16" height="16" /></th>
      <th class="tabla"><img src="../../menus/delete.gif" width="16" height="16" /></th>
      <th class="tabla">Fecha</th>
      <th class="tabla">C&oacute;digo</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="val{i}" type="checkbox" id="val{i}" value="{id}" onclick="if (del{i}.checked) del{i}.checked=false" /></td>
      <td class="tabla"><input name="del{i}" type="checkbox" id="del{i}" value="{id}" onclick="if (val{i}.checked) val{i}.checked=false" /></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{cod} {desc} </td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="5" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
    </tr>
    <tr>
      <td colspan="6" class="tabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : cia -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" />
</p></form></td>
</tr>
</table>
</body>
</html>
