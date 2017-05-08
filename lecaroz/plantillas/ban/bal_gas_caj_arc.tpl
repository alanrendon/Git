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
<td align="center" valign="middle"><p class="title">Carga de Archivo de Gastos</p>
  <form action="./bal_gas_caj_arc.php" method="post" enctype="multipart/form-data" name="form">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Archivo CSV </th>
      <td class="vtabla"><input name="archivo" type="file" class="insert" id="archivo" onkeydown="if (event.keyCode == 13) this.blur()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><select name="cod_gastos" class="insert" id="cod_gastos">
        <option value="{cod}">{desc}</option>
        <!-- START BLOCK : cod -->
		<!-- END BLOCK : cod -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Aplica a balance </th>
      <td class="vtabla"><input name="clave_balance" type="checkbox" id="clave_balance" value="checkbox" />
        Si</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo_mov" type="radio" value="FALSE" checked="checked" />
        Egresos
          <input name="tipo_mov" type="radio" value="TRUE" />
          Ingresos</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cargar" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.archivo.value == '')
		alert('No ha especificado el archivo');
	else if (confirm('¿Desea cargar el archivo?'))
		f.submit();
}
//-->
</script>
</body>
</html>
