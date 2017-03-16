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
		window.opener.document.location.reload();
		window.opener.opener.location = 'ban_con_dep_v2.php';
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificar Depósito</p>
<table class="tabla">
   <tr>
     <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
     </tr>
   <tr>
     <th class="tabla">{num_cia} - {nombre_cia}</th>
     </tr>
 </table>
 <br>
<form name="form" method="post" action="./ban_dep_minimod.php" onKeyDown="if (event.keyCode == 13) return false">
<input name="id" type="hidden" value="{id}">
<table class="tabla">
   <tr>
     <th class="tabla" scope="col">Concepto</th>
     <th class="tabla" scope="col">Importe</th>
     <th class="tabla" scope="col">Fecha Conciliaci&oacute;n</th>
     <th class="tabla" scope="col">C&oacute;digo Movimiento </th>
     <th class="tabla" scope="col">Fecha Dep&oacute;sito <font size="-2">(ddmmaa)</font> </th>
   </tr>
   <tr>
     <td class="vtabla">{concepto}</td>
     <td class="rtabla"><strong>{importe}</strong></td>
     <td class="tabla">{fecha_con}</td>
     <td class="tabla"><select name="cod_mov" class="insert" id="cod_mov">
       <!-- START BLOCK : cod_mov -->
	   <option value="{cod_mov}" {selected}>{cod_mov} - {descripcion}</option>
	   <!-- END BLOCK : cod_mov -->
     </select></td>
     <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) {
mod.focus();
fecha.select();
}" value="{fecha}" size="10" maxlength="10"></td>
   </tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar ventana" onClick="self.close()">
  &nbsp;&nbsp;
  <input name="mod" type="button" class="boton" id="mod" onClick="valida_registro()" value="Modificar">
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		/*if (actualiza_fecha(document.form.fecha) == false) {
			alert("Debe especificar la fecha de depósito");
			document.form.fecha.select();
			return false;
		}*/
		if (document.form.fecha.value == "") {
			alert("Debe especificar la fecha de depósito");
			document.form.fecha.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.fecha.select();
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
