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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Facturas de Pan</p>
  <form action="./pan_fac_con.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) anio.select();" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}º>ENERO</option>
        <option value="2" {2}>FEBRERO</option>
        <option value="3" {3}>MARZO</option>
        <option value="4" {4}>ABRIL</option>
        <option value="5" {5}>MAYO</option>
        <option value="6" {6}>JUNIO</option>
        <option value="7" {7}>JULIO</option>
        <option value="8" {8}>AGOSTO</option>
        <option value="9" {9}>SEPTIEMBRE</option>
        <option value="10" {10}>OCTUBRE</option>
        <option value="11" {11}>NOVIEMBRE</option>
        <option value="12" {12}>DICIEMBRE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) num_cia.select();" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value <= 0) {
			alert("Debe especificar la fecha");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}
	
	document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Facturas de Pan</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="row">Mes</th>
      <th class="tabla">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" scope="row"><strong>{mes}</strong></td>
      <td class="tabla"><strong>{anio}</strong></td>
    </tr>
  </table>  <br>
  <!-- START BLOCK : cia -->
  <table width="35%" class="tabla">
    <tr>
      <th colspan="5" class="tabla" scope="row">{num_cia} - {nombre_cia} </th>
      </tr>
    <tr>
      <th width="10%" class="tabla" scope="row">D&iacute;a</th>
      <th width="30%" class="tabla">Dep&oacute;sitos</th>
      <th width="30%" class="tabla">Facturas Clientes </th>
      <th width="30%" class="tabla">Facturas</th>
      <th width="30%" class="tabla">Diferencia</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla" scope="row">{dia}</td>
      <td class="rtabla">{depositos}</td>
      <td class="rtabla">{facturas}</td>
      <td class="rtabla">{fac}</td>
      <td class="rtabla"><strong>{diferencia}</strong></td>
      </tr>
	   <!-- END BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="tabla" scope="row">Total</th>
	  <th class="rtabla">{depositos}</th>
	  <th class="rtabla">{facturas}</th>
	  <th class="rtabla">{fac}</th>
	  <th class="rtabla">{diferencia}</th>
	  </tr>
  </table>
  <br>
  <input type="button" class="boton" value="Regresar" onClick="document.location='./pan_fac_con.php'">
  <!-- END BLOCK : cia --></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
