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
<td align="center" valign="middle"><p class="title">Carga de Archivo de Dep&oacute;sitos </p>
  <form action="ban_dep_arc.php" method="post" enctype="multipart/form-data" name="form">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Archivo CSV </th>
      <td class="vtabla"><input name="archivo" type="file" class="insert" id="archivo" onkeydown="if (event.keyCode == 13) this.blur()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select>
      </td>
    </tr>
  </table>  
    <p style=" font-family:Arial, Helvetica, sans-serif; font-weight:bold;">NOTA: El formato del archivo debe ser 'CSV delimitado por comas', sin t&iacute;tulos ni encabezados y los importes no deben contener 'coma' como separador de miles. Las columnas del archivo deben ir en el siguiente orden: (1) Compa&ntilde;&iacute;a, (2) Importe, (3) Fecha, (4) Comprobante</p>
    <p>
    <input type="button" class="boton" value="Cargar" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.archivo.value == '') {
		alert('No ha especificado el archivo de datos');
		f.archivo.focus();
	}
	else if (confirm('¿Desea cargar el archivo?'))
		f.submit();
}

window.onload = f.archivo.focus();
//-->
</script>
</body>
</html>
