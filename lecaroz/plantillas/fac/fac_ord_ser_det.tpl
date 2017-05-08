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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla" id="captura">
<tr>
  <th colspan="2" class="vtabla">Folio</th>
  <td class="vtabla" style="font-weight:bold;">{folio}</td>
  <th colspan="2" class="vtabla">Fecha</th>
  <td class="vtabla">{fecha}</td>
</tr>
<tr>
  <th colspan="2" class="vtabla">Compa&ntilde;&iacute;a</th>
  <td class="vtabla">{num_cia}
	{nombre_cia}</td>
  <th colspan="2" class="vtabla">No. M&aacute;quina </th>
  <td class="vtabla">{maq}</td>
</tr>
<tr>
  <th colspan="2" class="vtabla">Tipo</th>
  <td class="vtabla">{tipo_orden}</td>
  <th colspan="2" class="vtabla">Estatus</th>
  <td class="vtabla">{estatus}</td>
</tr>
<tr>
  <th colspan="2" class="vtabla">Autorizo</th>
  <td colspan="4" class="vtabla">{autorizo}</td>
  </tr>
<tr>
  <th colspan="2" class="vtabla">Orden de Servicio </th>
  <td colspan="4" class="vtabla"><a href="./img_ord_ser.php?folio={folio}&width=965" target="_blank"><img src="./img_ord_ser.php?folio={folio}&width=96"></a></td>
</tr>
<tr>
  <td colspan="6" class="tabla">&nbsp;</td>
  </tr>
<tr>
  <th colspan="6" class="vtabla">Concepto</th>
  </tr>
<tr>
  <td colspan="6" class="vtabla">{concepto}</td>
  </tr>
<tr>
  <td colspan="6" class="tabla">&nbsp;</td>
  </tr>
<tr>
  <th colspan="6" class="vtabla">Observaciones</th>
  </tr>
<tr>
  <td colspan="6" class="vtabla">{observaciones}</td>
  </tr>
<tr>
  <td colspan="6" class="tabla">&nbsp;</td>
  </tr>
<tr>
  <th class="tabla">Scan</th>
  <th class="tabla">	Factura</th>
  <th class="tabla">Proveedor</th>
  <th class="tabla">Fecha</th>
  <th class="tabla">Concepto</th>
  <th class="tabla">Importe</th>
</tr>
<!-- START BLOCK : fac -->
<tr>
  <td class="tabla"><a href="./img_ord_ser.php?num_pro={num_pro}&num_fact={num_fact}&width=965" target="_blank"><img src="./img_ord_ser.php?num_pro={num_pro}&num_fact={num_fact}&width=96"></a></td>
  <td class="rtabla">{num_fact}</td>
  <td class="vtabla">{num_pro} {nombre_pro}</td>
  <td class="tabla">{fecha}</td>
  <td class="tabla">{concepto}</td>
  <td class="rtabla">{importe}</td>
</tr>
<!-- END BLOCK : fac -->
<tr>
  <th colspan="5" class="rtabla">Costo Reparaci&oacute;n </th>
  <th class="rtabla">{total}</th>
</tr>
</table>
<p>
  <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
</p></td>
</tr>
</table>
</body>
</html>
