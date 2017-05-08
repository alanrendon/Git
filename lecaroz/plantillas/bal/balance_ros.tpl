<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Balances de Rosticerias</title>

<link href="../../styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />

<script language="javascript" type="application/javascript">
<!--
function DesgloseGastos(num_cia, anyo, mes, cod, tipo) {
	var url = 'balance_ros.php';
	var opt = '?c=' + num_cia + '&y=' + anyo + '&m=' + mes + '&g=' + cod + '&t=' + tipo;

	var win = window.open(url + opt, 'DesgloseGastos', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1024,height=400');
	win.focus();
}
//-->
</script>
</head>

<body>
<!-- START BLOCK : balance -->
<!-- EMPIEZA BALANCE DE ROSTICERIA -->
<!-- START BLOCK : hoja1 -->
<!-- EMPIEZA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, UTILIDAD, ESTADISTICAS -->
<div id="hoja1" class="hoja_carta">
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
    <div class="Concepto Bold">+ Venta</div>
    <div class="Anexo1">{v_p} {v_c}</div>
    <div class="Nivel2 Bold Blue">{venta}</div>
	<div class="Concepto Bold">+ Otros</div>
	<div class="Nivel2 Bold Blue">{otros}</div>
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
	<div class="Concepto">+ Mercancias</div>
    <div class="Nivel1">{mercancias}</div>
	<div class="Concepto">- Inventario Actual</div>
    <div class="Nivel1 Blue">{inv_act}</div>
	<div class="Concepto Bold">= Materia Prima Utilizada</div>
    <div class="Nivel2 Bold Blue">{mat_prima_utilizada}</div>
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
    <div class="Concepto">- Gastos Generales</div>
    <div class="Nivel2 Red">{gastos_generales}</div>
	<div class="Concepto">- Gastos de Caja</div>
    <div class="Nivel2 Red">{gastos_caja}</div>
	<div class="Concepto">- Comisiones Bancarias</div>
    <div class="Nivel2 Red">{comisiones}</div>
	<div class="Concepto">- Reservas</div>
    <div class="Nivel2 Red">{reservas}</div>
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
    <div class="Concepto Bold Font10">I.V.A./Ventas</div>
    <div class="Nivel3 Bold Font10" id="ImporteIVA{num_cia}">{iva}</div>
    <div class="Concepto Bold Font10">Utilidad del Mes</div>
	<div class="Anexo3 Bold">{un_c}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadNeta{num_cia}">{utilidad_neta}</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD NETA -->
  <!-- EMPIEZA SECCION DE VARIACION DE PIEZAS -->
  <div class="Seccion BorderBottom" id="UtilidadNeta">
    <div class="Concepto Bold Font10">Piezas</div>
	<div class="Anexo3 Bold Font12">{un_p}</div>
    <div class="Nivel3 Bold Font14" id="ImporteUtilidadNeta{num_cia}">{piezas}</div>
  </div>
  <!-- TERMINA SECCION DE VARIACION DE PIEZAS -->
  <!-- EMPIEZA SECCION DE PROMEDIOS -->
  <div class="Seccion BorderBottom" id="Promedios">
    <div class="ColumnaDerecha">
      <!-- START BLOCK : reservas -->
	  <div id="Reservas" style="margin-bottom:3mm;"{reservasdisplaynone}>
	    <div class="ConceptoTipo4 Bold">Reserva</div>
		<div class="ValorTipo4 Bold" style="text-align:left">Importe</div>
		<!-- START BLOCK : reserva -->
		<div class="ConceptoTipo4">{reserva}</div>
		<div class="ValorTipo4 TextAlignRight">{importe}</div>
		<!-- END BLOCK : reserva -->
		<div class="ConceptoTipo4 Bold">Total</div>
		<div class="ValorTipo4 Bold TextAlignRight">{total_reservas}</div>
		<div class="ConceptoTipo4 Bold">Empl. Aseg.</div>
		<div class="ValorTipo4 Bold TextAlignRight">{asegurados}</div>
	  </div>
	  <!-- END BLOCK : reservas -->
      <div id="EfectivoDepositado"{efectivodepositadohidden}>
	  	<div class="Bold">Efectivo Depositado</div>
		<div class="ConceptoTipo4 TextAlignCenter Bold">Fijo</div>
		<div class="ValorTipo4 TextAlignCenter Bold">{porcentaje}</div>
		<div class="ConceptoTipo4 TextAlignCenter">{efe_fijo}</div>
		<div class="ValorTipo4 TextAlignCenter">{efe_2por}</div>
	  </div>
    </div>
    <div class="ColumnaIzquierda">
      <div class="ConceptoTipo1">
        Efectivo
      </div>
      <div class="ValorTipo1">
        {efectivo}
      </div>
      <div class="ConceptoTipo1">
        MP / Ventas
      </div>
      <div class="ValorTipo1">
        {mp_vtas}
      </div>
      <div class="ConceptoTipo1">
        Pollos Vendidos
      </div>
      <div class="ValorTipo1">
        {pollos_vendidos}
      </div>
	  <div class="ConceptoTipo1">
        Pescuezos
      </div>
      <div class="ValorTipo1">
        {pescuezos_vendidos}
      </div>
      <div class="ConceptoTipo1">
        Piernas de Pavo
      </div>
      <div class="ValorTipo1">
        {p_pavo}
      </div>
      <div class="ConceptoTipo1">
        Peso Promedio Normal (Kg)
      </div>
      <div class="ValorTipo1">
        {peso_normal}
      </div>
      <div class="ConceptoTipo1">
        Peso Promedio Chico (Kg)
      </div>
      <div class="ValorTipo1">
        {peso_chico}
      </div>
      <div class="ConceptoTipo1">
        Peso Promedio Grande (Kg)
      </div>
      <div class="ValorTipo1">
        {peso_grande}
      </div>
	  <!-- START BLOCK : excedente_efectivo -->
      <div class="ConceptoTipo1">Exedente Efectivo</div>
	  <div class="ValorTipo1">{excedente_efectivo}</div>
	  <!-- END BLOCK : excedente_efectivo -->
    </div>
  </div>
  <!-- TERMINA SECCION DE PROMEDIOS -->
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
    <div class="Bold">Utilidades A&ntilde;o {anyo_act}</div>
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
			<th style="padding-left:1mm;">Ventas<br />Netas</th>
			<th style="padding-left:1mm;">Pollos</th>
		    <th style="padding-left:1mm;">Precio<br />x Kilo</th>
		    <th style="padding-left:1mm;">Pescue.</th>
		    <th style="padding-left:1mm;">Piernas</th>
		    <th style="padding-left:1mm;">MP/<br />
		    	Ventas</th>
		    <th style="padding-left:1mm;">Util/<br />
		      Ventas</th>
		    <th style="padding-left:1mm;">Util/<br />
	        MP</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_1}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_1}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_1}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_1}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_1}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_1}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_2}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_2}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_2}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_2}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_2}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_2}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_3}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_3}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_3}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_3}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_3}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_3}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_4}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_4}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_4}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_4}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_4}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_4}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_5}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_5}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_5}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_5}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_5}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_5}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_6}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_6}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_6}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_6}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_6}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_6}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_7}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_7}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_7}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_7}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_7}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_7}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_8}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_8}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_8}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_8}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_8}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_8}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_9}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_9}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_9}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_9}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_9}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_9}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_10}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_10}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_10}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_10}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_10}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_10}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_11}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_11}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_11}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_11}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_11}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_11}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" style="padding-left:1mm;">{vta_ant_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_ant_12}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_ant_12}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_ant_12}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_ant_12}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_ant_12}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_ant_12}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_ant_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{vta_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{pollos_ant}</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{precio_ant}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">{pescuezos_ant}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">{piernas_ant}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
		  </tr>
		  </tbody>
		</table>
	  </div>
	</div>
	<div class="ColumnaIzquierda" style="overflow:hidden;">
	  <div class="Bold TextAlignCenter">Estadísticas A&ntilde;o {anyo_act}</div>
	  <div>
	    <table align="center" style="border-collapse:collapse;empty-cells:show;">
		  <tr class="RowHeader">
			<td>&nbsp;</td>
			<th style="padding-left:1mm;">Ventas<br />Netas</th>
			<th style="padding-left:1mm;">Pollos</th>
		    <th style="padding-left:1mm;">Precio<br />x Kilo</th>
		    <th style="padding-left:1mm;">Pescue.</th>
		    <th style="padding-left:1mm;">Piernas</th>
            <th style="padding-left:1mm;">MP/<br />
		    	Ventas</th>
		    <th style="padding-left:1mm;">Util/<br />
		      Ventas</th>
		    <th style="padding-left:1mm;">Util/<br />
	        MP</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" style="padding-left:1mm;">{vta_1}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_1}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_1}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_1}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_1}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_1}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_1}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" style="padding-left:1mm;">{vta_2}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_2}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_2}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_2}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_2}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_2}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_2}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" style="padding-left:1mm;">{vta_3}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_3}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_3}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_3}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_3}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_3}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_3}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" style="padding-left:1mm;">{vta_4}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_4}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_4}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_4}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_4}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_4}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_4}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" style="padding-left:1mm;">{vta_5}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_5}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_5}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_5}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_5}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_5}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_5}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" style="padding-left:1mm;">{vta_6}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_6}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_6}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_6}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_6}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_6}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_6}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" style="padding-left:1mm;">{vta_7}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_7}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_7}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_7}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_7}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_7}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_7}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" style="padding-left:1mm;">{vta_8}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_8}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_8}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_8}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_8}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_8}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_8}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" style="padding-left:1mm;">{vta_9}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_9}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_9}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_9}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_9}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_9}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_9}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" style="padding-left:1mm;">{vta_10}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_10}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_10}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_10}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_10}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_10}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_10}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" style="padding-left:1mm;">{vta_11}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_11}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_11}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_11}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_11}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_11}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_11}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" style="padding-left:1mm;">{vta_12}</td>
			<td align="right" class="Blue" style="padding-left:1mm;">{pollos_12}</td>
			<td align="right" class="Green" style="padding-left:1mm;">{precio_12}</td>
		    <td align="right" class="Blue" style="padding-left:1mm;">{pescuezos_12}</td>
		    <td align="right" class="Green" style="padding-left:1mm;">{piernas_12}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{mp_vtas_12}</td>
            <td align="right" class="Green" style="padding-left:1mm;">{util_vtas_12}</td>
		    <td align="right" class="Orange" style="padding-left:1mm;">{util_mp_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{vta}</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{pollos}</td>
			<td align="right" class="Bold" style="padding-left:1mm;">{precio}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">{pescuezos}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">{piernas}</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
            <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
		    <td align="right" class="Bold" style="padding-left:1mm;">&nbsp;</td>
		  </tr>
		  </tbody>
		</table>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE ESTADISTICAS -->
</div>
<!-- TERMINA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, UTILIDAD, ESTADISTICAS -->
<div class="page_break"></div>
<!-- END BLOCK : hoja1 -->
<!-- START BLOCK : hoja2 -->
<!-- EMPIEZA SEGUNDA HOJA DE BALANCE @ RELACIÓN DE GASTOS TOTALES -->
<div id="hoja2" class="hoja_carta">
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
<!-- START BLOCK : hoja_blanca -->
<!-- EMPIEZA SECCION HOJA EN BLANCO -->
<div class="hoja_blanca_carta">
  &nbsp;
</div>
<div class="page_break"></div>
<!-- TERMINA SECCION HOJA EN BLANCO -->
<!-- END BLOCK : hoja_blanca -->
<!-- TERMINA BALANCE DE ROSTICERIA -->
<!-- END BLOCK : balance -->
</body>
</html>
