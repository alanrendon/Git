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
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Tipos de Documentos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{descripcion}</td>
      <td class="tabla"><input type="button" class="boton" value="Modificar" onClick="mod({id})">
        <input type="button" class="boton" value="Borrar" onClick="del({id})"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <!-- START BLOCK : no_result -->
	<tr>
      <th colspan="2" class="tabla">No hay resultados </th>
      </tr>
	  <!-- END BLOCK : no_result -->
  </table>  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function mod(id) {
		document.location = "./doc_cat_doc_con.php?accion=mod&id=" + id;
	}
	
	function del(id) {
		if (confirm("¿Desea borrar el registro?"))
			document.location = "./doc_cat_doc_con.php?accion=del&id=" + id;
		else
			return false;
	}
</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar de Tipo de Documento</p>
  <form action="./doc_cat_doc_con.php" method="post" name="form" onKeyDown="if (event.keyCode == 13) return false">
  <input name="tipo_doc" type="hidden" value="{tipo_doc}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="descripcion" type="text" class="vinsert" id="descripcion" value="{descripcion}" size="30" maxlength="100"></td>
      <th class="vtabla">Tama&ntilde;o</th>
      <td class="vtabla"><select name="tipo_hoja" class="insert" id="tipo_hoja">
        <option value="1" {1}>CARTA</option>
        <option value="2" {2}>OFICIO</option>
      </select></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./doc_cat_doc_con.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.descripcion.value.length < 3) {
			alert("Debe escribir la descripcion del documento");
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
<!-- END BLOCK : modificar -->
</body>
</html>
