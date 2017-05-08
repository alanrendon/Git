<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Registro de Facturas de Pastel</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools/Array.implement.js"></script>
<!--<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>-->
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}

.tip-title {
	font-weight: bold;
	font-size: 12pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}

.tip-text {
	font-weight: bold;
	padding: 0 5px;
}

/* Tabla de desglose */
#Detalle {
	background-color: #FFF;
	border-top: 2px solid #666;
	border-bottom: 2px solid #666;
	padding: 3px 0;
	margin: 5px 0;
}

#Detalle th {
	empty-cells: show;
	background-color: #666;
	color: #FFF;
	padding: 2px 4px 2px 4px;
}

#Detalle td {
	padding: 2px 2px 2px 2px;
	color: #000;
}

#Detalle tr.off {
	background-color: #FFF;
}

#Detalle tr.on {
	background-color: #CCC;
}

</style>

</head>

<body>
<!-- START BLOCK : captura -->
<div id="contenedor">
  <div id="titulo">Registro de Facturas de Pastel</div>
    <div id="captura" align="center">
    <form action="RegistroFacturasPastel.php" method="post" name="RegistroFacturasPastel" id="RegistroFacturasPastel" class="formulario">
	<table cellspacing="1" class="tabla_captura">
      <tr>
        <th align="left" class="encabezado" scope="row">Compa&ntilde;&iacute;a</th>
        <td width="200" align="left" class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter bold fontSize12pt" id="num_cia" style="width:30px;" onkeyup="movCursor(event.keyCode,fecha,null,null,null,fecha)" size="3" />
        <span id="nombre_cia" style="font-size:12pt;font-weight:bold;"></span></td>
      </tr>
      <tr>
        <th align="left" class="encabezado" scope="row">Fecha</th>
        <td align="left" class="linea_on"><input name="fecha" type="text" class="cap toDate bold fontSize12pt alignCenter" id="fecha" style="width:85px;" onkeyup="movCursor(event.keyCode,letra_folio[0],null,null,num_cia,letra_folio[0])" size="10" maxlength="10" /><span id="rezago"></span></td>
      </tr>
    </table>
    <table cellspacing="1" class="tabla_captura">
      <tr>
        <th align="center"><img src="imagenes/notice16x16.png" alt="Avisos" name="Avisos" width="16" height="16" id="Avisos" /></th>
        <th align="center">Factura</th>
        <th align="center">Exp</th>
        <th align="center">Kilos</th>
        <th align="center">Precio<br />Unidad</th>
        <th align="center">Pan</th>
        <th align="center">Base</th>
        <th align="center">A cuenta</th>
        <th align="center">Dev.<br />Exp.</th>
        <th align="center">Fecha<br />Entrega</th>
        <th align="center">Total<br />Factura</th>
        <th align="center">Resta<br />Pagar</th>
        <th align="center">Dev. de<br />Base</th>
        <th align="center">Resta</th>
        <th align="center">Pastillaje</th>
        <th align="center">Otros</th>
      </tr>
      <!-- START BLOCK : fila -->
      <tr class="linea_{color_row}">
        <td align="center"><input name="tipo[]" id="tipo" type="hidden" value="" /><input name="estado[]" id="estado" type="hidden" value="" />
        <img src="imagenes/BulletGray16x16.png" name="status" width="16" height="16" class="status{i}" id="status{i}" /><img src="imagenes/WhiteSheet16x16.png" name="type{i}" width="16" height="16" class="type{i}" id="type{i}" /></td>
        <td align="center" width="56"><input name="letra_folio[]" type="text" class="cap toText toUpper alignCenter" id="letra_folio" style="width:10px;" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,num_remi[{i}],null,num_remi[{i}],letra_folio[{back}],letra_folio[{next}])" size="1" maxlength="1" />
          <input name="num_remi[]" type="text" class="cap toPosInt" id="num_remi" style="width:35px;" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,idexpendio[{i}],letra_folio[{i}],idexpendio[{i}],num_remi[{back}],num_remi[{next}])" size="5" /></td>
        <td align="center"><input name="idexpendio[]" type="text" class="cap toPosInt alignCenter" id="idexpendio" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,kilos[{i}],num_remi[{i}],kilos[{i}],idexpendio[{back}],idexpendio[{next}])" size="3" /><input name="porExp[]" type="hidden" id="porExp" value="" /></td>
        <td align="center"><input name="kilos[]" type="text" class="cap numPosFormat2 alignRight Green" id="kilos" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,precio_unidad[{i}],idexpendio[{i}],precio_unidad[{i}],kilos[{back}],kilos[{next}])" size="6" /></td>
        <td align="center"><input name="precio_unidad[]" type="text" class="cap numPosFormat2 alignRight Green" id="precio_unidad" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,otros[{i}],kilos[{i}],otros[{i}],precio_unidad[{back}],precio_unidad[{next}])" size="6" /></td>
        <td align="center"><input name="otros[]" type="text" class="cap numPosFormat2 alignRight Green" id="otros" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,base[{i}],precio_unidad[{i}],base[{i}],otros[{back}],otros[{next}])" size="6" /></td>
        <td align="center"><input name="base[]" type="text" class="cap numPosFormat2 alignRight Green" id="base" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,cuenta[{i}],precio_unidad[{i}],cuenta[{i}],base[{back}],base[{next}])" size="6" /></td>
        <td align="center"><input name="cuenta[]" type="text" class="cap numPosFormat2 alignRight Green" id="cuenta" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,dev_exp[{i}],base[{i}],dev_exp[{i}],cuenta[{back}],cuenta[{next}])" size="6" /></td>
        <td align="center"><input name="dev_exp[]" type="text" class="cap numPosFormat2 alignRight Green" id="dev_exp" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,fecha_entrega[{i}],cuenta[{i}],fecha_entrega[{i}],dev_exp[{back}],dev_exp[{next}])" size="6" /></td>
        <td align="center"><input name="fecha_entrega[]" type="text" class="cap toDate alignCenter Green" id="fecha_entrega" style="width:70px;" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,dev_base[{i}],dev_exp[{i}],dev_base[{i}],fecha_entrega[{back}],fecha_entrega[{next}])" size="10" maxlength="10" /></td>
        <td align="center"><input name="total_factura[]" type="text" class="readOnly alignRight bold Blue" id="total_factura" size="6" /></td>
        <td align="center"><input name="resta_pagar[]" type="text" class="readOnly alignRight bold Red" id="resta_pagar" size="6" /></td>
        <td align="center"><input name="dev_base[]" type="text" class="cap numPosFormat2 alignRight Blue" id="dev_base" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,resta[{i}],fecha_entrega[{i}],resta[{i}],dev_base[{back}],dev_base[{next}])" size="6" /></td>
        <td align="center"><input name="resta[]" type="text" class="cap numPosFormat2 alignRight Red" id="resta" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,pastillaje[{i}],dev_base[{i}],pastillaje[{i}],resta[{back}],resta[{next}])" size="6" /></td>
        <td align="center"><input name="pastillaje[]" type="text" class="cap numPosFormat2 alignRight Green" id="pastillaje" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,otros_efectivos[{i}],resta[{i}],otros_efectivos[{i}],pastillaje[{back}],pastillaje[{next}])" size="6" /></td>
        <td align="center"><input name="otros_efectivos[]" type="text" class="cap numPosFormat2 alignRight Green" id="otros_efectivos" onblur="StatusFactura({i})" onkeyup="movCursor(event.keyCode,letra_folio[{next}],pastillaje[{i}],null,otros_efectivos[{back}],otros_efectivos[{next}])" size="6" /></td>
        <!-- END BLOCK : fila -->
      </tr>
    </table>
    <table class="tabla_captura">
      <tr>
        <th scope="row">Venta<br />
          en puerta </th>
        <th>Abono<br />
          Expendio</th>
        <th>Bases</th>
        <th>Devoluci&oacute;n<br />
          de bases </th>
      </tr>
      <tr>
        <td id="VentaPuerta" align="center" style="font-size:14pt;font-weight:bold;" scope="row">&nbsp;</td>
        <td id="AbonoExpendio" align="center" style="font-size:14pt;font-weight:bold;">&nbsp;</td>
        <td id="Bases" align="center" style="font-size:14pt;font-weight:bold;">&nbsp;</td>
        <td id="DevBases" align="center" style="font-size:14pt;font-weight:bold;">&nbsp;</td>
      </tr>
    </table>
    <p>
      <input type="button" class="boton" onclick="validar()" value="Registrar" />
    </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f;					// Contiene el objeto 'Formulario'
var t;					// Contiene el objeto 'Tabla'
var stack = [];			// Pila de almacenamiento de números de factura
var facStack = [];		// Pila de almacenamiento de facturas recuperadas de la base de datos
var tipsStatus = [];	// Contiene el objeto 'Tips' para los tooltips de status
var porExp = [];		// Pila de almacenamiento de porcentajes por expendio

window.addEvent('domready', function() {
	f = new Formulario('RegistroFacturasPastel');
	f.mostrarAlertas = true;
	
	t = new Tabla();
	
	f.form.getElementById('num_cia').addEvent('change', function(e)
	{
		ValidarCia();
	});
	
	f.form.getElementById('fecha').addEvent('change', function(e)
	{
		ValidarFecha();
	});
	
	// Setear tooltips
	AsignarTips();
	
	f.form.num_cia.select();
});

function ValidarCia() {
	var num_cia = f.form.getElementById('num_cia').get('value');
	
	// *** Resetear todo el formulario si hubo valores anteriores ***
	if (Formulario.tmp.getVal() > 0)
	{
		f.form.reset();
		f.form.getElementById('num_cia').set('value', num_cia);
		
		f.form.getElementById('fecha').set('value', null);
		
		// *** Resetear tooltips ***
		AsignarTips();
		
		// *** Resetear iconos ***
		ResetearIconos();
		
		// *** Resetear totales ***
		$('VentaPuerta').set('html', '&nbsp;');
		$('AbonoExpendio').set('html', '&nbsp;');
		$('Bases').set('html', '&nbsp;');
		$('DevBases').set('html', '&nbsp;');
	}
	
	if (num_cia.getVal() > 0)
	{
		new Request({
			url: 'RegistroFacturasPastel.php',
			method: 'get',
			onSuccess: function(nombre_cia)
			{
				$('nombre_cia').set('html', nombre_cia);
			}
		}).send('c=' + num_cia);
	}
	else
	{
		$('nombre_cia').set('html', '&nbsp;');
	}
}

function ValidarFecha() {
	var fecha = f.form.getElementById('fecha').get('value');
	var num_cia = f.form.getElementById('num_cia').get('value');
	
	if (fecha.length > 8)
	{
		new Request({
			url: 'RegistroFacturasPastel.php',
			method: 'get',
			onSuccess: function(result)
			{
				if (result.getVal() == -1) {
					alert('La fecha de captura no puede ser de meses pasados');
					f.form.getElementById('fecha').set('value', '');
					f.form.getElementById('fecha').focus();
				}
				else if (result.getVal() == -2) {
					alert('Hay facturas pendientes de liquidar menores a la fecha de captura');
					f.form.getElementById('fecha').set('value', '');
					f.form.getElementById('fecha').focus();
				}
			}
		}).send('f=' + fecha + '&c=' + num_cia);
	}
}

function AsignarTips() {
	// Asignar el objeto 'Tips' a las elementos con clases .status
	$$(document.getElementsByName('letra_folio[]')).each(function(el, i)
	{
		tipsStatus[i] = new Tips($$('.status' + i), {
			fixed: true,
			className: 'Tip',
			showDelay: 0,
			hideDelay: 0
		});
		/*tipsStatus[i].addEvents({
			'show': function(tip) {
				tip.fade('in');
			},
			'hide': function(tip) {
				tip.fade('out');
			}
		});*/
		$('status' + i).store('tip:title', 'Estatus');
		$('status' + i).store('tip:text', 'Sin definir');
	});
}

function ResetearIconos() {
	$$(document.getElementsByName('letra_folio[]')).each(function(el, i)
	{
		$('status' + i).setProperty('src', 'imagenes/BulletGray16x16.png');
		$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
	});
}

function DesgloseFactura(i) {
	if (facStack[i].length < 2)
	{
		return false;
	}
	
	var tbl = '', des = '', a_cuenta = 0;
	
	des += '<table id="Detalle" align="center">';
	des += '<tr>';
	des += '<th>Tipo</th>';
	des += '<th>Fecha</th>';
	des += '<th>Importe</th>';
	des += '</tr>';
	for (var j = 1; j < facStack[i].length; j++)
	{
		des += '<tr class="' + (j % 2 == 0 ? 'on' : 'off') + '">';
		des += '<td align="center"><img src="';
		switch (facStack[i][j][0])
		{
			case 0:
				des += 'imagenes/YellowSheet16x16.png';
			break;
			
			case 1:
				des += 'imagenes/GreenSheet16x16.png';
			break;
			
			case 2:
				des += 'imagenes/BlueSheet16x16.png';
			break;
		}
		des += '"></td>';
		des += '<td style="font-weight:bold;">' + facStack[i][j][3] + '</td>';
		des += '<td align="right" style="font-weight:bold;">';
		switch (facStack[i][j][0])
		{
			case 0:
				des += facStack[i][j][6].numberFormat(2, '.', ',');
				a_cuenta += facStack[i][j][6];
			break;
			
			case 1:
				des += '<span style="color:#00C;">';
				des += facStack[i][j][7].numberFormat(2, '.', ',');
				des += '</span>';
				a_cuenta += facStack[i][j][7];
			break;
			
			case 2:
				des += '<span style="color:#C00;">';
				des += facStack[i][j][8].numberFormat(2, '.', ',');
				des += '</span>';
			break;
		}
	}
	des += '</table>';
	
	tbl += '<table id="Detalle" align="center">';
	tbl += '<tr>';
	tbl += '<th>Fecha<br />Entrega</th>';
	if (facStack[i][1][11] != 0)
	{
		tbl += '<th>Pan</th>';
	}
	else
	{
		tbl += '<th>Kilos</th>';
		tbl += '<th>Precio</th>';
	}
	tbl += '<th>Base</th>';
	tbl += '<th>Pastillaje</th>';
	tbl += '<th>Otros</th>';
	tbl += '<th>Total<br />Factura</th>';
	tbl += '<th>A cuenta</th>';
	tbl += '<th>Resta</th>';
	tbl += '</tr>';
	tbl += '<tr class="off">';
	tbl += '<td style="font-weight:bold;">' + facStack[i][1][4] + '</td>';
	if (facStack[i][1][11] != 0)
	{
		tbl += '<td align="right" style="color:#060;">' + facStack[i][1][11].numberFormat(2, '.', ',') + '</td>';
	}
	else
	{
		tbl += '<td align="right" style="color:#060;">' + facStack[i][1][9].numberFormat(2, '.', ',') + '</td>';
		tbl += '<td align="right" style="color:#060;">' + facStack[i][1][10].numberFormat(2, '.', ',') + '</td>';
	}
	tbl += '<td align="right" style="color:#C00;">' + (facStack[i][1][12] != 0 ? facStack[i][1][12].numberFormat(2, '.', ',') : '&nbsp;') + '</td>';
	tbl += '<td align="right" style="color:#060;">' + (facStack[i][1][13] != 0 ? facStack[i][1][13].numberFormat(2, '.', ',') : '&nbsp;') + '</td>';
	tbl += '<td align="right" style="color:#060;">' + (facStack[i][1][14] != 0 ? facStack[i][1][14].numberFormat(2, '.', ',') : '&nbsp;') + '</td>';
	tbl += '<td align="right" style="font-weight:bold;font-size:12pt;text-decoration:underline;">' + facStack[i][1][5].numberFormat(2, '.', ',') + '</td>';
	tbl += '<td align="right" style="font-weight:bold;color:#00C;">' + (a_cuenta != 0 ? a_cuenta.numberFormat(2, '.', ',') : '&nbsp;') + '</td>';
	tbl += '<td align="right" style="font-weight:bold;color:#C00;">' + (facStack[i][1][5] - a_cuenta != 0 ? (facStack[i][1][5] - a_cuenta).numberFormat(2, '.', ',') : '&nbsp;') + '</td>';
	tbl += '</tr>';
	tbl += '</table>';
	
	return tbl + des;
}

function CambiaStatus(i, letra_folio, num_remi) {
	$('status' + i).store('tip:title', 'Estatus factura <span style="text-decoration:underline;">' + (letra_folio != 'X' ? letra_folio + '-' : '') + num_remi + '</span>');
	
	f.form.idexpendio[i].set('value', null);
	f.form.kilos[i].set('value', null);
	f.form.precio_unidad[i].set('value', null);
	f.form.otros[i].set('value', null);
	f.form.base[i].set('value', null);
	f.form.cuenta[i].set('value', null);
	f.form.dev_exp[i].set('value', null);
	f.form.fecha_entrega[i].set('value', null);
	f.form.total_factura[i].set('value', null);
	f.form.resta_pagar[i].set('value', null);
	f.form.dev_base[i].set('value', null);
	f.form.resta[i].set('value', null);
	f.form.pastillaje[i].set('value', null);
	f.form.otros_efectivos[i].set('value', null);
	
	// @@@ Determinar el último status de la factura @@@
	
	// *** Ya hay controles capturados ***
	if (facStack[i].length > 1)
	{
		// *** Ya esta pagada la factura ***
		if (facStack[i][1][1] == 1)
		{
			// *** Desactivar todos los campos ***
			f.form.kilos[i].set('readonly', true);
			f.form.precio_unidad[i].set('readonly', true);
			f.form.otros[i].set('readonly', true);
			f.form.base[i].set('readonly', true);
			f.form.cuenta[i].set('readonly', true);
			f.form.dev_exp[i].set('readonly', true);
			f.form.resta[i].set('readonly', true);
			f.form.fecha_entrega[i].set('readonly', true);
			f.form.idexpendio[i].set('readonly', true);
			f.form.pastillaje[i].set('readonly', true);
			f.form.otros_efectivos[i].set('readonly', true);
			
			// *** Ya no se puede meter ni un control de esta factura ***
			if (facStack[i].getLast()[0] == 2)
			{
				$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
				$('status' + i).store('tip:text', 'La factura esta pagada y tiene devoluci&oacute;n de base' + DesgloseFactura(i));
				
				$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
				
				// *** Desactivar campo dev_base ***
				f.form.dev_base[i].set('readonly', true);
				
				// *** Sin tipo ***
				// *** Estado -1: la factura esta pagada y tiene devolución de base
				f.form.tipo[i].set('value', null);
				f.form.estado[i].set('value', -1);
			}
			// *** Se puede meter el control azul 'devolución de base' de la factura si 'base' > 0 ***
			else if (facStack[i][1][12] > 0)
			{
				$('status' + i).setProperty('src', 'imagenes/BulletBlue16x16.png');
				$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#00C;font-weight:bold;">Azul</span> \'Devoluci&oacute;n de Base\'' + DesgloseFactura(i));
				// *** Activar campo dev_base ***
				f.form.dev_base[i].set('readonly', false);
				
				$('type' + i).setProperty('src', 'imagenes/BlueSheet16x16.png');
				
				// *** Poner el valor de la devolución de base ***
				f.form.dev_base[i].set('value', facStack[i][1][12].numberFormat(2, '.', ','));
				
				// *** Tipo 3: Control Azul ***
				// *** Estado 1: Capturado ***
				f.form.tipo[i].set('value', 3);
				f.form.estado[i].set('value', 1);
			}
			else
			{
				$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
				$('status' + i).store('tip:text', 'La factura esta pagada y no tiene base para devolcui&oacute;n' + DesgloseFactura(i));
				
				$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
				
				// *** Desactivar campo dev_base ***
				f.form.dev_base[i].set('readonly', true);
				
				// *** Sin tipo ***
				// *** Estado -2: la factura esta pagada y no tiene base para devolución
				f.form.tipo[i].set('value', null);
				f.form.estado[i].set('value', -2);
			}
		}
		// *** La factura esta cancelada y no se puede meter mas controles ***
		else if (facStack[i][1][1] == 2)
		{
			$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
			$('status' + i).store('tip:text', 'La factura esta cancelada');
			
			$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
			
			// *** Sin tipo ***
			// *** Estado -3: la factura esta cancelada
			f.form.tipo[i].set('value', null);
			f.form.estado[i].set('value', -3);
			
			// *** Desactivar todos los campos ***
			f.form.idexpendio[i].set('readonly', true);
			f.form.kilos[i].set('readonly', true);
			f.form.precio_unidad[i].set('readonly', true);
			f.form.otros[i].set('readonly', true);
			f.form.base[i].set('readonly', true);
			f.form.cuenta[i].set('readonly', true);
			f.form.dev_exp[i].set('readonly', true);
			f.form.dev_base[i].set('readonly', true);
			f.form.resta[i].set('readonly', true);
			f.form.fecha_entrega[i].set('readonly', true);
			f.form.pastillaje[i].set('readonly', true);
			f.form.otros_efectivos[i].set('readonly', true);
		}
		// *** La factura no esta pagada todabía, se puede capturar control verde 'resta' ***
		else if (facStack[i][1][1] == 0)
		{
			$('status' + i).setProperty('src', 'imagenes/BulletGreen16x16.png');
			$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#060;font-weight:bold;">Verde</span> \'Resta\'' + DesgloseFactura(i));
			
			$('type' + i).setProperty('src', 'imagenes/GreenSheet16x16.png');
			
			// *** Tipo 2: Control Verde ***
			// *** Estado 1: Capturado ***
			f.form.tipo[i].set('value', 2);
			f.form.estado[i].set('value', 1);
			
			f.form.total_factura[i].set('value', facStack[i][1][5].numberFormat(2, '.', ','));
			f.form.resta_pagar[i].set('value', null);
			f.form.resta[i].set('value', facStack[i][1][15].numberFormat(2, '.', ','));
			
			// *** Activar solo los campos resta e idexpendio para edición ***
			f.form.idexpendio[i].set('readonly', false);
			f.form.kilos[i].set('readonly', true);
			f.form.precio_unidad[i].set('readonly', true);
			f.form.otros[i].set('readonly', true);
			f.form.base[i].set('readonly', true);
			f.form.cuenta[i].set('readonly', true);
			f.form.dev_exp[i].set('readonly', true);
			f.form.dev_base[i].set('readonly', true);
			f.form.resta[i].set('readonly', false);
			f.form.fecha_entrega[i].set('readonly', true);
			f.form.pastillaje[i].set('readonly', true);
			f.form.otros_efectivos[i].set('readonly', true);
		}
	}
	// *** La factura no esta capturada en el sistema, se puede capturar control amarillo 'venta' ***
	else
	{
		$('status' + i).setProperty('src', 'imagenes/BulletYellow16x16.png');
		$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#FC0;font-weight:bold;">Amarillo</span> \'Venta\'');
		
		$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
		
		// *** Tipo 1: Control Amarillo ***
		// *** Estado 1: Capturado ***
		f.form.tipo[i].set('value', 1);
		f.form.estado[i].set('value', null);
		
		// *** Quitar marcas de solo lectura ***
		f.form.idexpendio[i].set('readonly', false);
		f.form.kilos[i].set('readonly', false);
		f.form.precio_unidad[i].set('readonly', false);
		f.form.otros[i].set('readonly', false);
		f.form.base[i].set('readonly', false);
		f.form.cuenta[i].set('readonly', false);
		f.form.dev_exp[i].set('readonly', true);
		f.form.dev_base[i].set('readonly', true);
		f.form.resta[i].set('readonly', true);
		f.form.fecha_entrega[i].set('readonly', false);
		f.form.pastillaje[i].set('readonly', false);
		f.form.otros_efectivos[i].set('readonly', false);
	}
	
	CalculaTotales();
}


function ValidarFechaEntrega(i) {
	var fc = f.form.getElementById('fecha').get('value');
	var fe = f.form.fecha_entrega[i].get('value');
	
	if (fc.length >= 8)
	{
		new Request({
			url: 'RegistroFacturasPastel.php',
			method: 'get',
			data:
			{
				'fc': fc,
				'fe': fe
			},
			onSuccess: function(result)
			{
				if (result.getVal() < 0)
				{
					$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
					$('status' + i).store('tip:text', 'La fecha de entrega no puede ser menor a la fecha de captura');
					
					// *** Estado -8: La fecha de entrega no puede ser menor a la fecha de captura ***
					f.form.estado[i].set('value', -8);
				}
				else
				{
					$('status' + i).setProperty('src', 'imagenes/BulletYellow16x16.png');
					$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#FC0;font-weight:bold;">Amarillo</span> \'Venta\'');
					
					f.form.estado[i].set('value', 1);
				}
			}
		}).send();
	}
}

function StatusFactura(i) {
	// *** Validar que esten capturados el número de panaderia y la fecha de registro
	if (f.form.num_cia.get('value').getVal() <= 0 || f.form.fecha.get('value').length < 8)
	{
		alert('Debe capturar el número de panaderia y la fecha de registro para poder continuar con la captura');
		f.form.num_cia.select();
		return false;
	}
	
	var num_cia = f.form.num_cia.get('value');
	var letra_folio = f.form.letra_folio[i].get('value');
	var num_remi = f.form.num_remi[i].get('value').getVal();
	var kilos = f.form.kilos[i].get('value').getVal();
	var precio_unidad = f.form.precio_unidad[i].get('value').getVal();
	var otros = f.form.otros[i].get('value').getVal();
	var base = f.form.base[i].get('value').getVal();
	var cuenta = f.form.cuenta[i].get('value').getVal();
	var dev_exp = f.form.dev_exp[i].get('value').getVal();
	var dev_base = f.form.dev_base[i].get('value').getVal();
	var resta = f.form.resta[i].get('value').getVal();
	var fecha_entrega = f.form.fecha_entrega[i].get('value');
	var idexp = f.form.idexpendio[i].get('value').getVal();
	var pastillaje = f.form.pastillaje[i].get('value').getVal();
	var otros_efectivos = f.form.otros_efectivos[i].get('value').getVal();
	
	// *** Si no hay letra para el folio poner su valor a 'X'
	if (letra_folio == '')
	{
		letra_folio = 'X';
	}
	
	// *** No se ha capturado un número de factura ***
	if (num_remi == 0)
	{
		// *** Quitar número de factura del stack ***
		stack[i] = null;
		// *** Quitar controles recuperados del stack ***
		facStack[i] = null;
		
		$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
		$('status' + i).setProperty('src', 'imagenes/BulletGray16x16.png');
		$('status' + i).store('tip:title', 'Estatus');
		$('status' + i).store('tip:text', 'Sin definir');
		
		f.form.tipo[i].set('value', null);
		f.form.estado[i].set('value', null);
		f.form.idexpendio[i].set('value', null);
		f.form.kilos[i].set('value', null);
		f.form.precio_unidad[i].set('value', null);
		f.form.otros[i].set('value', null);
		f.form.base[i].set('value', null);
		f.form.cuenta[i].set('value', null);
		f.form.dev_exp[i].set('value', null);
		f.form.fecha_entrega[i].set('value', null);
		f.form.total_factura[i].set('value', null);
		f.form.resta_pagar[i].set('value', null);
		f.form.dev_base[i].set('value', null);
		f.form.resta[i].set('value', null);
		f.form.pastillaje[i].set('value', null);
		f.form.otros_efectivos[i].set('value', null);
		
		// *** Poner marcas de solo lectura ***
		f.form.kilos[i].set('readonly', true);
		f.form.precio_unidad[i].set('readonly', true);
		f.form.otros[i].set('readonly', true);
		f.form.base[i].set('readonly', true);
		f.form.cuenta[i].set('readonly', true);
		f.form.cuenta[i].set('readonly', true);
		f.form.dev_exp[i].set('readonly', true);
		f.form.dev_base[i].set('readonly', true);
		f.form.resta[i].set('readonly', true);
		f.form.fecha_entrega[i].set('readonly', true);
		f.form.idexpendio[i].set('readonly', true);
		f.form.pastillaje[i].set('readonly', true);
		f.form.otros_efectivos[i].set('readonly', true);
	}
	// *** Se ha capturado un número de factura ***
	else if (num_remi > 0) {
		// *** Validar que la factura no este ya ingresada en el stack ***
		if (stack.indexOf(num_remi) < 0)
		{
			stack[i] = num_remi;
		}
		else if (stack.indexOf(num_remi) >= 0 && stack.indexOf(num_remi) != i)
		{
			alert('La factura ya esta capturada en pantalla');
			f.form.num_remi[i].value = f.tmp;
			return false;
		}
		
		// *** Obtener controles capturados con el folio dado ***
		if (facStack[i] == null || facStack[i][0] != stack[i])
		{
			new Request({
				url: 'RegistroFacturasPastel.php',
				method: 'get',
				data:
				{
					'num_cia': num_cia,
					'num_remi': num_remi,
					'letra': letra_folio
				},
				onRequest: function()
				{
					$('status' + i).setProperty('src', 'imagenes/_loading.gif');
				},
				onSuccess: function(r)
				{
					var result = r.split('\n');
					
					if (result.length == 1 && result[0].split('|').length == 1)
					{
						if (result[0].getVal() == -1) {
							$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
							$('status' + i).store('tip:text', 'El folio de la factura no se encuentra en ningun block dado de alta en el sistema');
							$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
							
							// *** Sin tipo ***
							// *** Estado -7: El folio de la factura no se encuentra en ningun block dado de alta en el sistema ***
							f.form.tipo[i].set('value', null);
							f.form.estado[i].set('value', -7);
						}
						else if (result[0].getVal() > 0) {
							for (var rem = num_remi - result[0]; rem < num_remi; rem++)
								if (stack.indexOf(rem) == -1) {
									$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
									$('status' + i).store('tip:text', 'La folio de la factura no es el consecutivo de la última nota (factura brincada)');
									$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
									
									// *** Sin tipo ***
									// *** Estado -9: La folio de la factura no es el consecutivo de la última nota (factura brincada) ***
									f.form.tipo[i].set('value', null);
									f.form.estado[i].set('value', -9);
									
									result[0] = '-1';
									
									break;
								}
								else {
									result[0] = '';
								}
						}
					}
					
					if (result.length > 0)
					{
						facStack[i] = [result.length];
						
						facStack[i][0] = num_remi;
						
						result.each(function(reg, j)
						{
							var fields = reg.split('|');
							
							if (fields.length > 0)
							{
								if (fields != '')
								{
									facStack[i][j + 1] = [fields.length - 1];
									
									fields.each(function(el, k)
									{
										facStack[i][j + 1][k] = k != 3 && k != 4 ? (el.getVal() !== false ? el.getVal() : 0) : el;
									});
								}
							}
						});
					}
					
					CambiaStatus(i, letra_folio, num_remi);
				}
			}).send();
		}
		
		// @@@ En caso de ser factura abonada a expendio obtener porcentaje de ganancia @@@
		if (idexp > 0)
		{
			if (f.form.tipo[i].get('value').getVal() == 1)
			{
				if (letra_folio == 'P')
				{
					f.form.kilos[i].set('value', null);
					f.form.kilos[i].set('readonly', true);
					f.form.precio_unidad[i].set('value', null);
					f.form.precio_unidad[i].set('readonly', true);
					f.form.base[i].set('value', null);
					f.form.base[i].set('readonly', true);
					
					f.form.dev_exp[i].set('readonly', false);
					
					if (!$defined(porExp[idexp]))
					{
						new Request({
							url: 'RegistroFacturasPastel.php',
							method: 'get',
							async: false,
							data:
							{
								num_cia: num_cia,
								num_exp: idexp
							},
							onSuccess: function(text)
							{
								porExp[idexp] = text.getVal();
								f.form.porExp[i].set('value', text.getVal());
							}
						}).send();
					}
				}
				else if (letra_folio == 'X')
				{
					f.form.kilos[i].set('readonly', false);
					f.form.precio_unidad[i].set('readonly', false);
					f.form.base[i].set('readonly', false);
					
					f.form.dev_exp[i].set('readonly', false);
					
					if (!$defined(porExp[idexp]))
					{
						new Request({
							url: 'RegistroFacturasPastel.php',
							method: 'get',
							async: false,
							data:
							{
								num_cia: num_cia,
								num_exp: idexp
							},
							onSuccess: function(text)
							{
								porExp[idexp] = text.getVal();
								f.form.porExp[i].set('value', text.getVal());
							}
						}).send();
					}
				}
			}
			else if (f.form.tipo[i].get('value').getVal() == 2)
			{
				if (letra_folio == 'P')
				{
					f.form.dev_exp[i].set('readonly', false);
					
					if (!$defined(porExp[idexp]))
					{
						new Request({
							url: 'RegistroFacturasPastel.php',
							method: 'get',
							async: false,
							data:
							{
								num_cia: num_cia,
								num_exp: idexp
							},
							onSuccess: function(text)
							{
								porExp[idexp] = text.getVal();
								f.form.porExp[i].set('value', text.getVal());
							}
						}).send();
					}
				}
				else if (letra_folio == '')
				{
					f.form.dev_exp[i].set('readonly', false);
					
					if (!$defined(porExp[idexp]))
					{
						new Request({
							url: 'RegistroFacturasPastel.php',
							method: 'get',
							async: false,
							data:
							{
								num_cia: num_cia,
								num_exp: idexp
							},
							onSuccess: function(text)
							{
								porExp[idexp] = text.getVal();
								f.form.porExp[i].set('value', text.getVal());
							}
						}).send();
					}
				}
				/*else
				{
					f.form.dev_exp[i].set('readonly', true);
				}*/
			}
		}
		else
		{
			f.form.kilos[i].set('readonly', false);
			f.form.precio_unidad[i].set('readonly', false);
			f.form.base[i].set('readonly', false);
			
			f.form.dev_exp[i].set('readonly', true);
		}
		
		// @@@ Determinar el tipo de factura @@@
		
		// @ Control Amarillo (control 0)
		if (((kilos > 0 && precio_unidad > 0) || otros > 0)
			&& fecha_entrega.length >= 8)
		{
			$('type' + i).setProperty('src', 'imagenes/YellowSheet16x16.png');
			
			// *** Calcular el importe total de la factura ***
			var TotalFactura = (kilos * precio_unidad) + otros + base + pastillaje + otros_efectivos;
			var RestaPagar = TotalFactura - cuenta;
			
			// *** Asignar total de la factura y el resto a pagar ***
			f.form.total_factura[i].set('value', TotalFactura > 0 ? TotalFactura.numberFormat(2, '.', ',') : null);
			f.form.resta_pagar[i].set('value', RestaPagar != 0 ? RestaPagar.numberFormat(2, '.', ',') : null);
			
			if (RestaPagar < 0)
			{
				$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
				$('status' + i).store('tip:text', 'El importe dejado a cuenta no puede ser mayor al valor total de la factura');
				
				// *** Estado -4: El importe dejado a cuenta no puede ser mayor al valor total de la factura ***
				f.form.estado[i].set('value', -4);
			}
			else
			{
				$('status' + i).setProperty('src', 'imagenes/BulletYellow16x16.png');
				$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#FC0;font-weight:bold;">Amarillo</span> \'Venta\'');
				
				f.form.estado[i].set('value', 1);
			}
			
			CalculaTotales();
		}
		// @ Control Verde (control 1)
		else if (resta > 0)
		{
			$('type' + i).setProperty('src', 'imagenes/GreenSheet16x16.png');
			
			// *** Calcular el resto a pagar ***
			var RestaPagar = facStack[i][1][15] - resta;
			f.form.resta_pagar[i].set('value', RestaPagar != 0 ? RestaPagar.numberFormat(2, '.', ',') : null);
			
			if (RestaPagar < 0)
			{
				$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
				$('status' + i).store('tip:text', 'El resto capturado no puede ser mayor a ' + facStack[i][1][15].numberFormat(2, '.', ','));
				
				// *** Estado -5: El resto capturado sobrepasa el costo total de la factura ***
				f.form.estado[i].set('value', -5);
			}
			else
			{
				$('status' + i).setProperty('src', 'imagenes/BulletGreen16x16.png');
				$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#060;font-weight:bold;">Verde</span> \'Resta\'' + DesgloseFactura(i));
				
				f.form.estado[i].set('value', 1);
			}
			
			CalculaTotales();
		}
		// @ Control Azul (control 2)
		else if (dev_base > 0)
		{
			$('type' + i).setProperty('src', 'imagenes/BlueSheet16x16.png');
			
			// *** Validar que el importe de devolución sea el mismo que el cobro de la base
			if (dev_base > 0 && dev_base != facStack[i][1][12])
			{
				$('status' + i).setProperty('src', 'imagenes/BulletRed16x16.png');
				$('status' + i).store('tip:text', 'El importe de devoluci&oacute;n de base debe ser ' + facStack[i][1][12].numberFormat(2, '.', ','));
				
				// *** Estado -6: El importe de devolución difiere del costo de la base ***
				f.form.estado[i].set('value', -6);
			}
			else
			{
				$('status' + i).setProperty('src', 'imagenes/BulletBlue16x16.png');
				$('status' + i).store('tip:text', 'Solo se puede capturar control <span style="color:#00C;font-weight:bold;">Azul</span> \'Devoluci&oacute;n de Base\'' + DesgloseFactura(i));
				
				f.form.estado[i].set('value', 1);
			}
			
			CalculaTotales();
		}
		else
		{
			$('type' + i).setProperty('src', 'imagenes/WhiteSheet16x16.png');
			
			if (facStack[i] != null && f.form.tipo[i].get('value').getVal() != 1)
				CambiaStatus(i, letra_folio, num_remi);
			
			CalculaTotales();
		}
	}
}

function CalculaTotales() {
	var VentaPuerta = 0, AbonoExpendio = 0, Bases = 0, DevBases = 0, PorExp = 0;
	
	$each(f.form.estado, function(est, i)
	{
		if (f.form.idexpendio[i].get('value').getVal() > 0)
		{
			PorExp = porExp[f.form.idexpendio[i].get('value').getVal()] > 0 ? (100 - porExp[f.form.idexpendio[i].get('value').getVal()]) / 100 : 1;
			
			if (f.form.cuenta[i].get('value').getVal() !== false)
			{
				AbonoExpendio += (f.form.cuenta[i].get('value').getVal() - f.form.base[i].get('value').getVal() - f.form.pastillaje[i].get('value').getVal() - f.form.otros_efectivos[i].get('value').getVal() - f.form.dev_exp[i].get('value').getVal()) * PorExp;
			}
			if (f.form.resta[i].get('value').getVal() !== false)
			{
				AbonoExpendio += (f.form.resta[i].get('value').getVal() - f.form.dev_exp[i].get('value').getVal()) * PorExp;
			}
			/*if (f.form.dev_exp[i].get('value').getVal() !== false)
			{
				AbonoExpendio -= f.form.dev_exp[i].get('value').getVal();
			}*/
		}
		else
		{
			VentaPuerta += f.form.cuenta[i].get('value').getVal() - f.form.base[i].get('value').getVal() - f.form.pastillaje[i].get('value').getVal() - f.form.otros_efectivos[i].get('value').getVal();
			VentaPuerta += f.form.resta[i].get('value').getVal();
		}
		Bases += f.form.base[i].get('value').getVal();
		DevBases += f.form.dev_base[i].get('value').getVal();
	});
	
	$('VentaPuerta').set('html', VentaPuerta > 0 ? VentaPuerta.numberFormat(2, '.', ',') : '&nbsp;');
	$('AbonoExpendio').set('html', AbonoExpendio > 0 ? AbonoExpendio.numberFormat(2, '.', ',') : '&nbsp;');
	$('Bases').set('html', Bases > 0 ? Bases.numberFormat(2, '.', ',') : '&nbsp;');
	$('DevBases').set('html', DevBases > 0 ? DevBases.numberFormat(2, '.', ',') : '&nbsp;');
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	var msg;
	
	// *** Validar día y fecha ***
	if (f.form.num_cia.get('value').getVal() == 0)
	{
		alert('Debe capturar el n&uacute;mero de compa&ntilde;&iacute;a');
		f.form.num_cia.select();
		return false;
	}
	else if (f.form.fecha.get('value').length < 8)
	{
		alert('Debe capturar la fecha');
		f.form.fecha.select();
	}
	
	// *** Validar facturas ***
	for (var i = 0; i < f.form.estado.length; i++)
	{
		if (f.form.estado[i].get('value').getVal() < 0)
		{
			msg = '@@ Factura ';
			msg += (f.form.letra_folio[i].get('value') != '' ? f.form.letra_folio[i].get('value') + '-' : '');
			msg += stack[i];
			msg += '\n\n';
			
			switch (f.form.estado[i].get('value').getVal())
			{
				case -1:
					msg += 'La factura esta pagada y tiene devolución de base';
				break;
				
				case -2:
					msg += 'La factura esta pagada y no tiene importe de base';
				break;
				
				case -3:
					msg += 'La factura esta cancelada';
				break;
				
				case -4:
					msg += 'El importe dejado a cuenta no puede ser mayor al importe total de la factura'
					msg += '\n\nTotal Factura = ' + facStack[i][1][5].numberFormat(2, '.', ',');
				break;
				
				case -5:
					msg += 'El resto capturado sobrepasa el costo total de la factura';
					msg += '\n\nTotal Factura = ' + facStack[i][1][5].numberFormat(2, '.', ',');
					msg += '\nResto a Pagar = ' + facStack[i][1][15].numberFormat(2, '.', ',');
				break;
				
				case -6:
					msg += 'El importe de devolución difiere del costo de la base';
					msg += '\n\nCosto de la Base = ' + facStack[i][1][12].numberFormat(2, '.', ',');
				break;
				
				case -7:
					msg += 'El folio de la factura no se encuentra en ningun block dado de alta en el sistema';
				break;
				
				case -8:
					msg += 'La fecha de entrega no puede ser menor a la fecha de captura';
				break;
				
				case -9:
					msg += 'La folio de factura no es el consecutivo de la última nota (factura brincada)';
				break;
			}
			
			alert(msg);
			f.form.num_remi[i].select();
			return false;
		}
		else if (f.form.tipo[i].get('value').getVal() == 1 && f.form.fecha_entrega[i].get('value').getVal().length < 8)
		{
			msg = '@@ Factura ';
			msg += (f.form.letra_folio[i].get('value') != '' ? f.form.letra_folio[i].get('value') + '-' : '');
			msg += stack[i];
			msg += '\n\n';
			msg += 'No capturo la fecha de entrega';
			
			alert(msg);
			f.form.fecha_entrega[i].select();
			return false;
		}
	}
	
	/*
	@ [16-Nov-2012]
	/*/
	remisiones = [];
	ok_all = true;
	var index = 0;
	
	$$('input[id=num_remi]').each(function(el, i) {
		if (el.get('value').getNumericValue() > 0) {
			remisiones[index] = $H({
				num_remi: el.get('value').getNumericValue(),
				letra: $$('input[id=letra_folio]')[i].get('value') == '' ? 'X' : $$('input[id=letra_folio]')[i].get('value'),
			});
			
			index++;
		}
	});
	
	remisiones.each(function(row, i, array) {
		new Request({
			url: 'RegistroFacturasPastel.php',
			data: 'getlast=1&num_cia=' + $('num_cia').get('value') + '&letra=' + row.letra + '&num_remi=' + row.num_remi,
			onRequest: function() {
			},
			onSuccess: function(result) {
				var last = result.getNumericValue()
				
				if (last > 0) {
					if (row.num_remi - last > 1) {
						var saltadas = 0;
						
						for (var j = 1; j <= row.num_remi - last; j++) {
							var ok = false;
							
							array.each(function(r) {
								if (r.num_remi == last + j && r.letra == row.letra) {
									ok = true;
								}
							});
							
							if (!ok) {
								alert('Usted ha saltado notas de remision, se reiniciara toda la captura');
								
								f.form.reset();
								$('nombre_cia').set('html', '');
								$$('#VentaPuerta, #Bases, #DevBases').set('html', '0.00');
								
								ok_all = false;
								
								document.location = 'RegistroFacturasPastel.php';
								
								return false;
							}
						}
					}
				}
			}
		}).send();
	});
	
	if (ok_all) {
		if (confirm('¿Son correctos todos los datos?'))
		{
			f.form.submit();
		}
	}
	
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
<!-- END BLOCK : captura -->
</body>
</html>
