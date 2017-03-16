<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title" style="font-size:12pt;">Detalle de Factura</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Proveedor</th>
      <th class="tabla" scope="col">Factura</th>
      <th class="tabla" scope="col">Fecha</th>
      </tr>
    <tr>
      <td class="tabla">{num_cia} {nombre_cia} </td>
      <td class="tabla">{num_pro} {nombre_pro} </td>
      <td class="tabla">{num_fact}</td>
      <td class="tabla">{fecha}</td>
      </tr>
  </table>  
  <br />
  <!-- START BLOCK : fac_mp -->
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cantidad</th>
      <th class="tabla" scope="col">Producto</th>
      <th class="tabla" scope="col">Contenido</th>
      <th class="tabla" scope="col">Unidad</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Desc.1</th>
      <th class="tabla" scope="col">Desc.2</th>
      <th class="tabla" scope="col">Desc.3</th>
      <th class="tabla" scope="col">I.V.A.</th>
      <th class="tabla" scope="col">I.E.P.S.</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
	<!-- START BLOCK : mp -->
    <tr>
      <td class="rtabla">{cantidad}</td>
      <td class="vtabla">{codmp} {nombre} </td>
      <td class="rtabla">{contenido}</td>
      <td class="vtabla">{unidad}</td>
      <td class="rtabla">{precio}</td>
      <td class="rtabla">{desc1}</td>
      <td class="rtabla">{desc2}</td>
      <td class="rtabla">{desc3}</td>
      <td class="rtabla">{iva}</td>
      <td class="rtabla">{ieps}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : mp -->
    <tr>
      <th colspan="10" class="rtabla">Total</th>
      <th class="rtabla" style="font-size:12pt;">{total}</th>
    </tr>
  </table>
  <!-- END BLOCK : fac_mp -->
  <!-- START BLOCK : fac_gas -->
  <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Litros</th>
    <th class="tabla" scope="col">Precio/Litro</th>
    <th class="tabla" scope="col">I.V.A.</th>
    <th class="tabla" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : tanque -->
  <tr>
    <td class="rtabla">{litros}</td>
    <td class="rtabla">{precio}</td>
    <td class="rtabla">{iva}</td>
    <td class="rtabla">{importe}</td>
  </tr>
  <!-- END BLOCK : tanque -->
  <tr>
    <th colspan="3" class="rtabla">Total</th>
    <th class="rtabla" style="font-size:12pt;">{total}</th>
  </tr>
</table>
  <!-- END BLOCK : fac_gas -->
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
  </p></td>
</tr>
</table>
</body>
</html>
