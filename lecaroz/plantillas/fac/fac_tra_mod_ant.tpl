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
		if (window.opener.document.form.antiguedad.length == undefined)
			window.opener.document.form.antiguedad.value = "{antiguedad}";
		else
			window.opener.document.form.antiguedad[{i}].value = "{antiguedad}";
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
<form action="./fac_tra_mod_ant.php" method="post" name="form" onKeyDown="if (event.keyCode == 13) return false">
<input type="hidden" name="i" value="{i}">
<input type="hidden" name="id" value="{id}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row"><input name="tipo" type="radio" onClick="if (this.checked) {
anios.disabled=false;
meses.disabled=false;
fecha_alta.disabled=true;
}" value="1" checked>
      Calculo</th>
    <td class="vtabla" scope="row"><select name="anios" class="insert" id="anios">
      <!-- START BLOCK : anios -->
	  <option value="{anios}" {selected}>{anios}</option>
	  <!-- END BLOCK : anios -->
    </select> 
      A&ntilde;os&nbsp;&nbsp;&nbsp;
      <select name="meses" class="insert" id="meses">
        <!-- START BLOCK : meses -->
		<option value="{meses}" {selected}>{meses}</option>
		<!-- END BLOCK : meses -->
      </select> 
      Meses  </td>
    </tr>
  <tr>
    <th class="vtabla" scope="row"><input name="tipo" type="radio" onClick="if (this.checked) {
anios.disabled=true;
meses.disabled=true;
fecha_alta.disabled=false;
fecha_alta.select();
}" value="2">
      Fecha</th>
    <td class="vtabla" scope="row"><input name="fecha_alta" type="text" disabled="true" class="insert" id="fecha_alta" value="{fecha_alta}" size="10" maxlength="10"></td>
  </tr>
</table>

  <p>
    <input name="cancel" type="button" class="boton" id="cancel" onClick="self.close()" value="Cancelar">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Modificar" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.tipo[0].checked) {
			if (form.anios.value == "" && form.meses.value == "") {
				alert("Debe especificar por lo menos un mes de antigüedad");
				return false;
			}
			else
				form.submit();
		}
		else if (form.tipo[1].checked) {
			if (!actualiza_fecha(form.fecha_alta)) {
				alert("La fecha de alta no puede quedarse en blanco");
				form.fecha_alta.select();
				return false;
			}
			else
				form.submit();
		}
	}
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
