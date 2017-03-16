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
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.form.turno[{i}].value = "{descripcion}";
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla">
  <tr>
    <th colspan="2" class="tabla" scope="col">Nombre</th>
    </tr>
  <tr>
    <td colspan="2" class="tabla" scope="col"><strong>{nombre}</strong></td>
    </tr>
  <tr>
    <th class="tabla" scope="col">Puesto</th>
    <th class="tabla" scope="col">Turno</th>
    </tr>
  <tr>
    <td class="tabla"><strong>{puesto}</strong></td>
    <td class="tabla"><strong>{turno}</strong></td>
    </tr>
</table>
<br>
<form action="./fac_tra_mod_tur.php" method="post" name="form">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="i" value="{i}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Turno</th>
    <td class="vtabla"><select name="turno" class="insert" id="puesto">
      <!-- START BLOCK : turno -->
	  <option value="{id}" {selected}>{turno}</option>
	  <!-- END BLOCK : turno -->
    </select></td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="submit" class="boton" value="Modificar">
</p></form></td>
</tr>
</table>
<!-- END BLOCK : modificar -->
</body>
</html>
