<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
<link href="/lecaroz/styles/Tips.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<style type="text/css">
#info_tip {
  border-collapse: collapse;
  border: solid 1px #000;
  background-color: #fff;
  font-family: Arial;
  font-size: 8pt;
}

#info_tip td,
#info_tip th {
  border: solid 1px #000;
}

#info_tip th {
  background-color: #999;
}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso de Diferencias de Inventario</p>
  <form action="./bal_act_inv_v3.php" method="get" name="form">
    <input name="accion" type="hidden" id="accion">
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}">
    <input name="mes" type="hidden" id="mes" value="{mes}">
    <input name="anio" type="hidden" id="anio" value="{anio}">
    <table class="tabla">
    <tr>
      <th class="vtabla">Consultar</th>
      <td class="vtabla"><input name="tipo" type="radio" value="" checked>
        Todas<br>
        <input name="tipo" type="radio" value="TRUE">
        Controladas<br>
        <input name="tipo" type="radio" value="FALSE">
        No controladas </td>
    </tr>
    <tr>
      <th class="vtabla">Generar</th>
      <td class="vtabla"><input name="generar" type="radio" value="0" checked>
        Todo<br>
        <input name="generar" type="radio" value="1">
        Panader&iacute;as<br>
        <input name="generar" type="radio" value="2">
        Rosticer&iacute;as</td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" value="Generar Diferencias" onClick="validar(1)"{disabled}>
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
    <tr>
    	<td colspan="3" class="tabla" style="font-size: 12pt; font-weight: bold;">{operadora}</td>
    	</tr>
  </table>
  <br>
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
      <th rowspan="2" class="tabla" scope="col">Dif</th>
    </tr>
    <tr>
      <th class="tabla" scope="col">C&oacute;mputo</th>
      <th class="tabla" scope="col">F&iacute;sica</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr id="row{i}" style="background-color:#{bgcolor};">
      <td class="vtabla" style="font-weight: bold; color: #{color_mp};" onMouseOver="this.style.cursor='pointer';" onMouseOut="this.style.cursor='default';" onClick="aux({num_cia},{codmp},{mes},{anio},{id},{i})">{codmp} {nombre} </td>
      <td class="tabla"><input name="existencia[]" type="text" class="rnombre" id="existencia" style="color: #{color_exi};" value="{existencia}" size="10" readonly>{info_distribucion}</td>
      <td class="tabla"><input name="inventario[]" type="text" class="rnombre" id="inventario" onClick="mod({id},{i})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" value="{inventario}" size="10" readonly>{info_tanques}</td>
      <td class="tabla"><input name="falta[]" type="text" class="rnombre" id="falta" style="color: #CC0000;" value="{falta}" size="10" readonly></td>
      <td class="tabla"><input name="sobra[]" type="text" class="rnombre" id="sobra" style="color: #0000CC;" value="{sobra}" size="10" readonly></td>
      <td class="tabla"><input name="costo[]" type="text" class="rnombre" id="costo" value="{costo}" size="5" readonly></td>
      <td colspan="2" class="tabla">{gas_pro}<input name="total[]" type="text" class="rnombre" id="total" style="color: #{color_t}" value="{total}" size="8" readonly></td>
	  <td class="rtabla">{gas_pro_0}{dif0}</td>
	  <td class="rtabla">{gas_pro_1}{dif1}</td>
     <td class="rtabla" style="font-weight:bold;">{dif_bultos}</td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="6" class="tabla">&nbsp;</th>
      <th class="tabla"><input name="contra" type="text" disabled="true" class="rnombre" id="contra" style="color: #CC0000; font-size: 12pt;" value="{contra}" size="10"></th>
      <th class="tabla"><input name="favor" type="text" disabled="true" class="rnombre" id="favor" style="color: #0000CC; font-size: 12pt;" value="{favor}" size="10"></th>
	  <th colspan="3" class="tabla">&nbsp;</th>
	  </tr>
    <tr>
      <th colspan="6" class="rtabla">Gran Total </th>
      <th colspan="2" class="tabla"><input name="gran_total" type="text" disabled="true" class="nombre" id="gran_total" style="width: 100%; font-size: 12pt; color: #{color_gt}" value="{total}" size="10"></th>
	  <th colspan="3" class="tabla">{dif_pro}</th>
	  </tr>
    <tr>
      <td class="tabla" colspan="11">&nbsp;</td>
    </tr>
    <tr>
      <th class="tabla" colspan="11">Observaciones</th>
    </tr>
    <tr>
      <td class="tabla" colspan="11"><textarea name="observaciones" id="observaciones" class="insert" rows="5" wrap="soft" onchange="this.value=this.value.toUpperCase().trim();actualizar_observaciones({num_cia},{anio_obs},{mes_obs})" style="width:100%">{observaciones}</textarea></td>
    </tr>
    <tr>
      <td class="tabla" colspan="11" id="observaciones_extra">{observaciones_extra}</td>
    </tr>
  </table>
  </form>
  <table width="100%">
  <tr>
    <td width="33%">&nbsp;</td>
    <td width="33%" align="center">
	<table class="tabla">
    <tr>
	  <th class="tabla" scope="row">Periodo</th>
	  <th class="tabla">Producci&oacute;n</th>
	  <th class="tabla">Mercancias</th>
	  <th class="tabla">Consumo</th>
	  <th class="tabla">%</th>
	  </tr>
	<!-- START BLOCK : consumo_ant -->
	<tr>
      <th class="vtabla" scope="row">{mes} {anio} </th>
      <td class="rtabla">{pro}</td>
      <td class="rtabla">{mer}</td>
      <td class="rtabla">{consumo}</td>
      <td class="rtabla" style="color:#0000CC;">{con_pro}</td>
	</tr>
	<!-- END BLOCK : consumo_ant -->
  </table>
    <p style="color:#F00;font-size:8pt;">NOTA: Producción y consumo incluye todos los turnos</p>
  <table class="tabla">
    <tr>
    <th colspan="3" class="tabla" scope="row">Tanques de gas</th>
    </tr>
    <tr>
    <th class="tabla" scope="row">#</th>
    <th class="tabla">Nombre</th>
    <th class="tabla">Capacidad</th>
    <th class="tabla">Capacidad al 90%</th>
    </tr>
  <!-- START BLOCK : tanque_gas -->
  <tr>
      <th class="rtabla" scope="row">{num}</th>
      <th class="rtabla">{nombre}</th>
      <td class="rtabla">{capacidad}</td>
      <td class="rtabla">{capacidad_90}</td>
  </tr>
  <!-- END BLOCK : tanque_gas -->
  </table>
</td>
    <td width="33%" align="center">
      <input name="button" type="button" class="boton" onClick="document.location.reload()" value="Recargar">
    </td>
  </tr>
</table>
  <br>
  <form action="./bal_act_inv_v3.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <input name="tipo" type="hidden" id="tipo" value="{tipo}">
    <table class="tabla">
    <tr>
      <td class="tabla"><input type="button" class="boton" value="Cancelar" onClick="document.location='./bal_act_inv_v3.php?accion=2'"></td>
      <td class="tabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia(this,nombre)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{num_cia_next}" size="3">
        <input name="nombre" type="text" disabled="true" class="vnombre" id="nombre" value="{nombre_next}" size="30"></td>
      <td class="tabla"><input type="button" class="boton" value="Siguiente >>" onClick="next()"></td>
      </tr>
  </table>
  </form>
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

function actualizar_observaciones(num_cia, anio, mes)
{
  new Request({
    url: 'bal_act_inv_v3.php',
    data: {
      actualizar_observaciones: 1,
      num_cia: num_cia,
      anio: anio,
      mes: mes,
      obs: document.id('observaciones').get('value')
    },
    onSuccess: function(result) {
    }
  }).send();
}

function actualizar_observaciones_extra(num_cia, anio, mes)
{
  new Request({
    url: 'bal_act_inv_v3.php',
    data: {
      obtener_observaciones_extra: 1,
      num_cia: num_cia,
      anio: anio,
      mes: mes
    },
    onSuccess: function(result) {
      document.id('observaciones_extra').set('html', result != '' ? result : '&nbsp;');
    }
  }).send();
}

$$('img[id=info]').each(function(el) {
  el.store('tip:title', '<img src="/lecaroz/iconos/info.png" /> Lecturas de gas');
  el.store('tip:text', el.get('data-info'));

  el.removeProperty('data-info');
});

tips_info = new Tips($$('img[id=info]'), {
  'fixed': true,
  'className': 'Tip',
  'showDelay': 50,
  'hideDelay': 50
});

actualizar_observaciones_extra({num_cia},{anio_obs},{mes_obs});
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
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_act_inv_v3.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : bloqueo -->
<!-- START BLOCK : bloqueo_capacidad_gas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;color:#CC0000">ADVERTENCIA</p>
  <p style="font-family:Arial, Helvetica, sans-serif;">Las existencias de gas de las siguientes compa&ntilde;&iacute;s son mayores al 90% de las capacidades de los tanques de gas de las mismas</p> <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Capacidad</th>
      <th class="tabla" scope="col">Inventario</th>
    </tr>
    <!-- START BLOCK : cia_gas -->
  <tr>
      <td class="vtabla">{num_cia} {nombre}</td>
      <td class="rtabla">{capacidad}</td>
      <td class="rtabla">{inventario}</td>
    </tr>
  <!-- END BLOCK : cia_gas -->
  </table>  <p>
    <input type="button" class="boton" value="Regresar" onClick="document.location='./bal_act_inv_v3.php'">
  </p></td>
</tr>
</table>
<!-- END BLOCK : bloqueo_capacidad_gas -->
</body>
</html>
