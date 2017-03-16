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
<td align="center" valign="middle"><p class="title">Entregas por Proveedor</p>
  <form action="./ped_fac_pro.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_pro.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected></option>
		<!-- START BLOCK : admin -->
        <option value="{id}">{admin}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) codmp.select()" size="3" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="tmp.value=this.value" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fecha1.select()" size="3" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10">
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onChange="actualiza_fecha(this)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{fecha2}" size="10" maxlength="10"></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form;

function validar() {
	if (form.num_pro.value <= 0) {
		alert("Debe especificar el proveedor");
		form.num_pro.select();
		return false;
	}
	else if (form.codmp.value <= 0) {
		alert("Debe especificar el producto");
		form.codmp.select();
		return false;
	}
	else if (form.fecha1.value.length < 8) {
		alert("Debe especificar la fecha inicial");
		form.fecha1.select();
		return false;
	}
	else if (form.fecha2.value.length < 8) {
		alert("Debe especificar la fecha final");
		form.fecha2.select();
		return false;
	}
	else {
		form.submit();
	}
}

window.onload = form.num_cia.select();
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
    <td width="60%" class="print_encabezado" align="center">Entregas del Proveedor<br>
      <span style="font-size: 14pt;">{nombre}</span><br>
      del {fecha1} al {fecha2} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Factura</th>
    <th class="print" scope="col">Cheque</th>
    <th class="print" scope="col">Conciliado</th>
    <th class="print" scope="col">Cantidad</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{fecha}</td>
    <td class="print">{factura}</td>
    <td class="print">{folio}</td>
    <td class="print">{con}</td>
    <td class="rprint">{cantidad}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <th colspan="5" class="rprint">Total</th>
    <th class="rprint_total">{total}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
