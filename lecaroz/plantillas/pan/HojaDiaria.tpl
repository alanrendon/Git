<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hoja de Diario</title>

<style type="text/css">
#Header {
	margin: 20px 10px;
}

#LeftColumnPrimary {
	margin-right: 50%;
}

#RightColumnSecondary {
	width: 50%;
	float: right;
}

#LeftColumnSecondary {
	width: 50%;
	float: left;
}

#RightColumnPrimary {
	margin-left: 50%;
}
</style>
<link href="../../styles/screen.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/screen.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : hoja -->
<div id="Wrapper">
  <div class="font14 bold" id="Header">{num_cia} {nombre_cia}<span style="float:right;">{fecha}</span></div>
  <div id="Body">
    <div id="RightColumnSecondary">
	  <table width="98%" align="center" class="print font10">
        <tr>
          <th class="print font10" scope="col">Gastos</th>
          <th class="print font10" scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : gasto -->
		<tr>
          <td class="print font10">{concepto}</td>
          <td align="right" class="print font10">{importe}</td>
        </tr>
		<!-- END BLOCK : gasto -->
        <tr>
          <th align="right" class="print font12">Total </th>
          <th align="right" class="print font12">{total_gastos}</th>
        </tr>
      </table>
	  <br />
      <table width="98%" align="center" class="print font10">
        <tr>
          <th colspan="2" class="print font10" scope="col">Prueba de Pan </th>
        </tr>
        <tr>
          <td class="print font10">Sobrante de ayer </td>
          <td align="right" class="print font10">{sobrante_ayer}</td>
        </tr>
        <tr>
          <td class="print font10">Producci&oacute;n</td>
          <td align="right" class="print font10">{produccion_total}</td>
        </tr>
        <tr>
          <td class="print font10">Pan comprado </td>
          <td align="right" class="print font10">{pan_comprado}</td>
        </tr>
        <tr>
          <td class="print font10">Total del d&iacute;a </td>
          <td align="right" class="print font10">{total_dia}</td>
        </tr>
        <tr>
          <td class="print font10">Venta en puerta </td>
          <td align="right" class="print font10">{venta_puerta}</td>
        </tr>
        <tr>
          <td class="print font10">Venta reparto </td>
          <td align="right" class="print font10">{reparto}</td>
        </tr>
        <tr>
          <td class="print font10">Descuento</td>
          <td align="right" class="print font10">{desc}</td>
        </tr>
        <tr>
          <td class="print font10">Sobrante para ma&ntilde;ana </td>
          <td align="right" class="print font10">{sobrante_manana}</td>
        </tr>
        <tr>
          <td class="print font10">Pan contado </td>
          <td align="right" class="print font10">{pan_contado}</td>
        </tr>
        <tr>
          <th align="left" class="print font10">Faltante</th>
          <th align="right" class="print font10">{faltante}</th>
        </tr>
      </table>
      <br />
      <table width="98%" align="center" class="print font10">
        <tr>
          <th colspan="5" class="print font10" scope="col">Prestamos a Plazo </th>
        </tr>
        <tr>
          <th class="print font10">Nombre</th>
          <th class="print font10">Saldo<br />
          anterior</th>
          <th class="print font10">Prestamo</th>
          <th class="print font10">Abono</th>
          <th class="print font10">Saldo<br />
          actual</th>
        </tr>
        <!-- START BLOCK : prestamo -->
		<tr>
          <td class="print font10">{nombre}</td>
          <td align="right" class="print font10">{saldo_ant}</td>
          <td align="right" class="print font10 red">{cargo}</td>
          <td align="right" class="print font10 blue">{abono}</td>
          <td align="right" class="print font10">{saldo_act}</td>
        </tr>
		<!-- END BLOCK : prestamo -->
        <tr>
          <th align="left" class="print font12">Total</th>
          <th align="right" class="print font12">{saldo_ant}</th>
          <th align="right" class="print font12 red">{total_cargos}</th>
          <th align="right" class="print font12 blue">{total_abonos}</th>
          <th align="right" class="print font12">{saldo_act}</th>
        </tr>
      </table>
    </div>
    <div id="LeftColumnPrimary">
      <table width="98%" align="center" class="print">
        <tr>
          <th class="print font10" scope="col">Turno</th>
          <th class="print font10" scope="col">Producci&oacute;n</th>
          <th class="print font10" scope="col">Raya</th>
        </tr>
        <tr>
          <td class="print font10">Frances Noche </td>
          <td align="right" class="print font10 blue">{produccion_2}</td>
          <td align="right" class="print font10 red">{raya_2}</td>
        </tr>
        <tr>
          <td class="print font10">Frances D&iacute;a </td>
          <td align="right" class="print font10 blue">{produccion_1}</td>
          <td align="right" class="print font10 red">{raya_1}</td>
        </tr>
        <tr>
          <td class="print font10">Bizcocheros</td>
          <td align="right" class="print font10 blue">{produccion_3}</td>
          <td align="right" class="print font10 red">{raya_3}</td>
        </tr>
        <tr>
          <td class="print font10">Reposteros</td>
          <td align="right" class="print font10 blue">{produccion_4}</td>
          <td align="right" class="print font10 red">{raya_4}</td>
        </tr>
        <tr>
          <td class="print font10">Piconeros</td>
          <td align="right" class="print font10 blue">{produccion_8}</td>
          <td align="right" class="print font10 red">{raya_8}</td>
        </tr>
        <tr>
          <td class="print font10">Gelatineros</td>
          <td align="right" class="print font10 blue">{produccion_9}</td>
          <td align="right" class="print font10 red">{raya_9}</td>
        </tr>
        <tr>
          <th align="right" class="print font12">Total</th>
          <th align="right" class="print font12 blue">{produccion_total}</th>
          <th align="right" class="print font12 red">{raya_total}</th>
        </tr>
      </table>
	  <br />
      <div>
	    <div id="LeftColumnSecondary">
		  <table width="98%" align="center" class="print font10">
            <tr>
              <th colspan="3" class="print font10" scope="col">Rendimientos</th>
            </tr>
            <tr>
              <td class="print font10">F.N.</td>
              <td align="right" class="print font10">{bultos_2}</td>
              <td align="right" class="print font10">{rendimiento_2}</td>
            </tr>
            <tr>
              <td class="print font10">F.D.</td>
              <td align="right" class="print font10">{bultos_1}</td>
              <td align="right" class="print font10">{rendimiento_1}</td>
            </tr>
            <tr>
              <td class="print font10">B.D.</td>
              <td align="right" class="print font10">{bultos_3}</td>
              <td align="right" class="print font10">{rendimiento_3}</td>
            </tr>
            <tr>
              <td class="print font10">REP.</td>
              <td align="right" class="print font10">{bultos_4}</td>
              <td align="right" class="print font10">{rendimiento_4}</td>
            </tr>
            <tr>
              <td class="print font10">PIC.</td>
              <td align="right" class="print font10">{bultos_8}</td>
              <td align="right" class="print font10">{rendimiento_8}</td>
            </tr>
            <tr>
              <td class="print font10">GEL.</td>
              <td align="right" class="print font10">{bultos_9}</td>
              <td align="right" class="print font10">{rendimiento_9}</td>
            </tr>
          </table>
		  <br />
	      <table align="center" class="print font10">
            <tr>
              <th class="print font10" scope="col">Corte</th>
              <th class="print font10" scope="col">Caja</th>
              <th class="print font10" scope="col">Clientes</th>
            </tr>
            <tr>
              <td class="print font10">A.M.</td>
              <td align="right" class="print font10">{am}</td>
              <td align="right" class="print font10">{clientes_am}</td>
            </tr>
            <tr>
              <td class="print font10">Error</td>
              <td align="right" class="print font10 red">{error_am}</td>
              <td align="right" class="print font10 red">{error_clientes_am}</td>
            </tr>
            <tr>
              <td class="print font10">P.M.</td>
              <td align="right" class="print font10">{pm}</td>
              <td align="right" class="print font10">{clientes_pm}</td>
            </tr>
            <tr>
              <td class="print font10">Error</td>
              <td align="right" class="print font10 red">{error_pm}</td>
              <td align="right" class="print font10 red">{error_clientes_pm}</td>
            </tr>
            <tr>
              <td class="print font10">Pastel A.M. </td>
              <td align="right" class="print font10">{pastel_am}</td>
              <td align="right" class="print font10">{clientes_am_pastel}</td>
            </tr>
            <tr>
              <td class="print font10">Pastel P.M. </td>
              <td align="right" class="print font10">{pastel_pm}</td>
              <td align="right" class="print font10">{clientes_pm_pastel}</td>
            </tr>
            <tr>
              <th align="left" class="print font12">Total</th>
              <th align="right" class="print font12">{total_caja}</th>
              <th align="right" class="print font12">{total_clientes}</th>
            </tr>
          </table>
	    </div>
        <div id="RightColumnPrimary">
          <table width="98%" align="center" class="print font10">
            <tr>
              <th class="print font10" scope="col">Agua</th>
              <th class="print font10" scope="col">Medici&oacute;n</th>
              <th class="print font10" scope="col">Hora</th>
            </tr>
            <tr>
              <td class="print font10">Toma 1 </td>
              <td class="print font10">{med1}</td>
              <td class="print font10">{hora1}</td>
            </tr>
            <tr>
              <td class="print font10">Toma 2 </td>
              <td class="print font10">{med2}</td>
              <td class="print font10">{hora2}</td>
            </tr>
            <tr>
              <td class="print font10">Toma 3 </td>
              <td class="print font10">{med3}</td>
              <td class="print font10">{hora3}</td>
            </tr>
            <tr>
              <th class="print font10">Camioneta</th>
              <th class="print font10">KM</th>
              <th class="print font10">Dinero</th>
            </tr>
            <tr>
              <td class="print font10">Unidad 1 </td>
              <td class="print font10">{km1}</td>
              <td class="print font10">{dinero1}</td>
            </tr>
            <tr>
              <td class="print font10">Unidad 2 </td>
              <td class="print font10">{km2}</td>
              <td class="print font10">{dinero2}</td>
            </tr>
            <tr>
              <td class="print font10">Unidad 3 </td>
              <td class="print font10">{km3}</td>
              <td class="print font10">{dinero3}</td>
            </tr>
            <tr>
              <td class="print font10">Unidad 4 </td>
              <td class="print font10">{km4}</td>
              <td class="print font10">{dinero4}</td>
            </tr>
            <tr>
              <td class="print font10">Unidad 5 </td>
              <td class="print font10">{km5}</td>
              <td class="print font10">{dinero5}</td>
            </tr>
          </table>
          <br />
          <table align="center" class="print font10">
            <tr>
              <th colspan="2" class="print font10" scope="col">Consecutivo de Corte </th>
            </tr>
            <tr>
              <th class="print font10">Pan</th>
              <th class="print font10">Pastel</th>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_0}</td>
              <td align="center" class="print font10">{corte_pastel_0}</td>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_1}</td>
              <td align="center" class="print font10">{corte_pastel_1}</td>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_2}</td>
              <td align="center" class="print font10">{corte_pastel_2}</td>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_3}</td>
              <td align="center" class="print font10">{corte_pastel_3}</td>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_4}</td>
              <td align="center" class="print font10">{corte_pastel_4}</td>
            </tr>
            <tr>
              <td align="center" class="print font10">&nbsp;{corte_pan_5}</td>
              <td align="center" class="print font10">{corte_pastel_5}</td>
            </tr>
          </table>
        </div>
        <br />
		<table width="98%" align="center" class="print font10">
          <tr>
            <th colspan="3" class="print font10" scope="col">Av&iacute;o recibido</th>
          </tr>
          <tr>
            <th class="print font10">Proveedor</th>
            <th class="print font10">Factura</th>
            <th class="print font10">Observaciones</th>
          </tr>
          <!-- START BLOCK : factura -->
		  <tr>
            <td class="print font10">{prov}</td>
            <td class="print font10">{fac}</td>
            <td class="print font10">{obs}</td>
          </tr>
		  <!-- END BLOCK : factura -->
        </table>
        <br />
        <div id="RightColumnSecondary">
          <table width="98%" align="center" class="print">
            <tr>
              <th colspan="2" class="print font10" scope="col">Pastillaje</th>
            </tr>
            <tr>
              <td class="print font10">Existencia</td>
              <td align="right" class="print font10">{existencia_inicial}</td>
            </tr>
            <tr>
              <td class="print font10">Venta del d&iacute;a </td>
              <td align="right" class="print font10">{venta_pastillaje}</td>
            </tr>
            <tr>
              <td class="print font10">Compras</td>
              <td align="right" class="print font10">{compra_pastillaje}</td>
            </tr>
            <tr>
              <th align="left" class="print font12">Existencia final </th>
              <th align="right" class="print font12">{existencia_final}</th>
            </tr>
          </table>
          <br />
          <table width="98%" align="center" class="print">
            <tr>
              <td class="print font10">Efectivo</td>
              <td align="right" class="print font10">{efectivo}</td>
            </tr>
            <tr>
              <td class="print font10">Gastos</td>
              <td align="right" class="print font10">{total_gastos}</td>
            </tr>
            <tr>
              <td class="print font10">Raya</td>
              <td align="right" class="print font10">{raya_total}</td>
            </tr>
            <tr>
              <th align="left" class="print font12">Suma</th>
              <th align="right" class="print font12">{suma_prueba2}</th>
            </tr>
          </table>
        </div>
		<div id="LeftColumnPrimary">
		  <table width="98%" align="center" class="print">
            <tr>
              <th colspan="2" class="print font10" scope="col">Prueba de Efectivo </th>
            </tr>
            <tr>
              <td class="print font10">Cambio ayer </td>
              <td align="right" class="print font10">{cambio_ayer}</td>
            </tr>
            <tr>
              <td class="print font10">Barredura</td>
              <td align="right" class="print font10">{barredura}</td>
            </tr>
            <tr>
              <td class="print font10">Pasteles</td>
              <td align="right" class="print font10">{pasteles}</td>
            </tr>
            <tr>
              <td class="print font10">Bases</td>
              <td align="right" class="print font10">{bases}</td>
            </tr>
            <tr>
              <td class="print font10">Esquilmos</td>
              <td align="right" class="print font10">{esquilmos}</td>
            </tr>
            <tr>
              <td class="print font10">Botes</td>
              <td align="right" class="print font10">{botes}</td>
            </tr>
            <tr>
              <td class="print font10">Pastillaje</td>
              <td align="right" class="print font10">{pastillaje}</td>
            </tr>
            <tr>
              <td class="print font10">Costales</td>
              <td align="right" class="print font10">{costales}</td>
            </tr>
            <tr>
              <td class="print font10">Abono obreros </td>
              <td align="right" class="print font10">{total_abonos}</td>
            </tr>
            <tr>
              <td class="print font10">Abonos</td>
              <td align="right" class="print font10">{abonos}</td>
            </tr>
            <tr>
              <td class="print font10">Puerta</td>
              <td align="right" class="print font10">{total_caja}</td>
            </tr>
            <tr>
              <th align="left" class="print font12">Suma</th>
              <th align="right" class="print font12">{suma_prueba1}</th>
            </tr>
          </table>
		</div>
      </div>
    </div>
  </div>
  <!-- START BLOCK : expendios -->
  <div class="font14 bold" id="Header">Expendios<span style="float:right;">{fecha}</span></div>
  <div>
    <table width="98%" align="center" class="print">
        <tr>
          <th class="print font10" scope="col">Expendio</th>
          <th class="print font10" scope="col">Rezago<br />
          Anterior</th>
          <th class="print font10" scope="col">Partidas</th>
          <th class="print font10" scope="col">Devuelto</th>
          <th class="print font10" scope="col">%</th>
          <th class="print font10" scope="col">Total</th>
          <th class="print font10" scope="col">Abono</th>
          <th class="print font10" scope="col">Rezago<br />
          Actual</th>
        </tr>
        <!-- START BLOCK : expendio -->
		<tr>
          <td class="print font10">{num_exp} {nombre_exp} </td>
          <td align="right" class="print font10">{rezago_ant}</td>
          <td align="right" class="print font10 blue">{pan_venta}</td>
          <td align="right" class="print font10">{dev}</td>
          <td align="right" class="print font10">{por}</td>
          <td align="right" class="print font10">{pan_exp}</td>
          <td align="right" class="print font10 red">{abono}</td>
          <td align="right" class="print font10">{rezago}</td>
        </tr>
		<!-- END BLOCK : expendio -->
        <tr>
          <th align="right" class="print font12">Totales</th>
          <th align="right" class="print font12">{rezago_ant}</th>
          <th align="right" class="print font12 blue">{pan_venta}</th>
          <th align="right" class="print font12">{dev}</th>
          <th align="right" class="print font12">&nbsp;</th>
          <th align="right" class="print font12">{pan_exp}</th>
          <th align="right" class="print font12 red">{abono}</th>
          <th align="right" class="print font12">{rezago}</th>
        </tr>
    </table>
  </div>
  <!-- END BLOCK : expendios -->
</div>
<!-- END BLOCK : hoja -->
</body>
</html>
