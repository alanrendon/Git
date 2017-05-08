<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Facturas</p>
  <form action="./fac_fac_can_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_fact.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.replace(/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]/g,'');this.value=this.value.toUpperCase();" onkeydown="if (event.keyCode == 13) num_pro.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descontar de Avio </th>
      <td class="vtabla"><input name="desc" type="checkbox" id="desc" value="1" checked="checked" />
        Si</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" /> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.num_pro) <= 0) {
		alert("Debe especificar el número de proveedor");
		f.num_pro.select();
		return false;
	}
	else if (f.num_fact.value == '') {
		alert("Debe especificar el número de factura");
		f.num_fact.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_pro.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cancelaci&oacute;n de Facturas</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Total</th>
    </tr>
    <tr>
      <td class="tabla" style="font-weight:bold; font-size:12pt;">{num_cia} {nombre_cia} </td>
      <td class="tabla" style="font-weight:bold; font-size:12pt;">{num_pro} {nombre_pro} </td>
      <td class="tabla" style="font-weight:bold; font-size:12pt;">{num_fact}</td>
      <td class="tabla" style="font-weight:bold; font-size:12pt;">{fecha}</td>
      <td class="tabla" style="font-weight:bold; font-size:12pt;">{total}</td>
    </tr>
  </table>
  <!-- START BLOCK : pagado -->
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Banco</th>
      <th class="tabla" scope="col">folio</th>
    </tr>
    <tr>
      <td class="tabla"><strong>{fecha}</strong></td>
      <td class="tabla"><strong>{banco}</strong></td>
      <td class="tabla"><strong>{folio}</strong></td>
    </tr>
  </table>
  <!-- END BLOCK : pagado -->
  <!-- START BLOCK : productos -->
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Contenido</th>
      <th class="tabla" scope="col">Presentaci&oacute;n</th>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Desc1</th>
      <th class="tabla" scope="col">Desc2</th>
      <th class="tabla" scope="col">Desc3</th>
      <th class="tabla" scope="col">I.V.A.</th>
      <th class="tabla" scope="col">I.E.P.S.</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : pro -->
	<tr>
      <td class="vtabla">{codmp} {nombre} </td>
      <td class="rtabla">{contenido}</td>
      <td class="vtabla">{unidad}</td>
      <td class="rtabla">{cantidad}</td>
      <td class="rtabla">{precio}</td>
      <td class="rtabla">{desc1}</td>
      <td class="rtabla">{desc2}</td>
      <td class="rtabla">{desc3}</td>
      <td class="rtabla">{iva}</td>
      <td class="rtabla">{ieps}</td>
      <td class="rtabla">{importe}</td>
      </tr>
	<!-- END BLOCK : pro -->
    <tr>
      <th colspan="10" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
      </tr>
  </table>
  <!-- END BLOCK : productos -->
  <!-- START BLOCK : tanques -->
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Tanque</th>
      <th class="tabla" scope="col">Capacidad</th>
      <th class="tabla" scope="col">Precio/Litro</th>
      <th class="tabla" scope="col">I.V.A.</th>
      <th class="tabla" scope="col">Litros</th>
      <th class="tabla" scope="col">%Inicial</th>
      <th class="tabla" scope="col">%Final</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
	<!-- START BLOCK : tanque -->
    <tr>
      <td class="tabla">{num_tanque}</td>
      <td class="rtabla">{capacidad}</td>
      <td class="rtabla">{precio}</td>
      <td class="rtabla">{iva}</td>
      <td class="rtabla">{litros}</td>
      <td class="rtabla">{porc_ini}</td>
      <td class="rtabla">{porc_fin}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : tanque -->
    <tr>
      <th colspan="7" class="rtabla">Total</th>
      <th class="rtabla">{total}</th>
    </tr>
  </table>
  <!-- END BLOCK : tanques -->
  <form action="./fac_fac_can_v2.php" method="post" name="form"><p>
    <input name="num_pro" type="hidden" id="num_pro" value="{num_pro}" />
    <input name="num_fact" type="hidden" id="num_fact" value="{num_fact}" />
    <input name="status" type="hidden" id="status" value="{status}" />
    <input name="tipo" type="hidden" id="tipo" value="{tipo}" />
	{desc}
    <input type="button" class="boton" value="Regresar" onclick="document.location='./fac_fac_can_v2.php'" /> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Cancelar Factura" onclick="validar()" /> 
</p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.status) > 0) {
		alert("La factura ha sido pagada, asi que no es posible cancelarla");
		return false;
	}
	else if (confirm("¿Desea cancelar la factura?"))
		f.submit();
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
