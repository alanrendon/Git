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
<td align="center" valign="middle"><p class="title">B&uacute;squeda Avanzada de Movimientos en Estado de Cuenta</p>
  <form action="./ban_bus_esc.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1" selected>BANORTE</option>
        <option value="2">SANTANDER SERFIN</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha <font size="-2">(ddmmaa)</font> </th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha}" size="10" maxlength="10">
        ... ( a 
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) importe.select()" size="10" maxlength="10"> 
          ) </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Movimiento </th>
      <td class="vtabla"><select name="tipo_mov" class="insert" id="tipo_mov">
        <option selected> </option>
        <option value="FALSE">DEPOSITOS</option>
        <option value="TRUE">RETIROS</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Importe</th>
      <td class="vtabla"><input name="importe" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) folio.select()" size="10" maxlength="10">
        &nbsp;&nbsp;
        <input name="tipo_importe" type="radio" value="exacto" checked>
        Exacto&nbsp;&nbsp;
        <input name="tipo_importe" type="radio" value="parecido">
        Parecido </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><select name="cod_mov" class="insert" id="cod_mov">
        <option> </option>
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}">{cod_mov} {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) concepto.select()" size="10" maxlength="10"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="30" maxlength="200">
        &nbsp;&nbsp;
        <input name="tipo_concepto" type="radio" value="exacto" checked>
        Exacto&nbsp;&nbsp;
        <input name="tipo_concepto" type="radio" value="parecido">
        Parecido</td>
    </tr>
  </table>  
  <p>
    <input type="button" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.num_cia.value <= 0 && form.fecha1.value.length < 8 && form.tipo_mov.value == "" && form.importe.value <= 0 && form.concepto.value == "" && form.folio.value <= 0 && form.concepto.value.length < 3) {
			alert("Debe especificar al menos un criterio de busqueda");
			form.num_cia.select();
			return false;
		}
		else
			form.submit();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Resultado de la b&uacute;squeda</p>
  <table class="tabla">
    <!-- START BLOCK : result -->
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Conciliado</th>
      <th class="tabla" scope="col">Dep&oacute;sito</th>
      <th class="tabla" scope="col">Retiro</th>
      <th class="tabla" scope="col">Folio</th>
      <th colspan="2" class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="rtabla">{num_cia}</td>
      <td class="vtabla">{nombre_cia}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{fecha_con}</td>
      <td class="rtabla"><strong><font color="#0000FF">{deposito}</font></strong></td>
      <td class="rtabla"><strong><font color="#FF0000">{retiro}</font></strong></td>
      <td class="tabla">{folio}</td>
      <td class="rtabla">{cod_mov}</td>
      <td class="vtabla">{descripcion}</td>
      <td class="vtabla">{concepto}</td>
    </tr>
    <!-- END BLOCK : fila -->
    <!-- END BLOCK : result -->
    <!-- START BLOCK : no_result -->
    <tr>
      <th class="tabla">No hay resultados </th>
    </tr>
    <!-- END BLOCK : no_result -->
  </table>
  <p><font face="Geneva, Arial, Helvetica, sans-serif" size="-1">{num_reg} registros encontrados.</font></p>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_bus_esc.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
