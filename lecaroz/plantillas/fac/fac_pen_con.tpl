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
<td align="center" valign="middle"><p class="title">Consulta de Facturas Pendientes</p>
  <form action="./fac_pen_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_fact.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="vinsert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13) num_cia.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked="checked" />
        Pendientes<br />
        <input name="tipo" type="radio" value="2" />
        Aclarados<br />
        <input name="tipo" type="radio" value="3" />
        Ultimos Aclarados </td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value == '';
		f.nombre_cia.value == '';
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
		f.num_pro.value == '';
		f.nombre_pro.value == '';
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
<!-- START BLOCK : pendientes -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas Pendientes</p>
  <form action="./fac_pen_con.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
    	<th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Fecha<br />
        Solicitud</th>
      <th class="tabla" scope="col">Observaciones</th>
      <th class="tabla" scope="col">Nueva Factura </th>
    </tr>
    <!-- START BLOCK : pen -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
		<td class="vtabla">{num_cia} {nombre_cia}</td>
      <td class="vtabla"><input name="id[]" type="hidden" id="id" value="{id}" />
        <input name="num_pro[]" type="hidden" id="num_pro" value="{num_pro}" />
        {num_pro} {nombre_pro} </td>
      <td class="rtabla"><input name="num_fact[]" type="hidden" id="num_fact" value="{num_fact}" />
        {num_fact}</td>
      <td class="rtabla">{importe}</td>
      <td class="tabla">{fecha_solicitud}</td>
      <td class="vtabla">{obs}</td>
      <td class="tabla"><input name="num_fact_new[]" type="text" class="insert" id="num_fact_new" onfocus="tmp.value=this.value;this.select()" onkeydown="if (num_fact_new.length != undefined) movCursor(event.keyCode,num_fact_new[{next}],null,null,num_fact_new[{back}],num_fact_new[{next}])" size="10" /></td>
	</tr>
	<!-- END BLOCK : pen -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./fac_pen_con.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Ultimos Aclarados" />
  &nbsp;&nbsp;
  <input type="button" class="boton" value="Aclarar" onclick="validar()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	if (confirm('¿Son correctos los cambios en las facturas?'))
		f.submit();
}

window.onload = f.num_fact_new.length == undefined ? f.num_fact_new.select() : f.num_fact_new[0].select();
//-->
</script>
<!-- END BLOCK : pendientes -->
<!-- START BLOCK : aclarados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturas Aclaradas</p>
  <table class="print">
    <tr>
    	<th class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="print" scope="col">Proveedor</th>
      <th class="print" scope="col">Factura</th>
      <th class="print" scope="col">Importe</th>
      <th class="print" scope="col">Fecha<br />
        Solicitud</th>
      <th class="print" scope="col">Observaciones</th>
      <th class="print" scope="col">Fecha<br />
        Aclarado</th>
      <th class="print" scope="col">Nueva<br />
        Factura</th>
      </tr>
    <!-- START BLOCK : acla -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
		<td class="vprint">{num_cia} {nombre_cia}</td>
      <td class="vprint">{num_pro} {nombre_pro} </td>
      <td class="rprint">{num_fact}</td>
      <td class="rprint">{importe}</td>
      <td class="print">{fecha_solicitud}</td>
      <td class="vprint">{obs}</td>
      <td class="print">{fecha_aclaracion}</td>
      <td class="rprint">{num_fact_nuevo}</td>
      </tr>
	  <!-- END BLOCK : acla -->
  </table>  
  <p>&nbsp;  </p></td>
</tr>
</table>
<!-- END BLOCK : aclarados -->
<!-- START BLOCK : ultimos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Ultimos Aclarados 
  
</p>
  <p>&nbsp;</p></td>
</tr>
</table>
<!-- END BLOCK : ultimos -->
</body>
</html>
