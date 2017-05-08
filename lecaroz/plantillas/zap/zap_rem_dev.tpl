<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Aplicaci&oacute;n de Devoluciones a Remisiones</p>
  <form action="./zap_rem_dev.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_fact[0].select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Facturas</th>
      <td class="vtabla"><input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[1].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[2].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[3].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[4].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[5].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[6].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[7].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[8].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) num_fact[9].select(); else if (event.keyCode == 40) fecha1.select()" size="8" />
	  <input name="num_fact[]" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13 || event.keyCode == 39) fecha1.select(); else if (event.keyCode == 40) fecha1.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" maxlength="10" /></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : c -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num}] = '{nombre}';
<!-- END BLOCK : p -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañia no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null)
		f.nombre_pro.value = pro[get_val(f.num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : resultado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Aplicaci&oacute;n de Devoluciones a Remisiones</p>
<form action="./zap_rem_dev.php" method="post" name="form"><table class="tabla">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="13" class="vtabla" scope="col">{num_cia} {nombre} </th>
    </tr>
  <tr>
    <th class="tabla">&nbsp;</th>
    <th class="tabla">Factura</th>
    <th class="tabla">Proveedor</th>
    <th class="tabla">Fecha</th>
    <th class="tabla">Importe</th>
    <th class="tabla">Faltantes</th>
    <th class="tabla">Dif. precio </th>
    <th class="tabla">Devoluciones</th>
    <th class="tabla">Descuentos</th>
    <th class="tabla">I.V.A.</th>
    <th class="tabla">Fletes</th>
    <th class="tabla">Otros</th>
    <th class="tabla">Total</th>
    </tr>
  <!-- START BLOCK : pro -->
  <!-- START BLOCK : fac -->
  <tr>
    <td class="tabla"><input name="id{i}" type="checkbox" id="id{i}" value="{id}" checked="checked" />
      <input name="num_pro[]" type="hidden" id="num_pro" value="{num_pro}" />
      <input name="devs[]" type="hidden" id="devs" value="{devs}" />
      <input name="num_fact[]" type="hidden" id="num_fact" value="{num_fact}" />
      <input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
      <input name="dev[]" type="hidden" id="dev" value="{dev}" /></td>
    <td class="tabla">{num_fact}</td>
    <td class="vtabla">{num_pro}-{clave} {nombre} </td>
    <td class="tabla"><input name="fecha[]" type="hidden" id="fecha" value="{fecha}" />
      {fecha}</td>
    <td class="rtabla">{importe}</td>
    <td class="rtabla">{faltantes}</td>
    <td class="rtabla">{dif_precio}</td>
    <td class="rtabla">{dev}</td>
    <td class="rtabla">{desc}
      <input name="desc1[]" type="hidden" id="desc1" value="{desc1}" />
      <input name="desc2[]" type="hidden" id="desc2" value="{desc2}" />
      <input name="desc3[]" type="hidden" id="desc3" value="{desc3}" />
      <input name="desc4[]" type="hidden" id="desc4" value="{desc4}" /></td>
    <td class="rtabla">{iva}
      <input name="iva[]" type="hidden" id="iva" value="{iva}" /></td>
    <td class="rtabla">{fletes}</td>
    <td class="rtabla">{otros}</td>
    <td class="rtabla">{total}
      <input name="total[]" type="hidden" id="total" value="{total}" /></td>
    </tr>
	<!-- END BLOCK : fac -->
  <tr>
    <th colspan="4" class="tabla">&nbsp;</th>
    <th class="rtabla">{importe}</th>
    <th class="rtabla">{faltantes}</th>
    <th class="rtabla">{dif_precio}</th>
    <th class="rtabla">{dev}</th>
    <th class="rtabla">{desc}</th>
    <th class="rtabla">{iva}</th>
    <th class="rtabla">{fletes}</th>
    <th class="rtabla">{otros}</th>
    <th class="rtabla">{total}</th>
    </tr>
  <tr>
    <td colspan="13" class="tabla">&nbsp;</td>
    </tr>
	<!-- END BLOCK : pro -->
	<!-- END BLOCK : cia -->
</table>
<p>
  <input type="button" class="boton" value="Cancelar" onclick="document.location='./zap_rem_dev.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Aceptar" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar() {
	if (confirm('¿Desea aplicar las devoluciones a las facturas seleccionadas?'))
		form.submit();
}
//-->
</script>
<!-- END BLOCK : resultado -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Devoluciones Aplicadas a Remisiones <br />
    al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia_imp -->
  <tr>
    <th colspan="12" class="vprint" scope="col">{num_cia} {nombre}</th>
  </tr>
  <tr>
    <th class="print">Proveedor</th>
    <th class="print">Factura</th>
    <th class="print">Fecha</th>
    <th class="print">Importe</th>
    <th class="print">Faltantes</th>
    <th class="print">Dif. precio </th>
    <th class="print">Devoluciones</th>
    <th class="print">Descuentos</th>
    <th class="print">I.V.A.</th>
    <th class="print">Fletes</th>
    <th class="print">Otros</th>
    <th class="print">Total</th>
  </tr>
  <!-- START BLOCK : pro_imp -->
  <!-- START BLOCK : fac_imp -->
  <tr>
    <td class="vprint">{num_pro}-{clave} {nombre}</td>
    <td class="print">{num_fact}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{importe}</td>
    <td class="rprint">{faltantes}</td>
    <td class="rprint">{dif_precio}</td>
    <td class="rprint">{dev}</td>
    <td class="rprint">{desc}</td>
    <td class="rprint">{iva}</td>
    <td class="rprint">{fletes}</td>
    <td class="rprint">{otros}</td>
    <td class="rprint">{total}</td>
  </tr>
	<!-- END BLOCK : fac_imp -->
  <tr>
    <th colspan="11" class="rprint">Total</th>
    <th class="print">{total}</th>
  </tr>
  <tr>
    <td colspan="12" class="print">&nbsp;</td>
  </tr>
	<!-- END BLOCK : pro_imp -->
	<!-- END BLOCK : cia_imp -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
