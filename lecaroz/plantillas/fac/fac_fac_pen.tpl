<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas pendientes de pago al d&iacute;a de hoy</p>
  <form action="./fac_fac_pen.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[1].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[2].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[3].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[4].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[5].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[6].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[7].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[8].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[9].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha de facturaci&oacute;n </th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia[0].select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" /> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = function () { showAlert = true; f.num_cia[0].select(); }
//-->
</script>
</body>
</html>
