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
<!-- START BLOCK : cia -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Baja de Expendios</p>
  <form action="./pan_exp_del.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="enviar" type="button" class="boton" value="Siguiente" onClick="if(document.form.num_cia.value  > 0) document.form.submit(); else{alert('debe ingresar la compañía'); document.form.num_cia.select();}">
  </p></form>
<script language="JavaScript" type="text/JavaScript">
window.onload=document.form.num_cia.select();
</script>
  
</td>
</tr>
</table>
<!-- END BLOCK : cia -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Baja de Expendios</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    </tr>
    <tr>
      <td class="tabla">        {num_cia} {nombre_cia}</td>
    </tr>
  </table>
  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">N&uacute;mero y Nombre de Expendio </th>
      <th class="tabla" scope="col">&nbsp;</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla">{num_expendio}</td>
      <td class="vtabla">{nombre_expendio}</td>
      <td class="tabla"><input name="" type="button" class="boton" onClick="borrar({num_cia},{id})" value="Borrar"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_exp_del.php'">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function borrar(num_cia,id) {
		if (confirm("¿Desea eliminar el expendio?"))
			document.location = "./pan_exp_del.php?num_cia=" + num_cia + "&id=" + id;
		else
			return false;
	}
</script>
<!-- END BLOCK : listado -->
</body>
</html>
