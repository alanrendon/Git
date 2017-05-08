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
<td align="center" valign="middle"><p class="title">Facturas por Aclarar</p>
  <form action="./fac_fac_acl.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_fact.select()" size="3" />
        <input name="nombre_pro" type="text" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha1.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" maxlength="10" /></td>
    </tr>

    <tr>
      <th class="vtabla" scope="row">Status</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked="checked" />
        Pendientes<br />
        <input name="tipo" type="radio" value="2" />
        Listado de pendientes <br />
        <input name="tipo" type="radio" value="3" />
        Listado de aclarados</td>
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
		alert('La compañía no se encuentra en el catálogo');
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
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : aclarar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas por Aclarar</p>
  <form action="./fac_fac_acl.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Fecha Solicitud </th>
      <th class="tabla" scope="col">Total</th>
      <th class="tabla" scope="col">Observaciones</th>
      <th class="tabla" scope="col">Nueva Factura </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="id[]" type="hidden" id="id[]" value="{id}" />
        <input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
        {num_cia} {nombre_cia} </td>
      <td class="vtabla"><input name="num_pro[]" type="hidden" id="num_pro" value="{num_pro}" />
        {num_pro} {nombre_pro} </td>
      <td class="tabla"><input name="num_fact[]" type="hidden" id="num_fact" value="{num_fact}" />
        {num_fact}</td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{total}</td>
      <td class="vtabla">{obs}</td>
      <td class="tabla"><input name="num_fact_nuevo[]" type="text" class="insert" id="num_fact_nuevo" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13 || event.keyCode == 40) {next}.select(); else if (event.keyCode == 38) {back}.select()" size="8" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./fac_fac_acl.php'" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Actualizar" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}
//-->
</script>
<!-- END BLOCK : aclarar -->
<!-- START BLOCK : pendientes -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Facturas Pendientes de Aclarar<br />
    al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Observaciones</th>
  </tr>
  <!-- START BLOCK : pen -->
  <tr>
    <td class="vprint">{num_cia} {nombvre_cia} </td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="print">{num_fact}</td>
    <td class="print">{fecha}</td>
    <td class="rprint">{total}</td>
    <td class="vprint">{obs}</td>
  </tr>
  <!-- END BLOCK : pen -->
</table>
<!-- END BLOCK : pendientes -->
<!-- START BLOCK : aclarados -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Facturas Aclaradas
    {leyenda}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Fecha Solicitud </th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Observaciones</th>
    <th class="print" scope="col">Nueva Factura </th>
    <th class="print" scope="col">Fecha Aclaraci&oacute;n </th>
  </tr>
  <!-- START BLOCK : acl -->
  <tr>
    <td class="vprint">{num_cia} {nombre_cia} </td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="print">{num_fact}</td>
    <td class="print">{fecha_solicitud}</td>
    <td class="rprint">{total}</td>
    <td class="vprint">{obs}</td>
    <td class="print">{num_fac_nuevo}</td>
    <td class="print">{fecha_aclaracion}</td>
  </tr>
  <!-- END BLOCK : acl -->
</table>
<!-- END BLOCK : aclarados -->
</body>
</html>
