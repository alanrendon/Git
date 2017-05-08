<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Faltantes de Cometra </p>
  <form action="./ban_fal_com.php" method="post" name="form">
  <input name="temp" type="hidden">
  <input name="listado" type="hidden" id="listado" value="0">  
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Dep&oacute;sito</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Descripci&oacute;n</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) actualiza_cia(this,nombre_cia[{i}])" onKeyDown="if (event.keyCode == 13) fecha[{i}].select()" size="3" maxlength="3">
        <input name="nombre_cia[]" type="text" class="vnombre" id="nombre_cia" size="30"></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onFocus="temp.value=this.value" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) deposito[{i}].select()" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><input name="deposito[]" type="text" class="rinsert" id="deposito" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) importe[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="isFloat(this,2,temp)" onKeyDown="if (event.keyCode == 13) descripcion[{i}].select()" size="10" maxlength="10"></td>
      <td class="tabla"><select name="tipo[]" class="insert" id="tipo">
        <option value="FALSE">FALTANTE</option>
        <option value="TRUE">SOBRANTE</option>
      </select></td>
      <td class="tabla"><h5>
        <input name="descripcion[]" type="text" class="vinsert" id="descripcion" onKeyDown="if (event.keyCode == 13) num_cia[{next}].select()" size="50" maxlength="100">
      </h5></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
&nbsp;&nbsp;
<input type="button" class="boton" value="Terminar" onClick="terminar(this.form)"> 
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function actualiza_cia(num_cia, nombre) {
		cia = new Array();
		<!-- START BLOCK : nombre_cia -->
		cia[{num_cia}] = '{nombre_cia}';
		<!-- END BLOCK : nombre_cia -->
		
		if (parseInt(num_cia.value) > 0) {
			if (cia[parseInt(num_cia.value)] == null) {
				alert("Compañía "+parseInt(num_cia.value)+" no esta en el catálogo de compañías");
				num_cia.value = "";
				nombre.value  = "";
				num_cia.focus();
				return false;
			}
			else {
				num_cia.value = parseFloat(num_cia.value);
				nombre.value  = cia[parseInt(num_cia.value)];
				return;
			}
		}
		else if (num_cia.value == "") {
			num_cia.value = "";
			nombre.value  = "";
			return false;
		}
	}
	
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia[0].select();
	}
	
	function terminar(form) {
		if (confirm("¿Son correctos los datos?")) {
			form.listado.value = 1;
			form.submit();
		}
		else
			form.num_cia[0].select();
	}
	
	window.onload = document.form.num_cia[0].select();
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Faltantes Capturados el {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
<table width="50%" align="center" class="print">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="5" class="print" scope="col"><font size="+1" color="#000000">{num_cia} - {nombre_cia}</font> </th>
    </tr>
    <tr>
      <th width="10%" class="print"><font color="#000000">Fecha</font></th>
      <th width="20%" class="print"><font color="#000000">Dep&oacute;sito</font></th>
	  <th width="20%" class="print"><font color="#000000">Faltante</font></th>
      <th width="20%" class="print"><font color="#000000">Sobrante</font></th>
      <th width="30%" class="print"><font color="#000000">Descripci&oacute;n</font></th>
    </tr>
    <!-- START BLOCK : fila_lis -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="print">{fecha}</td>
	  <td class="rprint">{deposito}</td>
      <td class="rprint"><font color="#0000FF">{faltante}</font></td>
      <td class="rprint"><font color="#FF0000">{sobrante}</font></td>
      <td class="vprint">{descripcion}</td>
    </tr>
	<!-- END BLOCK : fila_lis -->
    <!-- START BLOCK : totales -->
	<tr>
      <th class="rprint"><font color="#000000">Totales</font></th>
      <th class="rprint_total">{deposito}</th>
	  <th class="rprint_total">{faltante}</th>
      <th class="rprint_total">{sobrante}</th>
      <th class="print">&nbsp;</th>
    </tr>
	<tr>
	  <th colspan="2" class="rprint"><font color="#000000">Diferencia</font></th>
	  <th colspan="2" class="print_total">{diferencia}</th>
	  <th class="print">&nbsp;</th>
    </tr>
	<!-- END BLOCK : totales -->
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
	<!-- END BLOCK : cia -->
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col"><font size="+1" color="#000000">Faltantes</font></th>
    <th class="print" scope="col"><font size="+1" color="#000000">Sobrantes</font></th>
    <th class="print" scope="col"><font size="+1" color="#000000">Diferencia</font></th>
  </tr>
  <tr>
    <th class="print"><font size="+1" color="#000000">{faltantes}</font></th>
    <th class="print"><font size="+1" color="#000000">{sobrantes}</font></th>
    <th class="print"><font size="+1" color="#000000">{diferencia}</font></th>
  </tr>
</table>

<!-- END BLOCK : listado -->
</body>
</html>
