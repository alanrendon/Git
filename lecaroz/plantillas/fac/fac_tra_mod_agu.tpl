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
	function calcula_total() {
		var total = 0;
		
		if (window.opener.document.form.aguinaldo.length == undefined)
			total = window.opener.document.form.aguinaldo.value.replace(",","");
		else {
			for (i = 0; i < window.opener.document.form.aguinaldo.length; i++) {
				total += !isNaN(parseFloat(window.opener.document.form.aguinaldo[i].value.replace(",",""))) ? parseFloat(window.opener.document.form.aguinaldo[i].value.replace(",","")) : 0;
			}
		}
		window.opener.document.form.total_aguinaldo.value = total;
	}
	
	function cerrar() {
		if (window.opener.document.form.aguinaldo.length == undefined)
			window.opener.document.form.aguinaldo.value = "{aguinaldo}";
		else
			window.opener.document.form.aguinaldo[{i}].value = "{aguinaldo}";
		calcula_total();
		{codigo_extra}
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
<form action="./fac_tra_mod_agu.php" method="post" name="form" onKeyDown="if (event.keyCode == 13) return false">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="i" value="{i}">
<input type="hidden" name="idaguinaldo" value="{idaguinaldo}">
<input type="hidden" name="anio" value="{anio}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Aguinaldo</th>
    <td class="vtabla"><input name="aguinaldo" type="text" class="rinsert" id="aguinaldo" onFocus="tmp.value=this.value;this.select()" onClick="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{aguinaldo}" size="10" maxlength="10"></td>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;
<input type="button" class="boton" value="Modificar" onClick="validar(this.form)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (get_val(form.aguinaldo) < 0) {
			alert("Debe especificar el importe del aguinaldo o dejarlo vacio");
			form.aguinaldo.select();
			return false;
		}
		else if (form.aguinaldo.value == '' || form.aguinaldo.value == '0') {
			if (confirm("¿Desea borrar el aguinaldo del empleado?"))
				form.submit();
		}
		else
			form.submit();
	}
	
	window.onload = document.form.aguinaldo.select();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
