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
<td align="center" valign="middle">
<p class="title">Asignaci&oacute;n de orden de expendios </p>
<form name="form" method="get" action="pan_exp_ord.php">
<input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td class="tabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="form.temp.value=this.value" onChange="isInt(this,form.this)" onKeyDown="if (event.keyCode == 13) form.siguiente.focus();" size="3" maxlength="3"></td>
    </tr>
  </table>  <p>
    <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.num_cia.value <= 0) {
			alert("Debe especificar una compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : cia -->

<!-- START BLOCK : orden -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Asignaci&oacute;n de orden de expendios </p>
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <tr>
    <th class="tabla">{num_cia} - {nombre_cia} </th>
  </tr>
</table><br>
  <form name="form" method="post" action="./pan_exp_ord.php?tabla=catalogo_expendios">
  <input name="temp" type="hidden">
  <input name="num_cia" type="hidden" value="{num_cia}">
  <input name="numfilas" type="hidden" value="{numfilas}">
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Expendio</th>
      <th class="tabla" scope="col">Orden</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="num_expendio{i}" type="hidden" id="num_expendio{i}" value="{num_exp}">
        <strong>{num_exp}</strong></td>
      <td class="vtabla"><strong>{nombre_exp}</strong></td>
      <td class="tabla"><input name="num_orden{i}" type="text" class="insert" id="num_orden{i}" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.num_orden{next}.select();
else if (event.keyCode == 38) form.num_orden{back}.select();" value="{num_orden}" size="3" maxlength="3"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="form.submit()">
  </p>
  </form>  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">window.onload = document.form.num_orden0.select();</script>
<!-- END BLOCK : orden -->
</body>
</html>
