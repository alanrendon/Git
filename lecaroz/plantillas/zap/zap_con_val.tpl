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
<td align="center" valign="middle"><p class="title">Consulta de Validaci&oacute;n de Facturas</p>
  <form action="./zap_con_val.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Ordenar por </th>
      <td class="vtabla"><input name="orden" type="radio" value="1" checked="checked" />
        Compa&ntilde;&iacute;a
          <input name="orden" type="radio" value="2" />
          Proveedor</td>
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
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- END BLOCK : p -->
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
<!-- START BLOCK : listado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Validaci&oacute;n de Facturas</p>
  <table class="tabla">
    <!-- START BLOCK : main -->
	<tr>
      <th colspan="6" class="vtabla" scope="col">{num} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla">{title}</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Factura</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Validada</th>
      <th class="tabla">Copia</th>
    </tr>
    <!-- START BLOCK : row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{num} {nombre} </td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{num_fact}</td>
      <td class="rtabla">{importe}</td>
      <td class="tabla" style="color:#3300CC">{val}</td>
      <td class="tabla" style="color:#990000">{cop}</td>
    </tr>
	<!-- END BLOCK : row -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
      <th colspan="2" class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <td colspan="6" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : main -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./zap_con_val.php'" />
</p></td>
</tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
