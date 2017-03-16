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
<td align="center" valign="middle"><p class="title">Proceso de Diferencias de Inventario</p>
  <form action="./bal_act_inv_v4.php" method="get" name="form">
    <input name="accion" type="hidden" id="accion">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">	
    <input name="mes" type="hidden" id="mes" value="{mes}">
    <input name="anio" type="hidden" id="anio" value="{anio}">    
    <table class="tabla">
    <tr>
      <th class="vtabla"><input name="tipo" type="radio" value="" checked>
        Todas<br>
        <input name="tipo" type="radio" value="TRUE">
        Controladas<br>
        <input name="tipo" type="radio" value="FALSE">
        No controladas </th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Generar Diferencias" onClick="validar(1)">
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar(0)"> 
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar(accion) {
	if (accion == 1) {
		if (confirm("¿Desea generar las diferencias del mes de " + f.mes.value + " del " + f.anio.value + "?")) {
			f.accion.value = accion;
			f.submit();
		}
	}
	else {
		f.accion.value = accion;
		f.submit();
	}
}
-->
</script>
<!-- START BLOCK : alerta -->
<script language="javascript" type="text/javascript">
<!--
function alerta() {
	window.open("./alerta_reservas.htm","","top=284,left=112,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=800,height=200");
	document.location = "./bal_enc_cap.php";
}

window.onload = alerta();
-->
</script>
<!-- END BLOCK : alerta -->
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso de Diferencias de Inventario</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{mes}</td>
      <td class="tabla" style="font-size: 12pt; font-weight: bold;">{anio}</td>
    </tr>
  </table>  <br>
  <form name="data">
    <table class="tabla">
    <tr>
      <th rowspan="2" class="tabla" scope="col">Producto</th>
      <th colspan="2" class="tabla" scope="col">Existencia</th>
      <th rowspan="2" class="tabla" scope="col">Faltantes</th>
      <th rowspan="2" class="tabla" scope="col">Sobrantes</th>
      <th rowspan="2" class="tabla" scope="col">Costo</th>
      <th colspan="2" rowspan="2" class="tabla" scope="col">Total</th>
	  <th rowspan="2" class="tabla" scope="col">Hace un mes</th>
	  <th rowspan="2" class="tabla" scope="col">Hace dos meses</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">C&oacute;mputo</th>
      <th class="tabla" scope="col">F&iacute;sica</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr id="row{i}" onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla" style="font-weight: bold; color: #{color_mp};" onMouseOver="this.style.cursor='pointer';" onMouseOut="this.style.cursor='default';" onClick="aux({num_cia},{codmp},{mes},{anio},{id},{i})">{codmp} {nombre} </td>
      <td class="tabla"><input name="existencia[]" type="text" class="rnombre" id="existencia" style="color: #{color_exi};" value="{existencia}" size="10" readonly="true"></td>
      <td class="tabla"><input name="inventario[]" type="text" class="rnombre" id="inventario" onClick="mod({id},{i})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" value="{inventario}" size="10" readonly="true"></td>
      <td class="tabla"><input name="falta[]" type="text" class="rnombre" id="falta" style="color: #CC0000;" value="{falta}" size="10" readonly="true"></td>
      <td class="tabla"><input name="sobra[]" type="text" class="rnombre" id="sobra" style="color: #0000CC;" value="{sobra}" size="10" readonly="true"></td>
      <td class="tabla"><input name="costo[]" type="text" class="rnombre" id="costo" value="{costo}" size="5" readonly="true"></td>
      <td colspan="2" class="tabla"><input name="total[]" type="text" class="rnombre" id="total" style="color: #{color_t}" value="{total}" size="8" readonly="true"></td>
	  <td class="rtabla">{dif0}</td>
	  <td class="rtabla">{dif1}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="6" class="tabla">&nbsp;</th>
      <th class="tabla"><input name="contra" type="text" disabled="true" class="rnombre" id="contra" style="color: #CC0000; font-size: 12pt;" value="{contra}" size="10"></th>
      <th class="tabla"><input name="favor" type="text" disabled="true" class="rnombre" id="favor" style="color: #0000CC; font-size: 12pt;" value="{favor}" size="10"></th>
	  <th colspan="2" class="tabla">&nbsp;</th>
	  </tr>
    <tr>
      <th colspan="6" class="rtabla">Gran Total </th>
      <th colspan="2" class="tabla"><input name="gran_total" type="text" disabled="true" class="nombre" id="gran_total" style="width: 100%; font-size: 12pt; color: #{color_gt}" value="{total}" size="10"></th>
	  <th colspan="2" class="tabla">&nbsp;</th>
	  </tr>
  </table>
  </form>  
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="row">Consumos Anteriores </th>
      </tr>
    <!-- START BLOCK : consumo_ant -->
	<tr>
      <th class="vtabla" scope="row">{mes} {anio} </th>
      <td class="rtabla">{consumo}</td>
    </tr>
	<!-- END BLOCK : consumo_ant -->
  </table>
  <br>
  <form action="./bal_act_inv_v4.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="tipo" type="hidden" id="tipo" value="{tipo}">
    <table class="tabla">
    <tr>
      <td class="tabla"><input type="button" class="boton" value="Cancelar" onClick="document.location='./bal_act_inv_v4.php?accion=2'"></td>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{num_cia_next}" size="3">
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" value="{nombre_next}" size="30"></td>
      <td class="tabla"><input type="button" class="boton" value="Siguiente >>" onClick="next()"></td>
      </tr>
  </table></form>
  </td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var data = document.data, f = document.form, cia = new Array();

<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia(num, nombre) {
	if (num.value == "") {
		nombre.value = "";
	}
	else if (cia[num.value] != null) {
		nombre.value = cia[num.value];
	}
	else {
		alert("La compañía no se encuentra en el catalogo");
		num.value = num.form.tmp.value;
		num.select();
	}
}

function aux(num_cia, mp, mes, anio, id, i) {
	var opt = "?num_cia=" + num_cia + "&mes=" + mes + "&anio=" + anio + "&codmp=" + mp + "&ctrl=0&tipo=0&dif=1&gas=1&table=real&close=1&act_real=1&id=" + id + "&i=" + i;
	var win = window.open("./aux_inv_v4.php" + opt, "aux", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768");
	win.focus();
}

function mod(id, i) {
	var opt = "?id=" + id + "&i=" + i;
	var win = window.open("./bal_ifm_minimod_v3.php" + opt, "mod", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=640,height=480");
	win.moveTo(192, 144);
	win.focus();
}

function next() {
	f.submit();
}
-->
</script>
<!-- END BLOCK : listado -->
<!-- START BLOCK : bloqueo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;color:#CC0000">ADVERTENCIA</p>
  <p style="font-family:Arial, Helvetica, sans-serif;">Los gastos de las siguientes panaderias no han sido capturados o no han sido pagados </p>  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Gasto</th>
    </tr>
    <!-- START BLOCK : gasto -->
	<tr>
      <td class="vtabla">{num_cia} {nombre} </td>
      <td class="vtabla">{codgastos} {nombre_gasto} </td>
    </tr>
	<!-- END BLOCK : gasto -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_act_inv_v4.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : bloqueo -->
</body>
</html>
