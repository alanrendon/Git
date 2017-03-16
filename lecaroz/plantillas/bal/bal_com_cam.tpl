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
<td align="center" valign="middle"><p class="title">Comparativo Anual de Datos de Balance</p>
  <form action="./bal_com_cam.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td colspan="3" class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3">
        <input name="rango" type="radio" value="0" checked>
        Todos
        <input name="rango" type="radio" value="1">
        Panader&iacute;as
        <input name="rango" type="radio" value="2">
        Rosticer&iacute;as</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td colspan="3" class="vtabla"><select name="idadministrador" class="insert" id="idadministrador">
        <option value=""></option>
		<option value="-1" style="font-weight: bold; ">Agrupado por Administrador</option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td colspan="3" class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1" {1}>ENERO</option>
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
      <td colspan="3" class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) this.blur()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Campo</th>
      <td colspan="3" class="vtabla"><select name="campo" class="insert" id="campo" onChange="habilita_ing(this)">
        <option value="venta_puerta">Venta en Puerta</option>
        <option value="bases">Bases</option>
        <option value="barredura">Barredura</option>
        <option value="pastillaje">Pastillaje</option>
        <option value="abono_emp">Abono Empleados</option>
        <option value="otros">Otros</option>
        <option value="total_otros">Total Otros</option>
        <option value="abono_reparto">Abono Reparto</option>
        <option value="errores">Errores</option>
        <option value="ventas_netas" style="font-weight: bold; ">Ventas Netas</option>
        <option value="inv_ant">Inventario Anterior</option>
        <option value="compras">Compras</option>
        <option value="mercancias">Mercancias</option>
        <option value="inv_act">Inventario Actual</option>
        <option value="mat_prima_utilizada">Mat. Prima Utilizada</option>
        <option value="mano_obra">Mano de Obra</option>
        <option value="panaderos">Panaderos</option>
        <option value="gastos_fab">Gastos de Fabricaci&oacute;n</option>
        <option value="costo_produccion" style="font-weight: bold; ">Costo de Produccion</option>
        <option value="utilidad_bruta" style="font-weight: bold; ">Utilidad Bruta</option>
        <option value="pan_comprado">Pan Comprado</option>
        <option value="gastos_generales">Gastos Generales</option>
        <option value="gastos_caja">Gastos por Caja</option>
        <option value="reserva_aguinaldos">Reserva para Aguinaldos</option>
        <option value="gastos_otras_cias">Gastos Pagados por Otras</option>
        <option value="total_gastos" style="font-weight: bold; ">Total de Gastos</option>
        <option value="ingresos_ext" style="font-weight: bold; ">Ingresos Extraordinarios</option>
        <option value="utilidad_neta" style="font-weight: bold; ">Utilidad del Mes</option>
        <option value="produccion_total" style="font-weight: bold; ">Producci&oacute;n Total</option>
        <option value="faltante_pan" style="font-weight: bold; ">Faltante de Pan</option>
        <option value="rezago_ini" style="font-weight: bold; ">Rezago Inicial</option>
        <option value="rezago_fin" style="font-weight: bold; ">Rezago Final</option>
        <option value="efectivo" style="font-weight: bold; ">Efectivo</option>
    		<option value="utilidad_bruta_pro" style="font-weight: bold; ">Utilidad Bruta / Producci&oacute;n</option>
    		<option value="utilidad_pro" style="font-weight: bold; ">Utilidad Neta / Producci&oacute;n</option>
    		<option value="mp_pro" style="font-weight: bold; ">Materia prima / Producci&oacute;n</option>
        <option value="clientes" style="font-weight: bold; ">Clientes</option>
        <option style="font-weight: bold;" disabled="disabled"></option>
        <option style="font-weight: bold; text-decoration:underline; font-weight:bold; color:#C00;" disabled="disabled">POLLOS</option>
        <option value="utilidad_ventas" style="">Utilidad / Ventas</option>
        <option value="utilidad_mat_prima" style="">Utilidad / Materia prima</option>
        <option value="mat_prima_ventas" style="">Materia prima / Ventas</option>
        <option value="pollos" style="">Pollos</option>
        <option value="pescuezos" style="">Pescuezos</option>
        <option value="precio_pollo" style="">Precio por kilo</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Ingresos Ext. </th>
      <td class="vtabla"><input name="ing" type="checkbox" disabled="true" id="ing" value="ing" checked>
        Si</td>
      <th class="vtabla">Errores</th>
      <td class="vtabla"><input name="error" type="checkbox" id="error" value="1" checked>
        Restar</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cantidades</th>
      <td colspan="3" class="vtabla"><input name="mod" type="button" disabled="true" class="boton" id="mod" onClick="modDesc()" value="Modificar">
        <input name="desc" type="checkbox" disabled="true" id="desc" value="1">
        Aplicar</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Promedios</th>
      <td colspan="3" class="vtabla"><input name="prom" type="checkbox" id="prom" value="1">
        Si</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function habilita_ing(opcion) {
	if (opcion.options[opcion.selectedIndex].value == "utilidad_neta") {
		opcion.form.ing.disabled = false;
		opcion.form.mod.disabled = false;
		opcion.form.desc.disabled = false;
	}
	else {
		opcion.form.ing.disabled = true;
		opcion.form.mod.disabled = true;
		opcion.form.desc.disabled = true;
	}
	
	if (opcion.options[opcion.selectedIndex].value == "venta_puerta")
		opcion.form.error.disabled = false;
	else
		opcion.form.error.disabled = true;
}

function modDesc() {
	var win = window.open("./bal_can_des.php","","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	win.focus();
}

function validar(form) {
	if (form.anio.value.length < 4) {
		alert("Debe especificar el año");
		form.anio.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = document.form.num_cia.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comparativo de <span style="font-size: 12pt;">{campo}</span> del {anio}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <!-- START BLOCK : title_mes -->
	<th class="print" scope="col">{mes}</th>
	<!-- END BLOCK : title_mes -->
  </tr>
  <!-- START BLOCK : cia -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="vprint" scope="row"> {num_cia} {nombre_cia} </th>
    <!-- START BLOCK : mes -->
	<td class="rprint">{dato}</td>
	<!-- END BLOCK : mes -->
  </tr>
  <!-- START BLOCK : totales -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint" scope="row" colspan="{colspan}">Totales</th>
    <!-- START BLOCK : total -->
	<th class="rprint" style="color:#000000;">{total}</th>
	<!-- END BLOCK : total -->
  </tr>
  <!-- END BLOCK : totales -->
  <!-- END BLOCK : cia -->
</table>
{salto}
<!-- END BLOCK : listado -->
<!-- START BLOCK : promedios -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Comparativo de <span style="font-size: 12pt;">{campo}</span> Promedio del {anio}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="80%" align="center" class="print">
  <tr>
    <th rowspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
	<th colspan="2" class="print" scope="col">{mes}</th>
    <th rowspan="2" class="print" scope="col">Diferencia</th>
    <th rowspan="2" class="print" scope="col">% Incremento </th>
  </tr>
  <tr>
    <th class="print" scope="col">{anio_ant}</th>
    <th class="print" scope="col">{anio}</th>
  </tr>
  <!-- START BLOCK : cia_prom -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint" scope="row" style="color: #{color};"> {num_cia} {nombre_cia} </td>
	<td class="rprint" style="color: #{color};">{prom_ant}</td>
	<td class="rprint" style="color: #{color};">{prom_act}</td>
    <td class="rprint" style="color: #{color};">{dif}</td>
    <td class="rprint" style="color: #{color};">{inc}</td>
  </tr>
  <!-- START BLOCK : totales_prom -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <th class="rprint">Totales</th>
	<th class="rprint" style="color:#000000;">{total_ant}</th>
	<th class="rprint" style="color:#000000;">{total_act}</th>
    <th class="rprint" style="color:#000000;">{total_dif}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
  <!-- END BLOCK : totales_prom -->
  <!-- END BLOCK : cia_prom -->
</table>
{salto}
<!-- END BLOCK : promedios -->
</body>
</html>
