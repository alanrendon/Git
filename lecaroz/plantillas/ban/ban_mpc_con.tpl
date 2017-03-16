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
<td align="center" valign="middle"><p class="title">Listado de movimientos no conciliados</p>
  <form name="form" method="get" action="./ban_mpc_con.php" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla"><label><input name="cias" type="radio" onClick="form.num_cia.disabled=true;" value="todas">
        Todas la compa&ntilde;&iacute;as</label> <br>
        <label><input name="cias" type="radio" onClick="form.num_cia.disabled=true;" value="pan">
        Panader&iacute;as</label><br>
        <label><input name="cias" type="radio" onClick="form.num_cia.disabled=true;" value="ros">
        Rosticer&iacute;as</label><br>
        <label><input name="cias" type="radio" onClick="form.num_cia.disabled=false;form.num_cia.select();" value="cia" checked>
        Compa&ntilde;&iacute;a</label>&nbsp;
        <input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha.select();" size="3" maxlength="3"></th>
      <th class="vtabla"><label><input name="mov" type="radio" value="todas" checked>
        Todos los movimientos </label><br>
        <label><input name="mov" type="radio" value="dep">
        S&oacute;lo dep&oacute;sitos</label><br>
       <label> <input name="mov" type="radio" value="ret">
        S&oacute;lo retiros</label> </th>
    </tr>
    <tr>
      <th class="vtabla">Fecha de Corte</th>
      <th class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select();" value="{fecha}" size="10" maxlength="10"></th>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p>
  </form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.cias[3].checked == true && document.form.num_cia <= 0) {
			alert("Debe especificar la compañía");
			document.form.num_cia.select();
			return false;
		}
		else
			document.form.submit();
	}
	window.onload=document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->

<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="top">
<p class="title">Movimientos no conciliados </p>
<!-- START BLOCK : cia -->
<table width="100%" class="tabla">
  <tr>
    <th class="tabla" scope="col">Cia.: {num_cia} </th>
    <th colspan="2" class="tabla" scope="col">Cuenta: {cuenta} </th>
    <th colspan="3" class="tabla" scope="col">{nombre_cia}</th>
    </tr>
  <tr>
    <th width="10%" class="tabla">Fecha</th>
    <th width="10%" class="tabla">Dep&oacute;sito</th>
    <th width="10%" class="tabla">Retiro</th>
    <th width="10%" class="tabla">Folio</th>
    <th width="30%" class="tabla">Beneficiario</th>
    <th width="30%" class="tabla">Concepto</th>
    </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="tabla">{fecha}</td>
    <td class="rtabla"><strong><font color="#0000FF">{deposito}</font></strong></td>
    <td class="rtabla"><strong><font color="#FF0000">{retiro}</font></strong></td>
    <td class="tabla">{folio}</td>
    <td class="vtabla">{beneficiario}</td>
    <td class="vtabla">{concepto}</td>
    </tr>
	<!-- END BLOCK : fila -->
  <tr>
    <th class="rtabla">Totales</th>
    <th class="rtabla">{total_depositos}</th>
    <th class="rtabla">{total_retiros}</th>
    <th colspan="3" class="tabla">&nbsp;</th>
    </tr>
</table>
<br>
<!-- END BLOCK : cia -->
<input name="Input" type="button" class="boton" onClick="document.location='./ban_mpc_con.php'" value="Terminar">
&nbsp;&nbsp;
<input name="Button" type="button" class="boton" value="Imprimir" onClick="window.print()"></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
