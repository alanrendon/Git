<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Movimientos Bancarios </p>
  <form action="./ban_dep_cap_v2.php" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Cuenta</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected>SANTANDER</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">N&uacute;mero de Movimientos</th>
      <td class="vtabla"><input name="num_mov" type="text" class="insert" id="num_mov" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{num_mov}" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo_mov" type="radio" value="FALSE" checked>
        Abono
          <input name="tipo_mov" type="radio" value="TRUE">
          Cargo</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p> </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.num_mov.value <= 0) {
		form.num_mov.value = 10;
	}
	
	form.submit();
}

window.onload = document.form.num_mov.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de {mov} Manuales</p>
  <form action="./ban_dep_cap_v2.php" method="post" name="form">
    <input name="temp" type="hidden" id="temp">
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}">
    <input name="tipo_mov" type="hidden" id="tipo_mov" value="{tipo_mov}">    
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Cuenta</th>
      </tr>
      <tr>
        <th class="tabla">{banco}</th>
      </tr>
    </table>    
    <br>
    <table class="tabla">
    <tr>
      <th colspan="3" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Concepto</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="temp.value=this.value" onChange="if (isInt(this,temp)) cambiaCia(this, nombre_cia[{i}], cuenta_banco[{i}])" onKeyDown="if (event.keyCode == 13) if (num_cia.length == undefined) fecha.select(); else fecha[{i}].select();" size="3" maxlength="3"></td>
      <td class="tabla"><input name="nombre_cia[]" type="text" disabled="true" class="vnombre" id="nombre_cia" size="20" maxlength="20"></td>
      <td class="tabla"><input name="cuenta_banco[]" type="text" disabled class="nombre" id="cuenta_banco" size="11" maxlength="11"></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onChange="inputDateFormat(this)" onKeyDown="if (event.keyCode == 13) if (num_cia.length == undefined) importe.select(); else importe[{i}].select();" value="{fecha}" size="10" maxlength="10"></td>
      <td class="tabla"><select name="cod_mov[]" class="insert" id="cod_mov">
        <!-- START BLOCK : cod_mov -->
		<option value="{cod_mov}">{cod_mov} {descripcion}</option>
		<!-- END BLOCK : cod_mov -->
      </select></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="temp.value=this.value" onChange="if (inputFormat(this,2,temp)) Total()" onKeyDown="if (event.keyCode == 13) if (num_cia.length == undefined) concepto.select(); else concepto[{i}].select();" size="12" maxlength="12"></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onKeyDown="if (event.keyCode == 13) if (num_cia.length == undefined) num_cia.select(); else num_cia[{next}].select();" size="30" maxlength="1000"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="5" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="0.00" size="12" maxlength="12"></th>
      <th class="tabla">&nbsp;</th>
      </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./ban_dep_cap_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;
var cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = ["{nombre_cia}", '{cuenta_banco}'];
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre, cuenta_banco) {
	if (num.value == "") {
		nombre.value = "";
		cuenta_banco.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value][0];
		cuenta_banco.value = cia[num.value][1];
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.temp.value;
		num.select();
	}
}

function Total() {
	var total = 0;
	
	if (form.num_cia.length == undefined) {
		total = /*!isNaN(parseFloat(form.importe.value)) ? parseFloat(form.importe.value) : 0*/get_val(form.importe);
	}
	else {
		for (i = 0; i < form.num_cia.length; i++) {
			total += /*!isNaN(parseFloat(form.importe[i].value)) ? parseFloat(form.importe[i].value) : 0*/get_val(form.importe[i]);
		}
	}
	
	form.total.value = /*total.toFixed(2)*/numberFormat(total, 2);
}

function checkAll(checkall) {
	if (form.num_cia.length == undefined) {
		form.ficha0.checked = checkall.checked ? true : false;
	}
	else {
		for (i = 0; i < form.num_cia.length; i++) {
			document.getElementById("ficha" + i).checked = checkall.checked ? true : false;
		}
	}
}

function validar() {
	if (confirm("¿Desea capturar lo movimientos?")) {
		form.submit();
	}
	else {
		if (form.num_cia.length == undefined) {
			form.num_cia.select();
		}
		else {
			form.num_cia[0].select();
		}
		return false;
	}
}

window.onload = form.num_cia.length == undefined ? form.num_cia.select() : form.num_cia[0].select();
-->
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
    <td width="60%" class="print_encabezado" align="center">Movimientos Capturados <br>
    al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Cia</th>
    <th class="print" scope="col">Cuenta</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">C&oacute;digo</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Importe</th>
    <th class="print" scope="col">Fecha</th>
  </tr>
  <!-- START BLOCK : mov -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="print">{num_cia}</td>
    <td class="print">{cuenta}</td>
    <td class="vprint">{nombre_cia}</td>
    <td class="vprint">{cod_mov} {descripcion} </td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
    <td class="print">{fecha}</td>
  </tr>
  <!-- END BLOCK : mov -->
  <tr>
    <th colspan="5" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
    <th class="print">&nbsp;</th>
  </tr>
</table>

<!-- END BLOCK : listado -->
</body>
</html>
