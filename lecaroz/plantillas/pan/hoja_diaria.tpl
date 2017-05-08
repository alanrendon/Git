<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : hoja -->
<table width="100%" align="center">
  <tr>
    <td style="font-size:16pt; font-weight:bold; font-family:Arial, Helvetica, sans-serif;">{num_cia} {nombre_cia} </td>
    <td align="right" style="font-size:16pt; font-weight:bold; font-family:Arial, Helvetica, sans-serif;">{_fecha}</td>
  </tr>
</table>
<br />
<table width="100%" align="center">
    <tr>
      <td colspan="3" valign="top"><table width="100%" class="print">
        <tr>
          <th class="print_cia">Turno</th>
          <th class="print_cia">Producci&oacute;n</th>
          <th class="print_cia">Importe Raya </th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Frances Noche </td>
          <td class="rprint" style="color:#0000CC;">{pro2}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya2}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Frances D&iacute;a </td>
          <td class="rprint" style="color:#0000CC;">{pro1}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya1}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Bizcocheros</td>
          <td class="rprint" style="color:#0000CC;">{pro3}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya3}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Reposteros</td>
          <td class="rprint" style="color:#0000CC;">{pro4}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya4}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Piconeros</td>
          <td class="rprint" style="color:#0000CC;">{pro8}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya8}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Gelatineros</td>
          <td class="rprint" style="color:#0000CC;">{pro9}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{raya9}&nbsp;</td>
        </tr>
        <tr>
          <th class="print_cia" style="text-align:right;">Suma</th>
          <th class="rprint" style="color:#0000CC; font-size:12pt;">{pro}&nbsp;</th>
          <th class="rprint" style="color:#CC0000; font-size:12pt;">{raya}&nbsp;</th>
        </tr>
      </table></td>
      <td>&nbsp;</td>
      <td rowspan="11" valign="top"><table width="100%" class="print">
        <tr>
          <th class="print_cia" scope="col">Gastos</th>
          <th class="print_cia" scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : gasto_hoja -->
		<tr>
          <td class="vprint">{concepto}</td>
          <td class="rprint">{importe}</td>
        </tr>
		<!-- END BLOCK : gasto_hoja -->
        <tr>
          <th class="print_cia" style="text-align:right;">Total de Gastos </th>
          <th class="rprint" style="font-size:12pt;">{total_gastos}</th>
        </tr>
      </table>
	  <br />
	  <table width="100%" class="print">
        <tr>
          <th colspan="2" class="print_cia" scope="col">Prueba de Pan </th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Sobrante de Ayer </td>
          <td class="rprint">{sobrante_ayer}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Producci&oacute;n</td>
          <td class="rprint">{pro}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Pan Comprado </td>
          <td class="rprint">{pan_comprado}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Total D&iacute;a </td>
          <td class="rprint">{total_dia}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Venta en Puerta </td>
          <td class="rprint">{venta_puerta}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Venta Reparto </td>
          <td class="rprint">{reparto}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Descuento</td>
          <td class="rprint">{desc}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Sobrante para Ma&ntilde;ana</td>
          <td class="rprint">{sobrante_manana}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Pan Contado </td>
          <td class="rprint">{pan_contado}&nbsp;</td>
        </tr>
        <tr>
          <th class="vprint" style="font-weight:bold;">Faltante</th>
          <th class="rprint">{faltante}&nbsp;</th>
        </tr>
      </table>
	  <br />
	  <table width="100%" class="print">
        <tr>
          <th colspan="5" class="print_cia" scope="col">Prestamos a Plazo </th>
        </tr>
        <tr>
          <th class="print">Nombre</th>
          <th class="print">Saldo <br />
          Anterior</th>
          <th class="print">Prestamo</th>
          <th class="print">Abono</th>
          <th class="print">Saldo <br />
          Actual </th>
        </tr>
        <!-- START BLOCK : prestamo -->
		<tr>
          <td class="vprint">{nombre}&nbsp;</td>
          <td class="rprint">{saldo_ant}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{cargo}&nbsp;</td>
          <td class="rprint" style="color:#0000CC;">{abono}&nbsp;</td>
          <td class="rprint">{saldo_act}&nbsp;</td>
        </tr>
		<!-- END BLOCK : prestamo -->
        <tr>
          <th class="vprint">Total</th>
          <th class="vprint" style="font-size:12pt;">{saldo_ant}&nbsp;</th>
          <th class="vprint" style="color:#CC0000; font-size:12pt;">{cargo}</th>
          <th class="vprint" style="color:#0000CC; font-size:12pt;">{abono_obreros}&nbsp;</th>
          <th class="vprint" style="font-size:12pt;">{saldo_act}&nbsp;</th>
        </tr>
        </table></td>
  </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>&nbsp;</td>
  </tr>
    <tr>
      <td valign="top"><table width="100%" class="print">
        <tr>
          <th colspan="3" class="print_cia" scope="col">Rendimiento</th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">F.N.</td>
          <td class="rprint">{bultos2}&nbsp;</td>
          <td class="rprint">{ren2}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">F.D.</td>
          <td class="rprint">{bultos1}&nbsp;</td>
          <td class="rprint">{ren1}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">B.D.</td>
          <td class="rprint">{bultos3}&nbsp;</td>
          <td class="rprint">{ren3}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">REP.</td>
          <td class="rprint">{bultos4}&nbsp;</td>
          <td class="rprint">{ren4}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">PIC.</td>
          <td class="rprint">{bultos8}&nbsp;</td>
          <td class="rprint">{ren8}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">GEL.</td>
          <td class="rprint">{bultos9}&nbsp;</td>
          <td class="rprint">{ren9}&nbsp;</td>
        </tr>
      </table></td>
      <td>&nbsp;</td>
      <td><table width="100%" class="print">
        <tr>
          <th class="print_cia" scope="col">Agua</th>
          <th class="print_cia" scope="col">Medici&oacute;n</th>
          <th class="print" scope="col">Hora</th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Toma 1 </td>
          <td class="rprint">{med1}&nbsp;</td>
          <td class="rprint">{hora1}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Toma 2</td>
          <td class="rprint">{med2}&nbsp;</td>
          <td class="rprint">{hora2}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Toma 3 </td>
          <td class="rprint">{med3}&nbsp;</td>
          <td class="rprint">{hora3}&nbsp;</td>
        </tr>
        <tr>
          <th class="print_cia">Camioneta</th>
          <th class="print_cia">Km</th>
          <th class="print_cia">Dinero</th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Unidad 1 </td>
          <td class="rprint">{km1}&nbsp;</td>
          <td class="rprint">{din1}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Unidad 2 </td>
          <td class="rprint">{km2}&nbsp;</td>
          <td class="rprint">{din2}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Unidad 3 </td>
          <td class="rprint">{km3}&nbsp;</td>
          <td class="rprint">{din3}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Unidad 4 </td>
          <td class="rprint">{km4}&nbsp;</td>
          <td class="rprint">{din4}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Undiad 5 </td>
          <td class="rprint">{km5}&nbsp;</td>
          <td class="rprint">{din5}&nbsp;</td>
        </tr>
      </table></td>
      <td>&nbsp;</td>
  </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
  </tr>
    <tr>
      <td valign="top"><table width="100%" class="print">
        <tr>
          <th class="print_cia" scope="col">Corte</th>
          <th class="print_cia" scope="col">Caja</th>
          <th class="print_cia" scope="col">Clientes</th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">A.M.</td>
          <td class="rprint">{am}&nbsp;</td>
          <td class="rprint">{clientes_am}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Error</td>
          <td class="rprint" style="color:#CC0000;">{error_am}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{error_clientes_am}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">P.M.</td>
          <td class="rprint">{pm}&nbsp;</td>
          <td class="rprint">{clientes_pm}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Error</td>
          <td class="rprint" style="color:#CC0000;">{error_pm}&nbsp;</td>
          <td class="rprint" style="color:#CC0000;">{error_clientes_pm}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Pastel A.M.</td>
          <td class="rprint">{pastel_am}&nbsp;</td>
          <td class="rprint">{clientes_am_pastel}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Pastel P.M.</td>
          <td class="rprint">{pastel_pm}&nbsp;</td>
          <td class="rprint">{clientes_pm_pastel}&nbsp;</td>
        </tr>
        <tr>
          <th class="print_cia">TOTAL C.</th>
          <th class="rprint" style="font-size:12pt;">{total_caja}&nbsp;</th>
          <th class="rprint" style="font-size:12pt;">{total_clientes}&nbsp;</th>
        </tr>
      </table></td>
      <td>&nbsp;</td>
      <td><table width="100%" class="print">
        <tr class="print_cia">
          <th colspan="2" class="print_cia" scope="col">Consecutivo de Corte          </th>
        </tr>
        <tr class="print_cia">
          <th class="print_cia">Pan</th>
          <th class="print_cia">Pastel</th>
        </tr>
        <tr>
          <td class="print">{corte_pan_1}&nbsp;</td>
          <td class="print">{corte_pastel_1}&nbsp;</td>
        </tr>
        <tr>
          <td class="print">{corte_pan_2}&nbsp;</td>
          <td class="print">{corte_pastel_2}&nbsp;</td>
        </tr>
        <tr>
          <td class="print">{corte_pan_3}&nbsp;</td>
          <td class="print">{corte_pastel_3}&nbsp;</td>
        </tr>
        <tr>
          <td class="print">{corte_pan_4}&nbsp;</td>
          <td class="print">{corte_pastel_4}&nbsp;</td>
        </tr>
        <tr>
          <td class="print">{corte_pan_5}&nbsp;</td>
          <td class="print">{corte_pastel_5}&nbsp;</td>
        </tr>
        <tr>
          <td class="print">{corte_pan_6}&nbsp;</td>
          <td class="print">{corte_pastel_6}&nbsp;</td>
        </tr>
      </table></td>
      <td>&nbsp;</td>
  </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td rowspan="3" valign="top"><table width="100%" class="print">
        <tr>
          <th colspan="2" class="print_cia" scope="row">Prueba de Efectivo </th>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Cambio Ayer </td>
          <td class="rprint">{cambio_ayer}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Barredura</td>
          <td class="rprint">{barredura}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Pasteles</td>
          <td class="rprint">{pasteles}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Bases</td>
          <td class="rprint">{bases}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Esquilmos</td>
          <td class="rprint">{esquilmos}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Botes</td>
          <td class="rprint">{botes}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Pastillaje</td>
          <td class="rprint">{pastillaje}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Costales</td>
          <td class="rprint">{costales}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Abono Obreros</td>
          <td class="rprint">{abono_obreros}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Abonos</td>
          <td class="rprint">{abonos}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Puerta</td>
          <td class="rprint">{total_caja}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" scope="row" style="font-weight:bold;">Tiempo aire</td>
          <td class="rprint">{tiempo_aire}&nbsp;</td>
        </tr>
        <tr>
          <th class="print_cia" scope="row">Suma</th>
          <th class="print_cia" style="font-size:12pt; text-align:right;">{suma_prueba1}&nbsp;</th>
        </tr>
      </table></td>
      <td rowspan="3" valign="top">&nbsp;</td>
      <td valign="top"><table width="100%" class="print">
        <tr>
          <th colspan="2" class="print_cia" scope="col">Pastillaje</th>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Existencia</td>
          <td class="rprint">{existencia_inicial}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Venta del d&iacute;a </td>
          <td class="rprint">{venta_pastillaje}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Compras</td>
          <td class="rprint">{compra_pastillaje}&nbsp;</td>
        </tr>
        <tr>
          <th class="vprint">Existencia Final </th>
          <th class="rprint" style="font-size:12pt;">{existencia_final}&nbsp;</th>
        </tr>
      </table></td>
      <td rowspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom"><table width="100%" class="print">
        <tr>
          <td class="vprint" scope="col" style="font-weight:bold;">Efectivo</td>
          <td class="rprint" scope="col">{efectivo}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Gastos</td>
          <td class="rprint">{total_gastos}&nbsp;</td>
        </tr>
        <tr>
          <td class="vprint" style="font-weight:bold;">Raya</td>
          <td class="rprint">{raya}&nbsp;</td>
        </tr>
        <tr>
          <th class="print_cia">Suma</th>
          <th class="rprint" style="font-size:12pt;">{suma_prueba2}&nbsp;</th>
        </tr>
      </table></td>
    </tr>
</table>
<tr>
      <td colspan="3" valign="top">

      <td colspan="3" valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
<table width="100%" class="print">
        <tr>
          <th colspan="3" class="print_cia" scope="col">Avio Recibido el d&iacute;a </th>
        </tr>
        <tr>
          <th class="print_cia" scope="col">Proveedor</th>
          <th class="print_cia" scope="col">Factura</th>
          <th class="print_cia" scope="col">Observaciones</th>
        </tr>
        <!-- START BLOCK : avio_rec -->
		<tr>
          <td class="vprint">{prov}</td>
          <td class="vprint">{fac}</td>
          <td class="vprint">{obs}</td>
		</tr>
		<!-- END BLOCK : avio_rec -->
      </table></td>
      <td valign="top">&nbsp;</td>
    </tr>

    <tr>
<!-- START BLOCK : exp -->
<br style="page-break-after:always;" />
<table width="100%" align="center">
  <tr>
    <td style="font-size:16pt; font-weight:bold; font-family:Arial, Helvetica, sans-serif;">{num_cia} {nombre_cia} </td>
    <td align="right" style="font-size:16pt; font-weight:bold; font-family:Arial, Helvetica, sans-serif;">{_fecha}</td>
  </tr>
</table>
<br />
<table align="center" class="print">
<tr>
  <th colspan="2" class="print" scope="col">Expendio</th>
  <th class="print" scope="col">Rezago<br>
	Anterior</th>
  <th class="print" scope="col">Partidas</th>
  <th class="print" scope="col">%</th>
  <th class="print" scope="col">Total</th>
  <th class="print" scope="col">Abono</th>
  <th class="print" scope="col">Devuelto</th>
  <th class="print" scope="col">Nuevo<br>
  Rezago</th>
  <th class="print" scope="col">Diferencia</th>
  </tr>
<!-- START BLOCK : mov_exp -->
<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
  <td class="rprint" style="font-weight:bold;">{num}</td>
  <td class="vprint" style="font-weight:bold;">{nombre}</td>
  <td class="rprint" style="font-weight:bold;">{rezago_ant}</td>
  <td class="rprint" style="color:#0000CC;">{pan_p_venta}</td>
  <td class="rprint">{por}</td>
  <td class="rprint">{pan_p_exp}</td>
  <td class="rprint" style="color:#990033;">{abono}</td>
  <td class="rprint"{color_dev}>{dev}</td>
  <td class="rprint" style="font-weight:bold;">{rezago}</td>
  <td class="rprint" style="font-weight:bold;">{dif}</td>
  </tr>
<!-- END BLOCK : mov_exp -->
<tr>
  <th colspan="2" class="print">Total</th>
  <th class="rprint" style="font-size:10pt;">{rezago_ant}</th>
  <th class="rprint" style="font-size:10pt;">{pan_p_venta}</th>
  <th class="rprint" style="font-size:10pt;">{por}</th>
  <th class="rprint" style="font-size:10pt;">{pan_p_exp}</th>
  <th class="rprint" style="font-size:12pt;">{abono}</th>
  <th class="rprint" style="font-size:10pt;">{dev}</th>
  <th class="rprint" style="font-size:12pt;">{rezago}</th>
  <th class="rprint" style="font-size:12pt;">&nbsp;</th>
  </tr>
</table>
<!-- END BLOCK : exp -->
{salto}
{salto2}
<!-- END BLOCK : hoja -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert('No hay hojas para imprimir el día especificado');
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
