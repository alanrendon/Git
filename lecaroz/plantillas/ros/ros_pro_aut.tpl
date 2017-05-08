<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : cias -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Autom&aacute;tico de Rosticer&iacute;as</p>
  <form action="./ros_pro_aut.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="2" class="tabla" scope="col" style="font-size:14pt;">{num_cia} - {nombre} </th>
      </tr>
    <!-- START BLOCK : dia -->
	<tr>
      <td class="tabla"><input name="opt" type="radio" value="{opt}" onClick="next.disabled=false;tmp.value=this.value"{disabled} /></td>
      <td class="tabla" style="font-size:12pt; font-weight:bold;">{fecha}</td>
    </tr>
	<!-- END BLOCK : dia -->
    <tr>
      <td colspan="2" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
	  <!-- START BLOCK : no_cias -->
    <tr>
      <td colspan="2" class="tabla" style="font-size:14pt; font-weight:bold;">No hay datos por revisar </td>
      </tr>
	  <!-- END BLOCK : no_cias -->
  </table>  <p>
    <input name="next" type="button" class="boton" id="next" onclick="validar()" value="Siguiente >>" />
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	var tmp = f.tmp.value.split('|');
	var url = './ros_pro_aut.php?action=compras&num_cia=' + tmp[0] + '&fecha=' + escape(tmp[1]) + '&dir=r';
	document.location = url;
}
//-->
</script>
<!-- END BLOCK : cias -->
<!-- START BLOCK : compras -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Autom&aacute;tico de Rosticerias</p>
  <p class="title">Compras</p>
  <form action="./ros_pro_aut.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Rosticer&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="num_cia" type="hidden" id="num_cia" value="{fecha}" />
        {num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="fecha" type="hidden" id="fecha" value="{fecha}" />
        {_fecha}</td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Kilos</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Gastos</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Folio</th>
    </tr>
    <!-- START BLOCK : compra -->
	<tr>
      <td class="vtabla" style="font-weight:bold;"><input name="id[]" type="hidden" id="id" value="{id}" />
        {codmp} {nombre} </td>
      <td class="rtabla"><input name="cantidad[]" type="text" class="rnombre" id="cantidad" style="width:100%;" value="{cantidad}" size="4" readonly="true" /></td>
      <td class="rtabla"><input name="kilos[]" type="text" class="rinsert" id="kilos" style="width:100%;" value="{kilos}" size="5" /></td>
      <td class="rtabla"><input name="precio[]" type="text" class="rnombre" id="precio" style="width:100%;" value="{precio}" size="5" readonly="true" /></td>
      <td class="rtabla"><input name="importa[]" type="text" class="rnombre" id="importa" style="width:100%;" value="{importe}" size="8" readonly="true" /></td>
      <td class="tabla"><input name="aplica{i}" type="checkbox" id="aplica{i}" value="{aplica}"{checked} />
        Si</td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" value="{num_pro}" size="3" />
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" readonly="true" /></td>
      <td class="tabla"><input name="folio[]" type="text" class="insert" id="folio" value="{folio}" size="8" readonly="true" /></td>
    </tr>
	<!-- END BLOCK : compra -->
    <tr>
      <th colspan="4" class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" style="font-size:12pt;" value="{total}" size="8" /></th>
      <th colspan="3" class="tabla">&nbsp;</th>
      </tr>
  </table>  <p>
    <input type="button" class="boton" value="Inicio" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente >>" />
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : compras -->
<!-- START BLOCK : ventas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Autom&aacute;tico de Rosticerias</p>
  <p class="title">Ventas</p>
  <form action="./ros_pro_aut.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col">Rosticer&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="num_cia2" type="hidden" id="num_cia2" value="{fecha}" />
      {num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="fecha2" type="hidden" id="fecha2" value="{fecha}" />
      {_fecha}</td>
    </tr>
  </table>  
  <br /><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Producto</th>
    <th class="tabla" scope="col">Existencia</th>
    <th class="tabla" scope="col">Cantidad</th>
    <th class="tabla" scope="col">Precio</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : venta -->
  <tr>
    <td class="vtabla" style="font-weight:bold;"><input name="id[]" type="hidden" id="id" value="{id}" />
      {codmp} {nombre}</td>
    <td class="tabla"><input name="existencia[]" type="text" class="rnombre" id="existencia" value="{existencia}" size="10" /></td>
    <td class="tabla"><input name="cantidad[]" type="text" class="rnombre" id="cantidad" value="{cantidad}" size="10" /></td>
    <td class="tabla"><input name="precio[]" type="text" class="rnombre" id="precio" value="{precio}" size="8" /></td>
    <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" style="width:100%;" value="{importe}" size="10" /></td>
  </tr>
  <!-- END BLOCK : venta -->
  <tr>
    <td colspan="4" class="rtabla" style="font-weight:bold;">Otros</td>
    <td class="tabla"><input name="otros" type="text" class="rinsert" id="otros" style="width:100%;" size="10" /></td>
  </tr>
  <tr>
    <th colspan="4" class="rtabla">Total</th>
    <th class="tabla"><input name="total" type="text" class="rnombre" id="total" style="font-size:12pt; width:100%;" value="{total}" size="10" /></th>
  </tr>
</table>
  <p>
    <input type="button" class="boton" value="Regresar" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" /> 
</p></form></td>
</tr>
</table>
<!-- END BLOCK : ventas -->
<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Autom&aacute;tico de Rosticerias</p>
  <p class="title">Gastos</p>
  <form action="./ros_pro_aut.php" method="post" name="form" id="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Rosticer&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="num_cia" type="hidden" id="num_cia" value="{fecha}" />
      {num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="fecha" type="hidden" id="fecha" value="{fecha}" />
      {_fecha}</td>
    </tr>
  </table>

  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : gasto_row -->
	<tr>
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}" />        <input name="codgastos[]" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaGasto({i})" onkeydown="if ((event.keyCode == 13 || event.keyCode == 38 || event.keyCode == 40) && codgastos.length == undefined) this.blur;
else {
if (event.keyCode == 13 || event.keyCode == 40) codgastos[{next}].select();
else if (event.keyCode == 38) codgastos[{back}].select();
}" value="{codgastos}" size="3" />
        <input name="desc[]" type="text" class="vnombre" id="desc" value="{desc}" size="30" readonly="true" /></td>
      <td class="vtabla" style="font-weight:bold;">{concepto}</td>
      <td class="rtabla" style="font-weight:bold;"><input name="importe[]" type="text" class="rnombre" id="importe" style="width:100%;" value="{importe}" size="10" readonly="true" /></td>
    </tr>
	<!-- END BLOCK : gasto_row -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla"><input name="total" type="text" class="rnombre" id="total" style="width:100%;" value="{total}" size="10" /></th>
    </tr>
    <tr>
      <th colspan="2" class="rtabla">Compras</th>
      <th class="rtabla"><input name="compras" type="text" class="rnombre" id="compras" style="width:100%;" value="{compras}" size="10" /></th>
    </tr>
    <tr>
      <th colspan="2" class="rtabla">Total de Gastos </th>
      <th class="rtabla"><input name="total_gastos" type="text" class="rnombre" id="total_gastos" style="font-size:12pt;" value="{total_gastos}" size="10" /></th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Inicio" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente >>" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array();

<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->

function cambiaGasto(i) {
	var inputGasto = null, nombreGasto = null;
	
	inputGasto = f.codgastos.length == undefined ? f.codgastos : f.codgastos[i];
	nombreGasto = f.desc.length == undefined ? f.desc : f.desc[i];
	
	if (inputGasto.value == "" || inputGasto.value == "0") {
		inputGasto.value = "";
		nombreGasto.value = "";
	}
	else if (gasto[get_val(inputGasto)] != null)
		nombreGasto.value = gasto[get_val(inputGasto)];
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		inputGasto.value = f.tmp.value;
	}
	
	total();
}

function total() {
	var total = 0;
	
	if (f.codgastos.length == undefined)
		total += get_val(f.codgastos) > 0 ? get_val(f.importe) : 0;
	else
		for (var i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) > 0)
				total += get_val(f.importe[i]);
	
	f.total.value = number_format(total, 2);
}

function validar(dir) {
	// Validar que todos los gastos hayan sido codificados
	if (f.codgastos.length == undefined && get_val(f.codgastos) <= 0) {
		alert("Debe códificar todos los gastos");
		f.codgastos.select();
		return false;
	}
	else
		for (var i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) <= 0) {
				alert("Debe códificar todos los gastos");
				f.codgastos[i].select();
				return false;
			}
	
	// Validar el turno en los códigos de mercancia
	if (f.codgastos.length == undefined && get_val(f.codgastos) == 23 && f.turno.selectedIndex == 0) {
		alert("Debe seleccionar el turno para las mercancias");
		f.turno.focus();
		return false;
	}
	else
		for (i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) == 23 && f.turno[i].selectedIndex == 0) {
				alert("Debe seleccionar el turno para las mercancias");
				f.turno[i].focus();
				return false;
			}
	
	f.action = './pan_rev_dat.php?action=gastos_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.codgastos.length == undefined ? f.codgastos.select() : f.codgastos[0].select();
//-->
</script>
<!-- END BLOCK : gastos -->
<!-- START BLOCK : efectivo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Proceso Autom&aacute;tico de Rosticerias</p>
  <form action="./ros_pro_aut.php" method="post" name="form"><table class="tabla">
    <tr>
      <th class="tabla" scope="col">Rosticer&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="num_cia" type="hidden" id="num_cia" value="{fecha}" />
      {num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;"><input name="fecha" type="hidden" id="fecha" value="{fecha}" />
      {_fecha}</td>
    </tr>
  </table>
  <br />
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compras</th>
      <td class="rtabla" scope="col" style="font-size:12pt; font-weight:bold;">{compras}</td>
    </tr>
    <tr>
      <th class="vtabla">Ventas</th>
      <td class="rtabla" style="font-size:12pt; font-weight:bold;">{ventas}</td>
    </tr>
    <tr>
      <th class="vtabla">Abonos</th>
      <td class="rtabla" style="font-size:12pt; font-weight:bold;">{abonos}</td>
    </tr>
    <tr>
      <th class="vtabla">Prestamos</th>
      <td class="rtabla" style="font-size:12pt; font-weight:bold;">{prestamos}</td>
    </tr>
    <tr>
      <th class="vtabla">Gastos</th>
      <td class="rtabla" style="font-size:12pt; font-weight:bold;">{gastos}</td>
    </tr>
    <tr>
      <th class="vtabla">Efectivo</th>
      <td class="rtabla" style="font-size:14pt; font-weight:bold;">{efectivo}</td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Inicio" />
&nbsp;&nbsp;    
<input type="button" class="boton" value="Terminar" />
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : efectivo -->
</body>
</html>
