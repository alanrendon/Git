<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Balances de Panaderias</title>

<link href="/lecaroz/styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/lecaroz/styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />

<script language="JavaScript" type="text/javascript" src="./jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="./jscripts/mootools/extensiones.js"></script>

<script language="JavaScript" type="text/javascript">
<!--
function MarcaCheque(el, num_cia) {
	/*
	@@ Marcar o desmarcar checkbox oculto segun sea el caso
	*/
	$(el).getElement('input').checked = !$(el).getElement('input').checked;

	/*
	@@
	*/
	$(el).setStyle('text-decoration', $(el).getElement('input').checked ? 'line-through' : 'none');

	/*
	@@ Calcular importe de Utilidad Neta de la hoja
	*/
	CalculaUtilidadTotal(num_cia);
}

function CalculaUtilidadTotal(num_cia) {
	/*
	@@ Obtener importe de Utilidad Neta de la hoja
	*/
	var UtilidadNeta = $('ImporteUtilidadNeta' + num_cia).get('text').getVal();

	var GastosNoRelacionados = 0;
	var UtilidadTotal = 0;


	/*
	@@ Sumar todos los gastos marcados como no relacionados
	*/
	$each(document.getElementsByName('g' + num_cia), function(el, i)
	{
		GastosNoRelacionados += el.checked ? el.value.getVal() : 0;
	});

	/*
	@@ Calcular Utilidad Total con la formula 'Utilidad Total = Utilidad Neta + Gastos no relacionados'
	*/
	UtilidadTotal = UtilidadNeta + GastosNoRelacionados;

	$('ImporteGastosNoRelacionados' + num_cia).set('html', GastosNoRelacionados != 0 ? GastosNoRelacionados.numberFormat(2, '.', ',') : '&nbsp;');
	$('ImporteUtilidadTotal' + num_cia).set('html', GastosNoRelacionados != 0 ? UtilidadTotal.numberFormat(2, '.', ',') : '&nbsp;');
	$('ImporteUtilidadTotal' + num_cia).setStyle('color', UtilidadTotal <= 0 ? '#C00' : '#00C');
}

function DesgloseGastos(num_cia, anyo, mes, cod, tipo) {
	var url = 'balance_pan.php';
	var opt = '?c=' + num_cia + '&y=' + anyo + '&m=' + mes + '&g=' + cod + '&t=' + tipo;

	var win = window.open(url + opt, 'DesgloseGastos', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=400');
	win.focus();
}
//-->
</script>
</head>

<body>
<!-- START BLOCK : balance -->
<!-- EMPIEZA BALANCE DE PANADERIA -->
<!-- START BLOCK : hoja1 -->
<!-- EMPIEZA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, PRODUCCION, UTILIDAD, ESTADISTICAS -->
<div id="hoja1" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO -->
  <div class="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font12">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font12">{num_cia}</div>
	<div class="EncabezadoCentral Bold"><span class="Font12 Blue">{nombre}</span><br />
    <span class="Font10 Blue">{nombre_corto}</span><br />Balance del Mes de {mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO -->
  <!-- EMPIEZA SECCION DE VENTAS -->
  <div id="Ventas" class="Seccion BorderBottom">
    <div class="Concepto Bold">+ Venta en Puerta</div>
    <div class="Nivel2 Bold Blue">{venta_puerta}</div>
	<div class="Concepto Bold">+ Pastel Vitrina</div>
    <div class="Nivel2 Bold Blue">{pastel_vitrina}</div>
	<div class="Concepto Bold">+ Pastel Pedido</div>
    <div class="Anexo1">{pastel_kilos}</div>
    <div class="Nivel2 Bold Blue">{pastel_pedido}</div>
	<div class="Concepto Bold">+ Pan Pedido</div>
    <div class="Nivel2 Bold Blue">{pan_pedido}</div>
	<div class="Concepto Bold">= Venta en Puerta Total</div>
    <div class="Anexo1">{vpt_p} {vpt_c}</div>
    <div class="Nivel2 Bold Blue">{venta_puerta_total}</div>
	<div class="Concepto">+ Bases</div>
	<div class="Nivel1 Blue">{bases}</div>
	<div class="Concepto">+ Barredura</div>
	<div class="Nivel1 Blue">{barredura}</div>
	<div class="Concepto">+ Pastillaje</div>
	<div class="Nivel1 Blue">{pastillaje}</div>
	<div class="Concepto">+ Abono Empleados</div>
	<div class="Nivel1 Blue">{abono_emp}</div>
	<div class="Concepto">+ Otros</div>
	<div class="Nivel1 Blue">{otros}</div>
	<div class="Concepto Bold">= Total Otros</div>
	<div class="Nivel2 Bold Blue">{total_otros}</div>
	<div class="Concepto">+ Abono Reparto</div>
	<div class="Anexo1"><span class="Orange">{ar_p}</span> {ar_c}</div>
	<div class="Nivel2 Bold Blue">{abono_reparto}</div>
	<div class="Concepto">- Errores</div>
	<div class="Anexo1"><span class="Orange">{num_err}</span></div>
	<div class="Nivel2 Red">{errores}</div>
	<div class="Concepto Bold Font10">= Ventas Netas </div>
	<div class="Anexo2 Bold">{vn_c}</div>
	<div class="Nivel3 Bold Font10 Blue">{ventas_netas}</div>
  </div>
  <!-- TERMINA SECCION DE VENTAS -->
  <!-- EMPIEZA SECCION DE COSTOS -->
  <div id="costos" class="Seccion BorderBottom">
    <div class="Concepto">+ Inventario Anterior</div>
    <div class="Nivel1 Blue">{inv_ant}</div>
	<div class="Concepto">+ Compras</div>
    <div class="Nivel1">{compras}</div>
	<!-- <div class="Concepto">+ Mercancias, Leche y Vino</div> -->
	<div class="Concepto">+ Mercancias y Vino</div>
    <div class="Nivel1">{mercancias}</div>
	<div class="Concepto">- Inventario Actual</div>
    <div class="Nivel1 Blue">{inv_act}</div>
	<div class="Concepto Bold">= Materia Prima Utilizada</div>
    <div class="Nivel2 Bold Blue">{mat_prima_utilizada}</div>
	<div class="Concepto">+ Mano de Obra</div>
    <div class="Anexo1">{por_mano_obra}</div>
    <div class="Nivel2 Blue">{mano_obra}</div>
	<div class="Concepto">+ Panaderos</div>
    <div class="Anexo1">{por_panaderos}{por_total_mano_obra}</div>
    <div class="Nivel2 Blue">{panaderos}</div>
	<div class="Concepto">+ Gastos de Operaci&oacute;n</div>
    <div class="Nivel2 Blue">{gastos_fab}</div>
	<div class="Concepto Bold Font10">= Costo de Producción</div>
    <div class="Nivel3 Bold Font10 Blue">{costo_produccion}</div>
  </div>
  <!-- TERMINA SECCION DE COSTOS -->
  <!-- EMPIEZA SECCION DE UTLIDAD BRUTA -->
  <div class="Seccion BorderBottom" id="UtilidadBruta">
    <div class="Concepto Bold Font10">Utilidad Bruta</div>
    <div class="Anexo3 Bold">{ub_c}</div>
    <div class="Nivel3 Bold Font10">{utilidad_bruta}</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD BRUTA -->
  <!-- EMPIEZA SECCION DE GASTOS -->
  <div class="Seccion BorderBottom" id="Gastos">
    <div class="Concepto">- Pan Comprado</div>
    <div class="Nivel2 Red">{pan_comprado}</div>
	<div class="Concepto">- Gastos Generales</div>
    <div class="Nivel2 Red">{gastos_generales}</div>
	<div class="Concepto">- Gastos de Caja</div>
    <div class="Nivel2 Red">{gastos_caja}</div>
	<div class="Concepto">- Comisiones Bancarias</div>
    <div class="Nivel2 Red">{comisiones}</div>
	<div class="Concepto">- Reservas</div>
    <div class="Nivel2 Red">{reservas}</div>
	<div class="Concepto">- Pagos Anticipados</div>
    <div class="Nivel2 Red">{pagos_anticipados}</div>
	<div class="Concepto">- Gastos Pagados x Otras Cias.</div>
    <div class="Nivel2 Red">{gastos_otras_cias}</div>
	<div class="Concepto Bold Font10">= Total de Gastos</div>
    <div class="Nivel3 Bold Font10 Red">{total_gastos}</div>
	<div class="Concepto Bold Font10">+ Ingresos Extraordinarios</div>
    <div class="Nivel3 Bold Font10 Blue">{ingresos_ext}</div>
  </div>
  <!-- TERMINA SECCION DE GASTOS -->
  <!-- EMPIEZA SECCION DE UTILIDAD NETA -->
  <div class="Seccion BorderBottom" id="UtilidadNeta">
  	<!-- <div class="Concepto Bold Font10">I.V.A./Venta</div>
    <div class="Nivel3 Bold Font10" id="ImporteIVA{num_cia}">{iva}</div> -->
    <div class="Concepto Bold Font10">Utilidad del Mes</div>
	<div class="Anexo3 Bold">{un_c}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadNeta{num_cia}">{utilidad_neta}</div>
	<div class="Concepto">+ Gastos no relacionados</div>
    <div class="Nivel3 Red" id="ImporteGastosNoRelacionados{num_cia}">&nbsp;</div>
	<div class="Concepto Bold Font10">Utilidad para Comparativo</div>
	<div class="Anexo3 Bold Font8 Red">{errores_bancarios}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadTotal{num_cia}">&nbsp;</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD NETA -->
  <!-- EMPIEZA SECCION DE PROMEDIOS -->
  <div class="Seccion BorderBottom" id="Promedios">
    <div class="ColumnaDerechaNivel1">
	  <div id="Encargado">
	    <!-- <div class="ConceptoTipo3">Inici&oacute;:</div>
		<div class="ValorTipo3">{inicio}</div>
		<div class="ConceptoTipo3">Termin&oacute;:</div>
		<div class="ValorTipo3">{termino}</div> -->
		<div><strong>Inici&oacute;:</strong> {inicio}, <strong>Termin&oacute;:</strong> {termino}</div>
	  </div>
	  <!-- <div id="Reservas" style="margin-top:3mm;"> -->
	  <div id="Reservas">
	    <div class="ConceptoTipo4 Bold">Reserva</div>
		<div class="ValorTipo4 Bold" style="text-align:left">Importe</div>
		<!-- START BLOCK : reserva -->
		<div class="ConceptoTipo4">{reserva}</div>
		<div class="ValorTipo4 TextAlignRight">{importe}</div>
		<!-- END BLOCK : reserva -->
		<div class="ConceptoTipo4 Bold">Total</div>
		<div class="ValorTipo4 Bold TextAlignRight">{total_reservas}</div>
		<!-- <div class="ConceptoTipo4 Bold">Empl. Aseg.</div>
		<div class="ValorTipo4 Bold TextAlignRight">{asegurados}</div>
		<div class="ConceptoTipo4 Bold">Costo por Empl.</div>
		<div class="ValorTipo4 Bold TextAlignRight Purple">{costo_emp}</div> -->
		<div class="ConceptoTipo4 Bold">Costo por Empl. (Aseg.)</div>
		<div class="ValorTipo4 Bold TextAlignRight Purple">{costo_emp} <span class="Orange">({asegurados})</span></div>
	  </div>
	  <!-- <div id="Rendimientos" style="margin-top:3mm;"> -->
	  <div id="Rendimientos">
	    <div class="Bold">Rendimientos por Bulto de Harina</div>
		<!--<div class="ConceptoTipo4">{turno}</div>
		<div class="ValorTipo4 TextAlignRight Orange">{rendimiento}</div>-->
		<table style="border-collapse:collapse;">
			<tr>
				<th>Turno</th>
				<th>Actual</th>
				<th>Anterior</th>
				<th>Dif.</th>
				<th>Total</th>
			</tr>
			<!-- START BLOCK : rendimiento -->
			<tr>
				<td>{turno}</td>
				<td align="right" class="Blue">{rendimiento}</td>
				<td align="right" class="Green">{rendimiento_ant}</td>
				<td align="right">{dif}</td>
				<td align="right">{total}</td>
			</tr>
			<!-- END BLOCK : rendimiento -->
			<tr>
				<th colspan="3" align="right">Total</th>
				<th align="right">{dif_ren}</th>
				<th align="right">{total_ren}</th>
			</tr>
		</table>
	  </div>
	  <!-- <div id="EfectivoDepositado" style="margin-top:3mm;"{efectivodepositadohidden}> -->
	  <div id="EfectivoDepositado"{efectivodepositadohidden}>
	  	<!-- <div class="Bold">Efectivo Depositado</div>
		<div class="ConceptoTipo4 TextAlignCenter Bold">Fijo</div>
		<div class="ValorTipo4 TextAlignCenter Bold">{porcentaje}%</div>
		<div class="ConceptoTipo4 TextAlignCenter">{efe_fijo}</div>
		<div class="ValorTipo4 TextAlignCenter">{efe_2por}</div> -->
	  	<div><strong>Efectivo Depositado:</strong>&nbsp;&nbsp;<strong>Fijo:</strong> {efe_fijo}&nbsp;&nbsp;<strong>{porcentaje}</strong> {efe_2por}</div>
	  </div>
	  <!-- START BLOCK : excedente -->
	  <!-- <div id="ExcedenteEfectivo" style="margin-top:3mm;"> -->
	  <div id="ExcedenteEfectivo">
	    <div class="ConceptoTipo4 Bold">Excedente de Efectivo</div>
		<div class="ConceptoTipo4">{excedente_efectivo}</div>
	  </div>
	  <!-- END BLOCK : excedente -->
	</div>
	<div class="ColumnaIzquierdaNivel1">
	  <div>
	    <div class="ColumnaIzquierdaNivel2">
		  <table style="border-collapse:collapse;empty-cells:show;">
		    <tr>
		      <th>{anyo}</th>
		      <th>%</th>
		      <th>Total</th>
		      <th>Bultos</th>
		      <th style="border-left:solid 1px #000;">{anyo_ant}</th>
		      <th>%</th>
		      <th>Total</th>
		      <th>Bultos</th>
	        </tr>
		    <tr>
			  <td>F.D.</td>
			  <td align="right" class="Blue">{con_pro_1}</td>
			  <td align="right" class="Red">{pro_1}</td>
			  <td align="right" class="Green">{bul_1}</td>
		      <td style="border-left:solid 1px #000;">F.D.</td>
			  <td align="right" class="Blue">{con_pro_ant_1}</td>
			  <td align="right" class="Red">{pro_ant_1}</td>
			  <td align="right" class="Green">{bul_ant_1}</td>
		    </tr>
		    <tr>
		      <td>F.N.</td>
			  <td align="right" class="Blue">{con_pro_2}</td>
			  <td align="right" class="Red">{pro_2}</td>
			  <td align="right" class="Green">{bul_2}</td>
		      <td style="border-left:solid 1px #000;">F.N.</td>
			  <td align="right" class="Blue">{con_pro_ant_2}</td>
			  <td align="right" class="Red">{pro_ant_2}</td>
			  <td align="right" class="Green">{bul_ant_2}</td>
		    </tr>
		    <tr>
		      <td>B.D.</td>
			  <td align="right" class="Blue">{con_pro_3}</td>
			  <td align="right" class="Red">{pro_3}</td>
			  <td align="right" class="Green">{bul_3}</td>
		      <td style="border-left:solid 1px #000;">B.D.</td>
			  <td align="right" class="Blue">{con_pro_ant_3}</td>
			  <td align="right" class="Red">{pro_ant_3}</td>
			  <td align="right" class="Green">{bul_ant_3}</td>
		    </tr>
		    <tr>
		      <td>REP.</td>
			  <td align="right" class="Blue">{con_pro_4}</td>
			  <td align="right" class="Red">{pro_4}</td>
			  <td align="right" class="Green">{bul_4}</td>
		      <td style="border-left:solid 1px #000;">REP.</td>
			  <td align="right" class="Blue">{con_pro_ant_4}</td>
			  <td align="right" class="Red">{pro_ant_4}</td>
			  <td align="right" class="Green">{bul_ant_4}</td>
		    </tr>
		    <tr>
		      <td>PIC.</td>
			  <td align="right" class="Blue">{con_pro_8}</td>
			  <td align="right" class="Red">{pro_8}</td>
			  <td align="right" class="Green">{bul_8}</td>
		      <td style="border-left:solid 1px #000;">PIC.</td>
			  <td align="right" class="Blue">{con_pro_ant_8}</td>
			  <td align="right" class="Red">{pro_ant_8}</td>
			  <td align="right" class="Green">{bul_ant_8}</td>
		    </tr>
		    <tr>
		      <td>GEL.</td>
			  <td align="right" class="Blue">{con_pro_9}</td>
			  <td align="right" class="Red">{pro_9}</td>
			  <td align="right" class="Green">{bul_9}</td>
		      <td style="border-left:solid 1px #000;">GEL.</td>
			  <td align="right" class="Blue">{con_pro_ant_9}</td>
			  <td align="right" class="Red">{pro_ant_9}</td>
			  <td align="right" class="Green">{bul_ant_9}</td>
		    </tr>
		  </table>
		</div>
		<div class="ColumnaIzquierdaNivel2" id="PromediosProduccion">
		  <div class="ConceptoTipo1">M. Prima / Vtas - Pan-comp</div>
		  <div class="ValorTipo1">{mp_vtas}</div>
		  <div class="ConceptoTipo1">Utilidad / Producci&oacute;n</div>
		  <div class="ValorTipo1">{util_pro}</div>
		  <div class="ConceptoTipo1">Utilidad / (Prod. + Pan comp.)</div>
		  <div class="ValorTipo1">{util_pro_pc}</div>
		  <div class="ConceptoTipo1">M. Prima / Producci&oacute;n</div>
		  <div class="ValorTipo1">{mp_pro}</div>
		  <div class="ConceptoTipo1">Gas / Producci&oacute;n&nbsp;&nbsp;&nbsp;{dif_gas}</div>
		  <div class="ValorTipo1">{gas_pro}</div>
		  <div class="Red"><!--Nota: La producci&oacute;n no incluye Gelatinero-->&nbsp;</div>
		</div>
	  </div>
	  <div style="margin-top:8mm;">
	    <div class="ConceptoTipo2 Bold Font9">Producción Total</div>
		<div class="ValorTipo2 Bold Font9">{produccion_total}</div>
		<div class="ConceptoTipo2 Bold Font9">Ganancia</div>
		<div class="ValorTipo2 Bold Font9">{ganancia}</div>
		<div class="ConceptoTipo2 Bold Font9">% de Ganancia</div>
		<div class="ValorTipo2 Bold Font9">{porc_ganancia}</div>
		<div class="ConceptoTipo2 Bold Font9">Faltante de Pan</div>
		<div class="ValorTipo2 Bold Font9" style="width:75mm;">
			<span style="width:40mm; float:left;">
				{por_faltante_pan}{faltante_pan}
			</span>
			{ftg}{fp_vp}
		</div>
		<div class="ConceptoTipo2 Bold Font9">Devoluciones</div>
		<div class="ValorTipo2 Bold Font9">{por_devolucion}{devoluciones}</div>
		<div class="ConceptoTipo2 Bold Font9">Rezago Inicial</div>
		<div class="ValorTipo2 Bold Font9">{rezago_ini}</div>
		<div class="ConceptoTipo2 Bold Font9">Rezago Final</div>
		<div class="ValorTipo2 Bold Font9">{rezago_fin}</div>
		<div class="ConceptoTipo2 Bold Font9">{var} el Rezago</div>
		<div class="ValorTipo2 Bold Font9">{var_rezago}</div>
		<div class="ConceptoTipo2 Bold Font9">{var_anual} Rezago <span style="font-size:8pt;">(Anual)</span> </div>
		<div class="ValorTipo2 Bold Font9">{var_rezago_anual}</div>
		<div class="ConceptoTipo2 Bold Font9">Efectivo</div>
		<div class="ValorTipo2 Bold Font9">{efectivo}</div>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE PROMEDIOS -->
  <!-- EMPIEZA UTILIDAD AÑO ANTERIOR -->
  <div class="Seccion BorderBottom" id="UtilidadAnyoAnterior">
  	<div class="Concepto Bold Font8">Utilidad A&ntilde;o Anterior</div>
    <div class="Nivel3 Bold Font10">{utilidad_neta_ant}</div>
  </div>
  <!-- TERMINA UTILIDAD AÑO ANTERIOR -->
  <!-- EMPIEZA SECCION DE UTILIDAD ANTERIOR ANUAL -->
  <div class="Seccion BorderBottom" id="UtilidadAnteriorAnual">
    <div class="Bold">Utilidades A&ntilde;o {anyo_ant}</div>
	<div class="ColumnaDerecha">
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes10_ant}</div>
		    <div class="MesValor2 Orange">{ing10_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util10_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes7_ant}</div>
		    <div class="MesValor2 Orange">{ing7_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util7_ant}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes11_ant}</div>
		    <div class="MesValor2 Orange">{ing11_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util11_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes8_ant}</div>
		    <div class="MesValor2 Orange">{ing8_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util8_ant}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes12_ant}</div>
		    <div class="MesValor2 Orange">{ing12_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util12_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes9_ant}</div>
		    <div class="MesValor2 Orange">{ing9_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util9_ant}</div>
		  </div>
	    </div>
	  </div>
	</div>
	<div class="ColumnaIzquierda">
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes4_ant}</div>
		    <div class="MesValor2 Orange">{ing4_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util4_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes1_ant}</div>
		    <div class="MesValor2 Orange">{ing1_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util1_ant}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes5_ant}</div>
		    <div class="MesValor2 Orange">{ing5_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util5_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes2_ant}</div>
		    <div class="MesValor2 Orange">{ing2_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util2_ant}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes6_ant}</div>
		    <div class="MesValor2 Orange">{ing6_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util6_ant}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes3_ant}</div>
		    <div class="MesValor2 Orange">{ing3_ant}</div>
		    <div class="MesValor1 Blue">&nbsp;{util3_ant}</div>
		  </div>
	    </div>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD ANTERIOR ANUAL -->
  <!-- EMPIEZA SECCION DE UTILIDAD ACTUAL ANUAL -->
  <div class="Seccion BorderBottom" id="UtilidadActualAnual">
    <div class="Bold Font8">Utilidades A&ntilde;o {anyo_act}</div>
	<div class="ColumnaDerecha">
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes10_act}</div>
		    <div class="MesValor2 Orange">{ing10_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util10_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes7_act}</div>
		    <div class="MesValor2 Orange">{ing7_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util7_act}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes11_act}</div>
		    <div class="MesValor2 Orange">{ing11_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util11_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes8_act}</div>
		    <div class="MesValor2 Orange">{ing8_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util8_act}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes12_act}</div>
		    <div class="MesValor2 Orange">{ing12_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util12_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes9_act}</div>
		    <div class="MesValor2 Orange">{ing9_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util9_act}</div>
		  </div>
	    </div>
	  </div>
	</div>
	<div class="ColumnaIzquierda">
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes4_act}</div>
		    <div class="MesValor2 Orange">{ing4_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util4_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes1_act}</div>
		    <div class="MesValor2 Orange">{ing1_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util1_act}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes5_act}</div>
		    <div class="MesValor2 Orange">{ing5_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util5_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes2_act}</div>
		    <div class="MesValor2 Orange">{ing2_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util2_act}</div>
		  </div>
	    </div>
	  </div>
	  <div>
	    <div class="SubColumnaDerecha">
		  <div>
		    <div class="MesConcepto">{mes6_act}</div>
		    <div class="MesValor2 Orange">{ing6_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util6_act}</div>
		  </div>
	    </div>
	    <div class="SubColumnaIzquierda">
		  <div>
		    <div class="MesConcepto">{mes3_act}</div>
		    <div class="MesValor2 Orange">{ing3_act}</div>
		    <div class="MesValor1 Blue">&nbsp;{util3_act}</div>
		  </div>
	    </div>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD ACTUAL ANUAL -->
  <!-- EMPIEZA SECCION DE ESTADISTICAS -->
  <div class="Seccion" id="Estadisticas" style="border-bottom-style:none;">
    <div class="ColumnaDerecha" style="overflow:hidden;">
	  <div class="Bold TextAlignCenter">Estadísticas A&ntilde;o {anyo_ant}</div>
	  <div>
	    <table align="center" style="border-collapse:collapse;empty-cells:show;">
		  <tr class="RowHeader">
			<td>&nbsp;</td>
			<th style="padding-left:1mm;font-size:7pt;">Venta<br />Puerta</th>
			<th style="padding-left:1mm;font-size:7pt;">%<br />Efe </th>
			<th style="padding-left:1mm;font-size:7pt;">Abono R. </th>
			<th style="padding-left:1mm;font-size:7pt;">Prod</th>
			<th style="padding-left:1mm;font-size:7pt;"><span style="text-decoration:underline;">MP</span><br />Pro</th>
			<th style="padding-left:1mm;font-size:7pt;">Bult</th>
			<th style="padding-left:1mm;font-size:7pt;">Clien</th>
		    <th style="padding-left:1mm;font-size:7pt;">Prom<br />
		      Pan x <br />
		      Cliente </th>
		    <th style="padding-left:1mm;font-size:7pt;">%<br />
		    	Fal.</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_1}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_1}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_1}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_2}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_2}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_2}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_3}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_3}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_3}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_4}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_4}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_4}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_5}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_5}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_5}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_6}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_6}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_6}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_7}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_7}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_7}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_8}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_8}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_8}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_9}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_9}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_9}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_10}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_10}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_10}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_11}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_11}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_11}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_ant_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_ant_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_ant_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_ant_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_ant_12}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_ant_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_ant_12}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_ant_12}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_ant_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{vta_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">&nbsp;</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{abono_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{prod_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">&nbsp;</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{bultos_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{clientes_ant}</td>
		    <td align="right" class="Bold">&nbsp;</td>
		    <td align="right" class="Bold">&nbsp;</td>
		  </tr>
		  </tbody>
		</table>
	  </div>
	</div>
	<div class="ColumnaIzquierda" style="overflow:hidden; ">
	  <div class="Bold TextAlignCenter">Estadísticas A&ntilde;o {anyo_act}</div>
	  <div>
	    <table align="center" style="border-collapse:collapse;empty-cells:show;">
		  <tr class="RowHeader">
			<td>&nbsp;</td>
			<th style="padding-left:1mm;font-size:7pt;">Venta<br />Puerta</th>
			<th style="padding-left:1mm;font-size:7pt;">%<br />Efe</th>
			<th style="padding-left:1mm;font-size:7pt;">Abono R</th>
			<th style="padding-left:1mm;font-size:7pt;">Prod</th>
			<th style="padding-left:1mm;font-size:7pt;"><span style="text-decoration:underline;">MP</span><br />Pro</th>
			<th style="padding-left:1mm;font-size:7pt;">Bult</th>
			<th style="padding-left:1mm;font-size:7pt;">Clien</th>
		    <th style="padding-left:1mm;font-size:7pt;">Prom<br />
		      Pan x <br />
		      Cliente </th>
		    <th style="padding-left:1mm;font-size:7pt;">%<br />
		    	Fal.</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_1}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_1}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_1}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_1}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_2}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_2}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_2}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_2}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_3}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_3}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_3}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_3}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_4}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_4}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_4}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_4}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_5}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_5}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_5}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_5}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_6}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_6}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_6}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_6}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_7}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_7}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_7}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_7}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_8}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_8}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_8}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_8}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_9}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_9}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_9}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_9}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_10}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_10}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_10}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_10}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_11}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_11}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_11}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_11}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{vta_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{por_efe_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{abono_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{prod_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;font-size:7pt;">{mp_pro_12}</td>
			<td align="right" class="Red" style="padding-left:1mm;font-size:7pt;">{bultos_12}</td>
			<td align="right" style="padding-left:1mm;font-size:7pt;">{clientes_12}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{prom_12}</td>
		    <td align="right" style="padding-left:1mm;font-size:7pt;">{por_fal_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{vta}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">&nbsp;</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{abono}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{prod}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">&nbsp;</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{bultos}</td>
			<td align="right" class="Bold" style="padding-left:1mm;font-size:7pt;">{clientes}</td>
		    <td align="right" class="Bold">&nbsp;</td>
		    <td align="right" class="Bold">&nbsp;</td>
		  </tr>
		  </tbody>
		</table>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE ESTADISTICAS -->
  <!-- EMPIEZA SECCION DE PRUEBA DE EFECTIVO -->
  <div class="Seccion Font6">
    PE: {pe}
  </div>
  <!-- TERMINA SECCION DE PRUEBA DE EFECTIVO -->
</div>
<!-- TERMINA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, PRODUCCION, UTILIDAD, ESTADISTICAS -->
<div class="page_break"></div>
<!-- END BLOCK : hoja1 -->
<!-- START BLOCK : hoja2 -->
<!-- EMPIEZA SEGUNDA HOJA DE BALANCE @ RELACIÓN DE GASTOS TOTALES -->
<div id="hoja2" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Relaci&oacute;n de Gastos Totales<br />
	{mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZAFO DE PÁGINA -->
  <!-- START BLOCK : tipo_gasto -->
  <!-- EMPIEZA TABLA DE GASTOS -->
  <div class="Seccion" id="Gastos">
    <table align="center" style="border-collapse:collapse;empty-cells:show;">
	  <tr>
		<th colspan="5" align="left" class="Font12">GASTOS {tipo} {leyenda} </th>
      </tr>
	  <tr class="RowHeader">
		<th style="width:7mm;">&nbsp;</th>
		<th style="width:70mm;">Concepto </th>
		<th style="width:25mm;">{mes1} {anyo1}</th>
		<th style="width:25mm;">{mes2} {anyo2}</th>
		<th style="width:25mm;">{mes3} {anyo3}</th>
	  </tr>
	  <!-- START BLOCK : row_gasto -->
	  <!-- START BLOCK : row_gasto_empty -->
	  <tr class="{RowData}">
		<td colspan="5">&nbsp;</td>
	  </tr>
	  <!-- END BLOCK : row_gasto_empty -->
	  <tr class="{RowData}">
		<td>{cod}</td>
		<td>{desc}</td>
		<td align="right" class="Red" onclick="DesgloseGastos({num_cia},{anyo1},{mes1},{cod}, {tipo})">{importe1}</td>
		<td align="right" class="Green" onclick="DesgloseGastos({num_cia},{anyo2},{mes2},{cod}, {tipo})">{importe2}</td>
		<td align="right" class="Blue" onclick="DesgloseGastos({num_cia},{anyo3},{mes3},{cod}, {tipo})">{importe3}</td>
	  </tr>
	  <!-- END BLOCK : row_gasto -->
	  <!-- START BLOCK : subtotal_gastos -->
	  <tr class="RowHeader">
		<th colspan="2" align="right">Sub Total </th>
		<th align="right">{subtotal1}</th>
		<th align="right">{subtotal2}</th>
		<th align="right">{subtotal3}</th>
	  </tr>
	  <!-- END BLOCK : subtotal_gastos -->
	  <!-- START BLOCK : total_gastos -->
	  <!-- START BLOCK : total_gastos_empty -->
	  <tr>
		<td colspan="5">&nbsp;</td>
	  </tr>
	  <!-- END BLOCK : total_gastos_empty -->
	  <tr class="RowHeader">
		<th colspan="2" align="right" class="Font10">Total</th>
		<th align="right" class="Font10">{total1}</th>
		<th align="right" class="Font10">{total2}</th>
		<th align="right" class="Font10">{total3}</th>
	  </tr>
	  <!-- END BLOCK : total_gastos -->
	</table>
  </div>
  <!-- TEMINA TABLA DE GASTOS -->
  <!-- END BLOCK : tipo_gasto -->
</div>
<!-- TERMINA SEGUNDA HOJA DE BALANCE @ RELACIÓN DE GASTOS TOTALES -->
<div class="page_break"></div>
<!-- END BLOCK : hoja2 -->
<!-- START BLOCK : hoja3 -->
<!-- EMPIEZA TERCERA HOJA DE BALANCE @ COMPARATIVO -->
<div id="hoja3" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Comparativo de Balances <br />
	{mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO DE PÁGINA -->
  <!-- EMPIEZA SECCION DE COMPARATIVO -->
  <div id="Comparativo" class="Seccion">
    <table align="center" style="border-collapse:collapse;empty-cells:show;">
      <tr>
        <th style="width:50mm;">Concepto</th>
        <th style="width:40mm;">{mes1} {anyo1} </th>
        <th style="width:40mm;">{mes2} {anyo2} </th>
        <th style="width:40mm;">{mes3} {anyo3} </th>
      </tr>
      <tr>
        <td class="Bold">+ Venta en Puerta</td>
        <td align="right" class="Bold Blue">{venta_puerta1}</td>
        <td align="right" class="Bold Blue">{venta_puerta2}</td>
        <td align="right" class="Bold Blue">{venta_puerta3}</td>
      </tr>
      <tr>
        <td>+ Bases</td>
        <td align="right" class="Blue">{bases1}</td>
        <td align="right" class="Blue">{bases2}</td>
        <td align="right" class="Blue">{bases3}</td>
      </tr>
      <tr>
        <td>+ Barredura</td>
        <td align="right" class="Blue">{barredura1}</td>
        <td align="right" class="Blue">{barredura2}</td>
        <td align="right" class="Blue">{barredura3}</td>
      </tr>
      <tr>
        <td>+ Pastillaje</td>
        <td align="right" class="Blue">{pastillaje1}</td>
        <td align="right" class="Blue">{pastillaje2}</td>
        <td align="right" class="Blue">{pastillaje3}</td>
      </tr>
      <tr>
        <td>+ Abono Empleados </td>
        <td align="right" class="Blue">{abono_emp1}</td>
        <td align="right" class="Blue">{abono_emp2}</td>
        <td align="right" class="Blue">{abono_emp3}</td>
      </tr>
      <tr>
        <td>+ Otros</td>
        <td align="right" class="Blue">{otros1}</td>
        <td align="right" class="Blue">{otros2}</td>
        <td align="right" class="Blue">{otros3}</td>
      </tr>
      <tr>
        <td class="Bold">=Total Otros </td>
        <td align="right" class="Bold Blue">{total_otros1}</td>
        <td align="right" class="Bold Blue">{total_otros2}</td>
        <td align="right" class="Bold Blue">{total_otros3}</td>
      </tr>
      <tr>
        <td class="Bold Blue">+ Abono Reparto </td>
        <td align="right" class="Bold Blue">{abono_reparto1}</td>
        <td align="right" class="Bold Blue">{abono_reparto2}</td>
        <td align="right" class="Bold Blue">{abono_reparto3}</td>
      </tr>
      <tr>
        <td>- Errores </td>
        <td align="right" class="Red">{errores1}</td>
        <td align="right" class="Red">{errores2}</td>
        <td align="right" class="Red">{errores3}</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">= Ventas Netas </td>
        <td align="right" class="Font10 Bold Blue">{ventas_netas1}</td>
        <td align="right" class="Font10 Bold Blue">{ventas_netas2}</td>
        <td align="right" class="Font10 Bold Blue">{ventas_netas3}</td>
      </tr>
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
        <td>+ Inventario Anterior </td>
        <td align="right" class="Blue">{inv_ant1}</td>
        <td align="right" class="Blue">{inv_ant2}</td>
        <td align="right" class="Blue">{inv_ant3}</td>
      </tr>
      <tr>
        <td>+ Compras </td>
        <td align="right">{compras1}</td>
        <td align="right">{compras2}</td>
        <td align="right">{compras3}</td>
      </tr>
      <tr>
        <!-- <td>+Mercancias, Leche y Vino </td> -->
        <td>+Mercancias y Vino </td>
        <td align="right">{mercancias1}</td>
        <td align="right">{mercancias2}</td>
        <td align="right">{mercancias3}</td>
      </tr>
      <tr>
        <td>- Inventario Actual </td>
        <td align="right" class="Blue">{inv_act1}</td>
        <td align="right" class="Blue">{inv_act2}</td>
        <td align="right" class="Blue">{inv_act3}</td>
      </tr>
      <tr>
        <td class="Bold">= Materia Prima Utilizada </td>
        <td align="right" class="Bold Blue">{mat_prima_utilizada1}</td>
        <td align="right" class="Bold Blue">{mat_prima_utilizada2}</td>
        <td align="right" class="Bold Blue">{mat_prima_utilizada3}</td>
      </tr>
      <tr>
        <td>+ Mano de Obra </td>
        <td align="right" class="Blue">{mano_obra1}</td>
        <td align="right" class="Blue">{mano_obra2}</td>
        <td align="right" class="Blue">{mano_obra3}</td>
      </tr>
      <tr>
        <td>+ Panaderos </td>
        <td align="right" class="Blue">{panaderos1}</td>
        <td align="right" class="Blue">{panaderos2}</td>
        <td align="right" class="Blue">{panaderos3}</td>
      </tr>
      <tr>
        <td>+ Gastos de Fabricaci&oacute;n </td>
        <td align="right" class="Blue">{gastos_fab1}</td>
        <td align="right" class="Blue">{gastos_fab2}</td>
        <td align="right" class="Blue">{gastos_fab3}</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">= Costo de Producci&oacute;n </td>
        <td align="right" class="Font10 Bold Blue">{costo_produccion1}</td>
        <td align="right" class="Font10 Bold Blue">{costo_produccion2}</td>
        <td align="right" class="Font10 Bold Blue">{costo_produccion3}</td>
      </tr>
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">Utilidad Bruta </td>
        <td align="right" class="Font10 Bold">{utilidad_bruta1}</td>
        <td align="right" class="Font10 Bold">{utilidad_bruta2}</td>
        <td align="right" class="Font10 Bold">{utilidad_bruta3}</td>
      </tr>
      <tr>
        <td colspan="4">&nbsp;</td>
      </tr>
      <tr>
        <td>- Pan Comprado </td>
        <td align="right" class="Red">{pan_comprado1}</td>
        <td align="right" class="Red">{pan_comprado2}</td>
        <td align="right" class="Red">{pan_comprado3}</td>
      </tr>
      <tr>
        <td>- Gastos Generales </td>
        <td align="right" class="Red">{gastos_generales1}</td>
        <td align="right" class="Red">{gastos_generales2}</td>
        <td align="right" class="Red">{gastos_generales3}</td>
      </tr>
      <tr>
        <td>- Gastos de Caja </td>
        <td align="right" class="Red">{gastos_caja1}</td>
        <td align="right" class="Red">{gastos_caja2}</td>
        <td align="right" class="Red">{gastos_caja3}</td>
      </tr>
      <tr>
        <td>- Comisiones Bancarias </td>
        <td align="right" class="Red">{comisiones1}</td>
        <td align="right" class="Red">{comisiones2}</td>
        <td align="right" class="Red">{comisiones3}</td>
      </tr>
      <tr>
        <td>- Reservas </td>
        <td align="right" class="Red">{reservas1}</td>
        <td align="right" class="Red">{reservas2}</td>
        <td align="right" class="Red">{reservas3}</td>
      </tr>
      <tr>
        <td>- Pagos Anticipados </td>
        <td align="right" class="Red">{pagos_anticipados1}</td>
        <td align="right" class="Red">{pagos_anticipados2}</td>
        <td align="right" class="Red">{pagos_anticipados3}</td>
      </tr>
      <tr>
        <td>- Gastos Pagados x Otras Cias. </td>
        <td align="right" class="Red">{gastos_otras_cias1}</td>
        <td align="right" class="Red">{gastos_otras_cias2}</td>
        <td align="right" class="Red">{gastos_otras_cias3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">= Total de Gastos </td>
        <td align="right" class="Font10 Bold Red">{total_gastos1}</td>
        <td align="right" class="Font10 Bold Red">{total_gastos2}</td>
        <td align="right" class="Font10 Bold Red">{total_gastos3}</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">+ Ingresos Extraordinarios </td>
        <td align="right" class="Font10 Bold Blue">{ingresos_ext1}</td>
        <td align="right" class="Font10 Bold Blue">{ingresos_ext2}</td>
        <td align="right" class="Font10 Bold Blue">{ingresos_ext3}</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">Utilidad del Mes </td>
        <td align="right" class="Font10 Bold">{utilidad_neta1}</td>
        <td align="right" class="Font10 Bold">{utilidad_neta2}</td>
        <td align="right" class="Font10 Bold">{utilidad_neta3}</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
      </tr>
      <tr>
        <td>M. Prima / Vtas - Pan-comp</td>
        <td align="right">{mp_vtas1}</td>
        <td align="right">{mp_vtas2}</td>
        <td align="right">{mp_vtas3}</td>
      </tr>
      <tr>
        <td>Utilidad / Producci&oacute;n </td>
        <td align="right">{util_pro1}</td>
        <td align="right">{util_pro2}</td>
        <td align="right">{util_pro3}</td>
      </tr>
      <tr>
        <td>M. Prima / Producci&oacute;n </td>
        <td align="right">{mp_pro1}</td>
        <td align="right">{mp_pro2}</td>
        <td align="right">{mp_pro3}</td>
      </tr>
      <tr class="BorderBottom">
        <td>Gas / Producci&oacute;n </td>
        <td align="right">{gas_pro1}</td>
        <td align="right">{gas_pro2}</td>
        <td align="right">{gas_pro3}</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Producci&oacute;n Total </td>
        <td align="right" class="Font10 Bold">{produccion_total1}</td>
        <td align="right" class="Font10 Bold">{produccion_total2}</td>
        <td align="right" class="Font10 Bold">{produccion_total3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Ganancia</td>
        <td align="right" class="Font10 Bold">{ganancia1}</td>
        <td align="right" class="Font10 Bold">{ganancia2}</td>
        <td align="right" class="Font10 Bold">{ganancia3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">% de Ganancia </td>
        <td align="right" class="Font10 Bold">{porc_ganancia1}</td>
        <td align="right" class="Font10 Bold">{porc_ganancia2}</td>
        <td align="right" class="Font10 Bold">{porc_ganancia3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Faltante de Pan </td>
        <td align="right" class="Font10 Bold">{faltante_pan1}</td>
        <td align="right" class="Font10 Bold">{faltante_pan2}</td>
        <td align="right" class="Font10 Bold">{faltante_pan3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Devoluciones</td>
        <td align="right" class="Font10 Bold">{devoluciones1}</td>
        <td align="right" class="Font10 Bold">{devoluciones2}</td>
        <td align="right" class="Font10 Bold">{devoluciones3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Rezago Inicial </td>
        <td align="right" class="Font10 Bold">{rezago_ini1}</td>
        <td align="right" class="Font10 Bold">{rezago_ini2}</td>
        <td align="right" class="Font10 Bold">{rezago_ini3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Rezago Final </td>
        <td align="right" class="Font10 Bold">{rezago_fin1}</td>
        <td align="right" class="Font10 Bold">{rezago_fin2}</td>
        <td align="right" class="Font10 Bold">{rezago_fin3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Variaci&oacute;n de Rezago </td>
        <td align="right" class="Font10 Bold">{var_rezago1}</td>
        <td align="right" class="Font10 Bold">{var_rezago2}</td>
        <td align="right" class="Font10 Bold">{var_rezago3}</td>
      </tr>
	  <tr>
        <td class="Font10 Bold">Variaci&oacute;n de Rezago (Anual) </td>
        <td align="right" class="Font10 Bold">{var_rezago_anual1}</td>
        <td align="right" class="Font10 Bold">{var_rezago_anual2}</td>
        <td align="right" class="Font10 Bold">{var_rezago_anual3}</td>
      </tr>
      <tr>
        <td class="Font10 Bold">Efectivo</td>
        <td align="right" class="Font10 Bold">{efectivo1}</td>
        <td align="right" class="Font10 Bold">{efectivo2}</td>
        <td align="right" class="Font10 Bold">{efectivo3}</td>
      </tr>
    </table>
  </div>
  <!-- TERMINA SECCION DE COMPARATIVO -->
</div>
<!-- TERMINA TERCERA HOJA DE BALANCE @ COMPARATIVO -->
<div class="page_break"></div>
<!-- END BLOCK : hoja3 -->
<!-- START BLOCK : hoja4 -->
<!-- EMPIEZA CUARTA HOJA DE BALANCE @ LISTADO DE CHEQUES -->
<div id="hoja4" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Listado de Cheques<br />
	{mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO DE PÁGINA -->
  <!-- EMPIEZA SECCION CHEQUES -->
  <div class="Seccion">
    <table align="center" class="Border" style="border-collapse:collapse;empty-cells:show;table-layout:fixed;" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'">
      <tr>
        <td class="Border" style="width:7mm;">&nbsp;</td>
        <th class="Border" style="width:35mm;">Gasto</th>
        <th class="Border" style="width:50mm;">Beneficiario</th>
        <th class="Border" style="width:60mm;">Concepto</th>
        <th class="Border" style="width:15mm;">Fecha</th>
        <th class="Border" style="width:10mm;">Cheque</th>
        <th class="Border" style="width:18mm;">Importe</th>
      </tr>
	  <!-- START BLOCK : gasto_cheque -->
      <!-- START BLOCK : row_cheque -->
	  <tr>
        <td valign="middle" class="Border" style="height:10mm;" onclick="MarcaCheque(this,{num_cia})"><input name="g{num_cia}" type="checkbox" class="DisplayNone" id="g{num_cia}" value="{importe_checkbox}" />{cod}</td>
        <td valign="middle" class="Border">{desc}</td>
        <td valign="middle" class="Border">{a_nombre}</td>
        <td valign="middle" class="Border">{facturas}{concepto}</td>
        <td align="center" valign="middle" class="Border">{fecha}</td>
        <td align="center" valign="middle" class="Border">{folio}</td>
        <td align="right" valign="middle" class="Border">{importe}</td>
      </tr>
	  <!-- END BLOCK : row_cheque -->
      <!-- START BLOCK : cheque_subtotal -->
	  <tr>
        <th colspan="6" align="right" valign="middle" class="Border">Subtotal</th>
        <th align="right" valign="middle" class="Border">{subtotal}</th>
      </tr>
	  <!-- END BLOCK : cheque_subtotal -->
      <tr>
        <td colspan="7" valign="middle" class="Border">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : gasto_cheque -->
      <!-- START BLOCK : total_cheques-->
	  <tr>
        <td colspan="6" align="right" valign="middle" class="Border Font10 Bold">Total</td>
        <td align="right" valign="middle" class="Border Font10 Bold">{total}</td>
      </tr>
	  <!-- END BLOCK : total_cheques -->
    </table>
  </div>
  <!-- TERMINA SECCION DE CHEQUES -->
</div>
<!-- TERMINA CUARTA HOJA DE BALANCE @ LISTADO DE CHEQUES -->
<div class="page_break"></div>
<!-- END BLOCK : hoja4 -->
<!-- START BLOCK : hoja5 -->
<!-- EMPIEZA CUARTA HOJA DE BALANCE @ PAGOS ANTICIPADOS -->
<div id="hoja4" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Pagos Anticipados<br />
	{mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO DE PÁGINA -->
  <!-- EMPIEZA SECCION PAGOS -->
  <div class="Seccion">
    <table align="center" style="border-collapse:collapse;empty-cells:show;">
      <tr class="RowHeader">
        <th style="width:20mm;">Inicio</th>
        <th style="width:20mm;">Termino</th>
        <th style="width:80mm;">Concepto</th>
        <th style="width:20mm;">Importe</th>
        <th style="width:20mm;">Acumulado</th>
        <th style="width:20mm;">Meses<br />restantes</th>
      </tr>
      <!-- START BLOCK : row_pago -->
	  <tr>
        <td align="center">{fecha_ini}</td>
        <td align="center">{fecha_fin}</td>
        <td>{concepto}</td>
        <td align="right">{importe}</td>
        <td align="right">{acumulado}</td>
        <td align="right">{meses_restantes}</td>
      </tr>
	  <!-- END BLOCK : row_pago -->
	  <tr class="RowHeader">
        <th colspan="3" align="right" class="Font10">Total</th>
        <th align="right" class="Font10">{total}</th>
        <th colspan="2">&nbsp;</th>
      </tr>
    </table>
  </div>
  <!-- TERMINA SECCION DE PAGOS -->
</div>
<!-- TERMINA CUARTA HOJA DE BALANCE @ PAGOS ANTICIPADOS -->
<div class="page_break"></div>
<!-- END BLOCK : hoja5 -->
<!-- START BLOCK : hoja_blanca -->
<!-- EMPIEZA SECCION HOJA EN BLANCO -->
<div class="hoja_blanca_oficio">
  &nbsp;
</div>
<div class="page_break"></div>
<!-- TERMINA SECCION HOJA EN BLANCO -->
<!-- END BLOCK : hoja_blanca -->
<!-- TERMINA BALANCE DE PANADERIA -->
<!-- END BLOCK : balance -->
</body>
</html>
