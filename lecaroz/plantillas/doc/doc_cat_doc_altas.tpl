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
<td align="center" valign="middle"><p class="title">Alta de Tipos de Documento</p>
  <form action="./doc_cat_doc_altas.php" method="post" name="form" onKeyDown="if (event.keyCode == 13) return false"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" size="30" maxlength="100"></td>
      <th class="vtabla">Tama&ntilde;o</th>
      <td class="vtabla"><select name="tipo_hoja" class="insert" id="tipo_hoja">
        <option value="1">CARTA</option>
        <option value="2" selected>OFICIO</option>
      </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Alta" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.descripcion.value.length < 3) {
			alert("Debe escribir la descripcion del nuevo documento");
			form.descripcion.select();
			return false;
		}
		else if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.descripcion.select();
	}
	
	window.onload = document.form.descripcion.select();
</script>
</body>
</html>
