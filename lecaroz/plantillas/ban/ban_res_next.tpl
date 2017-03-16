<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {font-size: 12pt}
-->
</style>
</head>

<body>
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		// Actualizar ventana de conciliacion
		window.opener.document.form.accion.value = "siguiente";
		window.opener.document.form.target = "_self";
		window.opener.document.form.action = "./ban_conciliacion.php";
		window.opener.document.form.submit();
		
		// Cerrar ventana actual
		self.close();
	}
</script>
<!-- END BLOCK : cerrar -->
<!-- START BLOCK : resultados -->
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Cuenta</th>
    <th class="tabla" scope="col">Fecha de Conciliaci&oacute;n</th>
  </tr>
  <tr>
    <td class="tabla" scope="row"><font size="+2" color="#0066FF"><strong>{num_cia} - {nombre_cia} </strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{cuenta}</strong></font></td>
    <td class="tabla"><font size="+2" color="#0066FF"><strong>{fecha_con}</strong></font></td>
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="vtabla" scope="col">Saldo Inicial </th>
    <th class="rtabla style1" scope="col"><font size="+1">{saldo_inicial}</font></th>
  </tr>
</table>

<br>
<form name="form" method="post" action="./ban_res_next.php?tabla={tabla}">
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Cargos</th>
    <!-- START BLOCK : cargo -->
	<td class="rtabla"><input name="id{i}" type="hidden" value="{id}"><strong>{cargo}</strong></td>
	<!-- END BLOCK : cargo -->
  </tr>
</table>
<br>
<table class="tabla">
  <tr>
    <th class="vtabla" scope="row">Abonos</th>
    <!-- START BLOCK : abono -->
	<td class="rtabla"><input name="id{i}" type="hidden" value="{id}"><strong>{abono}</strong></td>
	<!-- END BLOCK : abono -->
  </tr>
</table>
</form>
<br>
<table class="tabla">
  <tr>
    <th class="vtabla" scope="col">Saldo Final </th>
    <th class="rtabla" scope="col"><font size="+1">{saldo_inicial}</font></th>
  </tr>
</table>
<p>
  <input name="" type="button" class="boton" onClick="self.close()" value="Cerrar ventana">
&nbsp;&nbsp;
<input type="button" class="boton" value="Conciliar">
</p>
<!-- END BLOCK : resultados -->
</body>
</html>
