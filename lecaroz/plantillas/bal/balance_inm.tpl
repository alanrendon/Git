<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Balances de Inmobiliarias</title>

<link href="../../styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />
<link href="styles/balance_screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="styles/balance_print.css" rel="stylesheet" type="text/css" media="print" />

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

function CalculaUtilidadTotal(num_cia) {console.log(num_cia);
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
<div id="hoja1" class="hoja_carta">
  <!-- EMPIEZA SECCION DE ENCABEZADO -->
  <div class="Encabezado">
    <div class="EncabezadoIzquierdo Bold Font12">{num_cia}</div>
    <div class="EncabezadoDerecho Bold Font12">{num_cia}</div>
    <div class="EncabezadoCentral Bold"><span class="Font12 Blue">{nombre}</span><br />
        <span class="Font10 Blue">{nombre_corto}</span><br />
      Balance del Mes de {mes} de {anyo}</div>
  </div>
  <!-- TERMINA SECCION DE ENCABEZADO -->
  <!-- EMPIEZA SECCION DE RENTAS COBRADAS -->
  <div id="Ventas" class="Seccion BorderBottom">
    <div class="Concepto Bold">Rentas Cobradas </div>
    <div class="Anexo1">{rc_p} {rc_c}</div>
    <div class="Nivel2 Bold Blue">{rentas_cobradas}</div>
  </div>
  <!-- TERMINA SECCION DE RENTAS COBRADAS -->
  <!-- EMPIEZA SECCION DE UTLIDAD BRUTA -->
  <div class="Seccion BorderBottom" id="UtilidadBruta">
    <div class="Concepto Bold Font10">Utilidad Bruta</div>
    <div class="Nivel3 Bold Font10">{utilidad_bruta}</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD BRUTA -->
  <!-- EMPIEZA SECCION DE GASTOS -->
  <div class="Seccion BorderBottom" id="div">
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
    <div class="Concepto Bold Font10">Utilidad del Mes</div>
    <div class="Anexo3 Bold">{un_c}</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadNeta{num_cia}">{utilidad_neta}</div>
    <div class="Concepto">+ Gastos no relacionados</div>
    <div class="Nivel3 Red" id="ImporteGastosNoRelacionados{num_cia}">&nbsp;</div>
    <div class="Concepto Bold Font10">Utilidad para Comparativo</div>
    <div class="Nivel3 Bold Font10" id="ImporteUtilidadTotal{num_cia}">&nbsp;</div>
  </div>
  <!-- TERMINA SECCION DE UTILIDAD NETA -->
  <!-- EMPIEZA SECCION DE PROMEDIOS -->
  <div class="Seccion BorderBottom" id="Promedios">
    <div class="ColumnaDerechaNivel1">
      <div id="Reservas">
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
    </div>
    <div class="ColumnaIzquierdaNivel1">
      <div>
        <div class="ConceptoTipo2 Bold Font10">Saldo Inicial </div>
        <div class="ValorTipo2 Bold Font10">{saldo_inicial}</div>
        <div class="ConceptoTipo2 Bold Font10">Saldo Final </div>
        <div class="ValorTipo2 Bold Font10">{saldo_final}</div>
        <div class="ConceptoTipo2 Bold Font10">Diferencia</div>
        <div class="ValorTipo2 Bold Font10">{diferencia_saldo}</div>
      </div>
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
            <th style="padding-left:1mm;">Rentas<br />
              Cobradas</th>
            <th style="padding-left:1mm;">Saldos</th>
          </tr>
          <tbody>
            <tr>
              <td>Ene</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_1}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_1}</td>
            </tr>
            <tr class="RowData">
              <td>Feb</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_2}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_2}</td>
            </tr>
            <tr>
              <td>Mar</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_3}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_3}</td>
            </tr>
            <tr class="RowData">
              <td>Abr</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_4}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_4}</td>
            </tr>
            <tr>
              <td>May</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_5}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_5}</td>
            </tr>
            <tr class="RowData">
              <td>Jun</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_6}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_6}</td>
            </tr>
            <tr>
              <td>Jul</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_7}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_7}</td>
            </tr>
            <tr class="RowData">
              <td>Ago</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_8}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_8}</td>
            </tr>
            <tr>
              <td>Sep</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_9}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_9}</td>
            </tr>
            <tr class="RowData">
              <td>Oct</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_10}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_10}</td>
            </tr>
            <tr>
              <td>Nov</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_11}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_11}</td>
            </tr>
            <tr class="RowData">
              <td>Dic</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_ant_12}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_ant_12}</td>
            </tr>
            <tr class="RowHeader">
              <td class="Bold">Tot</td>
              <td align="right" class="Bold Blue" style="padding-left:2mm;">{rentas_ant}</td>
              <td align="right" class="Bold Purple" style="padding-left:2mm;">&nbsp;</td>
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
            <th style="padding-left:1mm;">Rentas<br />
              Cobradas </th>
            <th style="padding-left:1mm;">Saldos</th>
          </tr>
          <tbody>
            <tr>
              <td>Ene</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_1}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_1}</td>
            </tr>
            <tr class="RowData">
              <td>Feb</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_2}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_2}</td>
            </tr>
            <tr>
              <td>Mar</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_3}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_3}</td>
            </tr>
            <tr class="RowData">
              <td>Abr</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_4}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_4}</td>
            </tr>
            <tr>
              <td>May</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_5}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_5}</td>
            </tr>
            <tr class="RowData">
              <td>Jun</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_6}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_6}</td>
            </tr>
            <tr>
              <td>Jul</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_7}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_7}</td>
            </tr>
            <tr class="RowData">
              <td>Ago</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_8}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_8}</td>
            </tr>
            <tr>
              <td>Sep</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_9}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_9}</td>
            </tr>
            <tr class="RowData">
              <td>Oct</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_10}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_10}</td>
            </tr>
            <tr>
              <td>Nov</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_11}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_11}</td>
            </tr>
            <tr class="RowData">
              <td>Dic</td>
              <td align="right" style="padding-left:2mm;" class="Blue">{rentas_12}</td>
              <td align="right" style="padding-left:2mm;" class="Purple">{saldo_12}</td>
            </tr>
            <tr class="RowHeader">
              <td class="Bold">Tot</td>
              <td align="right" class="Bold Blue" style="padding-left:2mm;">{rentas}</td>
              <td align="right" class="Bold Purple" style="padding-left:2mm;">&nbsp;</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- START BLOCK : rentas_pendientes -->
  <div class="Seccion" id="Estadisticas" style="border-bottom-style:none;">
    <div class="ColumnaDerecha" style="overflow:hidden;">
      <div class="Bold TextAlignCenter">Rentas Pendientes del Mes </div>
      <div>
	    <table align="center" style="border-collapse:collapse;empty-cells:show;">
          <tr class="RowHeader">
            <th style="padding-left:1mm;">Fecha</th>
            <th style="padding-left:1mm;">Local</th>
            <th style="padding-left:1mm;">Importe</th>
          </tr>
          <tbody>
            <!-- START BLOCK : renta_pendiente_2 -->
			<tr>
              <td align="center" style="padding-left:2mm;">{fecha}</td>
              <td style="padding-left:2mm;">{local}</td>
              <td align="right" style="padding-left:2mm;">{importe}</td>
            </tr>
			<!-- END BLOCK : renta_pendiente_2 -->
          </tbody>
        </table>
	  </div>
    </div>
    <div class="ColumnaIzquierda" style="overflow:hidden; ">
      <div class="Bold TextAlignCenter">Rentas Pendientes del Mes </div>
      <div>
        <table align="center" style="border-collapse:collapse;empty-cells:show;">
          <tr class="RowHeader">
            <th style="padding-left:1mm;">Fecha</th>
            <th style="padding-left:1mm;">Local</th>
            <th style="padding-left:1mm;">Importe</th>
          </tr>
          <tbody>
            <!-- START BLOCK : renta_pendiente_1 -->
			<tr {class}>
              <td align="center" style="padding-left:2mm;">{fecha}</td>
              <td style="padding-left:2mm;">{local}</td>
              <td align="right" style="padding-left:2mm;">{importe}</td>
            </tr>
			<!-- END BLOCK : renta_pendiente_1 -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- END BLOCK : rentas_pendientes -->
  <!-- TERMINA SECCION DE ESTADISTICAS -->
</div>
<!-- TERMINA PRIMERA HOJA DE BALANCE @ VENTAS, MATERIA PRIMA, GASTOS, PRODUCCION, UTILIDAD, ESTADISTICAS -->
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
<!-- END BLOCK : hoja2 -->
<!-- START BLOCK : hoja3 -->
<!-- EMPIEZA CUARTA HOJA DE BALANCE @ LISTADO DE CHEQUES -->
<div id="hoja3" class="hoja_carta">
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
<!-- END BLOCK : hoja3 -->
<!-- START BLOCK : hoja_blanca -->
<!-- EMPIEZA SECCION HOJA EN BLANCO -->
<div class="hoja_blanca_carta">
  &nbsp;
</div>
<!-- TERMINA SECCION HOJA EN BLANCO -->
<!-- END BLOCK : hoja_blanca -->
<!-- TERMINA BALANCE DE PANADERIA -->
<!-- END BLOCK : balance -->
</body>
</html>
