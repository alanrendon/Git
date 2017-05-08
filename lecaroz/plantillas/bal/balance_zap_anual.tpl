<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Balances de Zapater&iacute;as Anual</title>

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
    <span class="Font10 Blue">{nombre_corto}</span><br />
    Balance del A&ntilde;o {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO -->
  <!-- EMPIEZA SECCION DE VENTAS -->
  <div id="Ventas" class="Seccion BorderBottom">
    <div class="Concepto Bold">+ Venta Zapater&iacute;a </div>
    <div class="Anexo1">{vz_p} {vz_c}</div>
    <div class="Nivel2 Bold Blue">{venta_zap}</div>
	<div class="Concepto">+ Abono Empleados</div>
	<div class="Nivel1 Blue">{abono_emp}</div>
	<div class="Concepto">+ Otros</div>
	<div class="Nivel1 Blue">{otros}</div>
	<div class="Concepto Bold">= Total Otros</div>
	<div class="Nivel2 Bold Blue">{total_otros}</div>
	<div class="Concepto">- Errores</div>
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
	<div class="Concepto">- Desc. Compras </div>
    <div class="Nivel1">{desc_compras}</div>
	<div class="Concepto">+ Traspaso Pares </div>
    <div class="Nivel1">{traspaso_pares}</div>
	<div class="Concepto">- Devoluciones </div>
    <div class="Nivel1">{devoluciones}</div>
	<div class="Concepto">- Inventario Actual</div>
    <div class="Nivel1 Blue">{inv_act}</div>
	<div class="Concepto Bold">= Materia Prima Utilizada</div>
    <div class="Nivel2 Bold Blue">{mat_prima_utilizada}</div>
	<div class="Concepto">- Desc. Pagos </div>
    <div class="Nivel2 Blue">{desc_pagos}</div>
	<div class="Concepto">- Dev. otros meses </div>
    <div class="Nivel2 Blue">{dev_otros_meses}</div>
	<div class="Concepto">- Dev. otras tiendas </div>
    <div class="Nivel2 Blue">{dev_otras_tiendas}</div>
	<div class="Concepto">- Dev. por otras tiendas </div>
    <div class="Nivel2 Blue">{dev_por_otras_tiendas}</div>
	<div class="Concepto Bold Font10">= Costo de Venta </div>
    <div class="Nivel3 Bold Font10 Blue">{costo_venta}</div>
  </div>
  <!-- TERMINA SECCION DE COSTOS -->
  <!-- EMPIEZA SECCION DE UTLIDAD BRUTA -->
  <div class="Seccion BorderBottom" id="UtilidadBruta">
    <div class="Concepto Bold Font10">Utilidad Bruta</div>
    <div class="Nivel3 Bold Font10">{utilidad_bruta}</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD BRUTA -->
  <!-- EMPIEZA SECCION DE GASTOS -->
  <div class="Seccion BorderBottom" id="Gastos">
    <div class="Concepto">- Gastos de Operaci&oacute;n </div>
    <div class="Nivel2 Red">{gastos_operacion}</div>
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
	 <div class="Concepto Bold Font10">Utilidad del A&ntilde;o</div>
	<div class="Anexo3 Bold">{un_c}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadNeta{num_cia}">{utilidad_neta}</div>
	 <div class="Concepto Bold Font10">Utilidad del A&ntilde;o + Inventario</div>
	<div class="Anexo3 Bold">{uni_c}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadNeta{num_cia}">{utilidad_neta_inv}</div>
	<div class="Concepto">+ Gastos no relacionados</div>
    <div class="Nivel3 Red" id="ImporteGastosNoRelacionados{num_cia}">&nbsp;</div>
	<div class="Concepto Bold Font10">Utilidad para Comparativo</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadTotal{num_cia}">&nbsp;</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD NETA -->
  <!-- EMPIEZA SECCION DE PROMEDIOS -->
  <div class="Seccion BorderBottom" id="Promedios">
    <div class="ColumnaDerechaNivel1">
	  <div id="Encargado">
	    <div class="ConceptoTipo3">Inici&oacute;:</div>
		<div class="ValorTipo3">{inicio}</div>
		<div class="ConceptoTipo3">Termin&oacute;:</div>
		<div class="ValorTipo3">{termino}</div>
	  </div>
	  <div id="Reservas" style="margin-top:3mm;">
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
	  <div id="EfectivoDepositado" style="margin-top:3mm;"{efectivodepositadohidden}>
	  	<div class="Bold">Efectivo Depositado</div>
		<div class="ConceptoTipo4 TextAlignCenter Bold">Fijo</div>
		<div class="ValorTipo4 TextAlignCenter Bold">{porcentaje}%</div>
		<div class="ConceptoTipo4 TextAlignCenter">{efe_fijo}</div>
		<div class="ValorTipo4 TextAlignCenter">{efe_2por}</div>
	  </div>
	</div>
	<div class="ColumnaIzquierdaNivel1">
	  <table align="center" style="border-collapse:collapse;empty-cells:show;">
        <tr class="RowHeader">
          <th scope="col">&nbsp;</th>
          <th scope="col">Sal.Ban.</th>
          <th scope="col">Sal.Pro.</th>
          <th scope="col">Par.Ven.</th>
          <th scope="col">Inv.Final</th>
          <th scope="col">Efectivo</th>
        </tr>
        <tr>
          <td>Ene</td>
          <td align="right" class="Blue">{sal1}</td>
          <td align="right" class="Red">{salpro1}</td>
          <td align="right" class="Green">{parven1}</td>
          <td align="right" class="Orange">{inv1}</td>
          <td align="right" class="Blue">{efe1}</td>
        </tr>
        <tr class="RowData">
          <td>Feb</td>
          <td align="right" class="Blue">{sal2}</td>
          <td align="right" class="Red">{salpro2}</td>
          <td align="right" class="Green">{parven2}</td>
          <td align="right" class="Orange">{inv2}</td>
          <td align="right" class="Blue">{efe2}</td>
        </tr>
        <tr>
          <td>Mar</td>
          <td align="right" class="Blue">{sal3}</td>
          <td align="right" class="Red">{salpro3}</td>
          <td align="right" class="Green">{parven3}</td>
          <td align="right" class="Orange">{inv3}</td>
          <td align="right" class="Blue">{efe3}</td>
        </tr>
        <tr class="RowData">
          <td>Abr</td>
          <td align="right" class="Blue">{sal4}</td>
          <td align="right" class="Red">{salpro4}</td>
          <td align="right" class="Green">{parven4}</td>
          <td align="right" class="Orange">{inv4}</td>
          <td align="right" class="Blue">{efe4}</td>
        </tr>
        <tr>
          <td>May</td>
          <td align="right" class="Blue">{sal5}</td>
          <td align="right" class="Red">{salpro5}</td>
          <td align="right" class="Green">{parven5}</td>
          <td align="right" class="Orange">{inv5}</td>
          <td align="right" class="Blue">{efe5}</td>
        </tr>
        <tr class="RowData">
          <td>Jun</td>
          <td align="right" class="Blue">{sal6}</td>
          <td align="right" class="Red">{salpro6}</td>
          <td align="right" class="Green">{parven6}</td>
          <td align="right" class="Orange">{inv6}</td>
          <td align="right" class="Blue">{efe6}</td>
        </tr>
        <tr>
          <td>Jul</td>
          <td align="right" class="Blue">{sal7}</td>
          <td align="right" class="Red">{salpro7}</td>
          <td align="right" class="Green">{parven7}</td>
          <td align="right" class="Orange">{inv7}</td>
          <td align="right" class="Blue">{efe7}</td>
        </tr>
        <tr class="RowData">
          <td>Ago</td>
          <td align="right" class="Blue">{sal8}</td>
          <td align="right" class="Red">{salpro8}</td>
          <td align="right" class="Green">{parven8}</td>
          <td align="right" class="Orange">{inv8}</td>
          <td align="right" class="Blue">{efe8}</td>
        </tr>
        <tr>
          <td>Sep</td>
          <td align="right" class="Blue">{sal9}</td>
          <td align="right" class="Red">{salpro9}</td>
          <td align="right" class="Green">{parven9}</td>
          <td align="right" class="Orange">{inv9}</td>
          <td align="right" class="Blue">{efe9}</td>
        </tr>
        <tr class="RowData">
          <td>Oct</td>
          <td align="right" class="Blue">{sal10}</td>
          <td align="right" class="Red">{salpro10}</td>
          <td align="right" class="Green">{parven10}</td>
          <td align="right" class="Orange">{inv10}</td>
          <td align="right" class="Blue">{efe10}</td>
        </tr>
        <tr>
          <td>Nov</td>
          <td align="right" class="Blue">{sal11}</td>
          <td align="right" class="Red">{salpro11}</td>
          <td align="right" class="Green">{parven11}</td>
          <td align="right" class="Orange">{inv11}</td>
          <td align="right" class="Blue">{efe11}</td>
        </tr>
        <tr class="RowData">
          <td>Dic</td>
          <td align="right" class="Blue">{sal12}</td>
          <td align="right" class="Red">{salpro12}</td>
          <td align="right" class="Green">{parven12}</td>
          <td align="right" class="Orange">{inv12}</td>
          <td align="right" class="Blue">{efe12}</td>
        </tr>
        <tr class="RowHeader">
          <th>Tot</th>
          <th align="right">{sal}</th>
          <th align="right">{salpro}</th>
          <th align="right">{parven}</th>
          <th align="right">{inv}</th>
          <th align="right"> {efe} </th>
        </tr>
      </table>
    </div>
  </div>
  <!-- TERMINA SECCION DE PROMEDIOS -->
  <!-- EMPIEZA UTILIDAD AÑO ANTERIOR -->
  <div class="Seccion BorderBottom" id="UtilidadAnyoAnterior">
  	<div class="Concepto Bold Font10">Utilidad A&ntilde;o Anterior</div>
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
			<th style="padding-left:1mm;">Venta</th>
			<th style="padding-left:1mm;">Clientes</th>
		    <th style="padding-left:1mm;">Prom.</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_1}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_1}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_2}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_2}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_3}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_3}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_4}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_4}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_5}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_5}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_6}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_6}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_7}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_7}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_8}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_8}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_9}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_9}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_10}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_10}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_11}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_11}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_ant_12}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_ant_12}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_ant_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:2mm;">{vta_ant}</td>
			<td align="right" class="Bold" style="padding-left:2mm;">{clientes_ant}</td>
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
			<th style="padding-left:1mm;">Venta </th>
			<th style="padding-left:1mm;">Clientes</th>
		    <th style="padding-left:1mm;">Prom.</th>
		  </tr>
		  <tbody>
		  <tr>
			<td>Ene</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_1}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_1}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_1}</td>
		  </tr>
		  <tr class="RowData">
			<td>Feb</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_2}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_2}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_2}</td>
		  </tr>
		  <tr>
			<td>Mar</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_3}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_3}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_3}</td>
		  </tr>
		  <tr class="RowData">
			<td>Abr</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_4}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_4}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_4}</td>
		  </tr>
		  <tr>
			<td>May</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_5}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_5}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_5}</td>
		  </tr>
		  <tr class="RowData">
			<td>Jun</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_6}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_6}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_6}</td>
		  </tr>
		  <tr>
			<td>Jul</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_7}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_7}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_7}</td>
		  </tr>
		  <tr class="RowData">
			<td>Ago</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_8}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_8}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_8}</td>
		  </tr>
		  <tr>
			<td>Sep</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_9}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_9}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_9}</td>
		  </tr>
		  <tr class="RowData">
			<td>Oct</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_10}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_10}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_10}</td>
		  </tr>
		  <tr>
			<td>Nov</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_11}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_11}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_11}</td>
		  </tr>
		  <tr class="RowData">
			<td>Dic</td>
			<td align="right" class="Blue" style="padding-left:2mm;">{vta_12}</td>
			<td align="right" class="Red" style="padding-left:2mm;">{clientes_12}</td>
		    <td align="right" class="Green" style="padding-left:2mm;">{prom_12}</td>
		  </tr>
		  <tr class="RowHeader">
			<td class="Bold">Tot</td>
			<td align="right" class="Bold" style="padding-left:2mm;">{vta}</td>
			<td align="right" class="Bold" style="padding-left:2mm;">{clientes}</td>
		    <td align="right" class="Bold">&nbsp;</td>
		  </tr>
		  </tbody>
		</table>
	  </div>
	</div>
  </div>
  <!-- TERMINA SECCION DE ESTADISTICAS -->
</div>
<!-- TERMINA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, PRODUCCION, UTILIDAD, ESTADISTICAS -->
<!-- END BLOCK : hoja1 -->
<!-- START BLOCK : hoja2 -->
<!-- EMPIEZA SEGUNDA HOJA DE BALANCE @ RELACIÓN DE GASTOS TOTALES -->
<div id="hoja2" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Relaci&oacute;n de Gastos Totales<br />
	{anyo}</div>
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
		<th style="width:25mm;">{anyo1}</th>
		<th style="width:25mm;">{anyo2}</th>
		<th style="width:25mm;">{anyo3}</th>
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
<!-- END BLOCK : hoja2 -->
<!-- START BLOCK : hoja3 -->
<!-- EMPIEZA TERCERA HOJA DE BALANCE @ COMPARATIVO -->
<div id="hoja3" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Comparativo de Balances <br />
	{anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO DE PÁGINA -->
  <!-- EMPIEZA SECCION DE COMPARATIVO -->
  <div id="Comparativo" class="Seccion">
    <table align="center" style="border-collapse:collapse;empty-cells:show;">
      <tr>
        <th style="width:50mm;">Concepto</th>
        <th style="width:40mm;">{anyo1} </th>
        <th style="width:40mm;">{anyo2} </th>
        <th style="width:40mm;">{anyo3} </th>
      </tr>
      <tr>
        <td class="Bold">+ Venta Zapateria </td>
        <td align="right" class="Bold Blue">{venta_zap1}</td>
        <td align="right" class="Bold Blue">{venta_zap2}</td>
        <td align="right" class="Bold Blue">{venta_zap3}</td>
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
        <td>- Desc. Compras </td>
        <td align="right">{desc_compras1}</td>
        <td align="right">{desc_compras2}</td>
        <td align="right">{desc_compras3}</td>
      </tr>
      <tr>
        <td>+ Traspaso Pares </td>
        <td align="right">{traspaso_pares1}</td>
        <td align="right">{traspaso_pares2}</td>
        <td align="right">{traspaso_pares3}</td>
      </tr>
      <tr>
        <td>- Devoluciones </td>
        <td align="right">{devoluciones1}</td>
        <td align="right">{devoluciones2}</td>
        <td align="right">{devoluciones3}</td>
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
        <td>- Desc. Pagos </td>
        <td align="right" class="Blue">{desc_pagos1}</td>
        <td align="right" class="Blue">{desc_pagos2}</td>
        <td align="right" class="Blue">{desc_pagos3}</td>
      </tr>
      <tr>
        <td>- Dev. otros meses </td>
        <td align="right" class="Blue">{dev_otros_meses1}</td>
        <td align="right" class="Blue">{dev_otros_meses2}</td>
        <td align="right" class="Blue">{dev_otros_meses3}</td>
      </tr>
      <tr>
        <td>- Dev. otras tiendas </td>
        <td align="right" class="Blue">{dev_otras_tiendas1}</td>
        <td align="right" class="Blue">{dev_otras_tiendas2}</td>
        <td align="right" class="Blue">{dev_otras_tiendas3}</td>
      </tr>
      <tr>
        <td>- Dev. por otras tiendas </td>
        <td align="right" class="Blue">{dev_por_otras_tiendas1}</td>
        <td align="right" class="Blue">{dev_por_otras_tiendas2}</td>
        <td align="right" class="Blue">{dev_por_otras_tiendas3}</td>
      </tr>
      <tr class="BorderBottom">
        <td class="Font10 Bold">= Costo de Venta </td>
        <td align="right" class="Font10 Bold Blue">{costo_venta1}</td>
        <td align="right" class="Font10 Bold Blue">{costo_venta2}</td>
        <td align="right" class="Font10 Bold Blue">{costo_venta3}</td>
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
        <td>- Gastos de Operaci&oacute;n </td>
        <td align="right" class="Red">{gastos_operacion1}</td>
        <td align="right" class="Red">{gastos_operacion2}</td>
        <td align="right" class="Red">{gastos_operacion3}</td>
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
    </table>
  </div>
  <!-- TERMINA SECCION DE COMPARATIVO -->
</div>
<!-- TERMINA TERCERA HOJA DE BALANCE @ COMPARATIVO -->
<!-- END BLOCK : hoja3 -->
<!-- START BLOCK : hoja4 -->
<!-- EMPIEZA CUARTA HOJA DE BALANCE @ LISTADO DE CHEQUES -->
<div id="hoja4" class="hoja_oficio">
  <!-- EMPIEZA SECCION DE ENCABEZADO DE PÁGINA -->
  <div id="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font10">{num_cia}</div>
	<div class="EncabezadoDerecho Bold Font10">{num_cia}</div>
	<div class="EncabezadoCentral Bold Font10">Listado de Cheques<br />
	{anyo}</div>
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
<!-- END BLOCK : hoja4 -->
<!-- START BLOCK : hoja_blanca -->
<!-- EMPIEZA SECCION HOJA EN BLANCO -->
<div class="hoja_blanca_oficio">
  &nbsp;
</div>
<!-- TERMINA SECCION HOJA EN BLANCO -->
<!-- END BLOCK : hoja_blanca -->
<!-- TERMINA BALANCE DE PANADERIA -->
<!-- END BLOCK : balance -->
</body>
</html>
