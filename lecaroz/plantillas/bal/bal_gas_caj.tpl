<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gastos de Caja</title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Gastos de Caja </div>
  <div id="captura" align="center">
    <form action="bal_gas_caj.php" method="post" name="CapturaGastosCaja" id="CapturaGastosCaja" class="formulario">
	  <table class="tabla_captura">
        <tr>
          <th scope="row">Fecha</th>
          <td><input name="fecha" type="text" class="cap toDate alignCenter bold fontSize14pt" id="fecha" onkeydown="movCursor(event.keyCode,0,false,'num_cia',null,null,null,null)" value="{fecha}" size="10" maxlength="10" /></td>
        </tr>
      </table>
      <br />
	  <table class="tabla_captura">
        <tbody id="tablaCaptura">
		<tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Concepto</th>
          <th scope="col">Comentario</th>
          <th scope="col">Balance</th>
          <th scope="col">Tipo</th>
          <th scope="col">Importe</th>
        </tr>
        <tr id="row_copy" style="display:none;">
          <td align="center"><input name="num_cia_copy" type="text" class="cap toPosInt alignRight" id="num_cia_copy" size="1" /></span>
          <input name="nombre_cia_copy" type="text" class="disabled" id="nombre_cia_copy" size="30" /></td>
          <td align="center"><select name="cod_gastos_copy" id="cod_gastos_copy">
            <!-- START BLOCK : gasto -->
			<option value="{id}"{style}>{gasto}</option>
			<!-- END BLOCK : gasto -->
		  </select></td>
          <td align="center"><input name="comentario_copy" type="text" class="cap toUpper" id="comentario_copy" size="25" maxlength="255" /></td>
          <td align="center"><select name="clave_balance_copy" id="clave_balance_copy" style="color:#00C;font-weight:bold;">
            <option value="TRUE" selected="selected" style="color:#00C;">SI</option>
            <option value="FALSE" style="color:#C00;">NO</option>
          </select>          </td>
          <td align="center"><select name="tipo_mov_copy" id="tipo_mov_copy" style="color:#C00;font-weight:bold;">
            <option value="FALSE" selected="selected" style="color:#C00;font-weight:bold;">EGRESO</option>
            <option value="TRUE" style="color:#00C;font-weight:bold;">INGRESO</option>
          </select>          </td>
          <td align="center"><input name="importe_copy" type="text" class="cap numPosFormat2 alignRight" id="importe_copy" size="12" /></td>
        </tr>
        <tr id="extra_copy" style="display:none;">
          <td colspan="6" align="center">
		    <table>
              <tr id="row_header">
                <th scope="col">Compa&ntilde;&iacute;a</th>
                <th scope="col">Proveedor</th>
                <th scope="col">Factura</th>
                <th scope="col">Total</th>
				<th scope="col">Pagado</th>
				<th scope="col">Importe</th>
              </tr>
              <tr class="linea_off">
                <td align="center"><input name="num_cia1_copy" type="text" class="cap toPosInt alignRight" id="num_cia1_copy" size="1" /><input name="nombre_cia1_copy" type="text" class="disabled" id="nombre_cia1_copy" size="30" /></td>
                <td align="center"><input name="num_pro1_copy" type="text" class="cap toPosInt alignRight" id="num_pro1_copy" size="1" /><input name="nombre_pro1_copy" type="text" class="disabled" id="nombre_pro1_copy" size="30" /></td>
                <td align="center"><input name="num_fact1_copy" type="text" class="cap toPosInt alignRight" id="num_fact1_copy" size="10" /></td>
                <td align="center"><input name="total1_copy[]" type="text" class="readOnly Red alignRight" id="total1_copy" size="10" /></td>
				<td align="center"><input name="pagado1_copy" type="text" class="readOnly Blue alignRight" id="pagado1_copy" size="10" /></td>
				<td align="center"><input name="importe1_copy" type="text" class="cap numPosFormat2 alignRight" id="importe1_copy" size="10" /></td>
              </tr>
            </table>
		  </td>
        </tr>
		<tr id="empty">
          <td colspan="6" align="right" style="border-top:double 6px #000;">&nbsp;</td>
          </tr>
        <tr id="total_egresos_row">
          <td colspan="5" align="right" style="font-weight:bold;">Total Egresos </td>
          <td align="center"><input name="total_egresos" type="text" class="disabled bold Red alignRight fontSize12pt" id="total_egresos" style="width:100%;" value="0.00" size="5" /></td>
        </tr>
        <tr id="total_ingresos_row">
          <td colspan="5" align="right" style="font-weight:bold;">Total Ingresos </td>
          <td align="center"><input name="total_ingresos" type="text" class="disabled bold Blue alignRight fontSize12pt" id="total_ingresos" style="width:100%;" value="0.00" size="5" /></td>
        </tr>
        <tr id="total_general_row">
          <td colspan="5" align="right" style="font-weight:bold;">Total General </td>
          <td align="center"><input name="total_general" type="text" class="disabled bold alignRight fontSize12pt" id="total_general" style="width:100%;" value="0.00" size="5" /></td>
        </tr>
		</tbody>
      </table>
      <p>
        <input type="button" class="boton" value="Siguiente" onclick="validar()" />
      </p>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var f, cod_gastos = null, clave_balance = null, tipo_mov = null;

var Table = $('tablaCaptura');
var total_row = $('empty');

window.addEvent('domready', function() {
	f = new Formulario('CapturaGastosCaja');
	
	nuevaFila(0);
	
	new Request({
		url: 'bal_gas_caj.php',
		method: 'get',
		data: {
			list: 1
		},
		onSuccess: function(result)
		{
			if (result != '') {
				var win = window.open('', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
				win.document.writeln(result);
				win.focus();
			}
		}
	}).send();
	
	f.form.fecha.select();
});

function validar() {
	if (f.form.num_cia.length == undefined) {
		if (f.form.cod_gastos.value == 147 && f.form.total_fac.value.getVal() != f.form.importe.value.getVal()) {
			alert('La suma total del pago de las remisiones debe ser igual al importe del gasto');
			f.form.importe.select();
			return false;
		}
	}
	else
		for (var i = 0; i < f.form.num_cia.length; i++)
			if (f.form.cod_gastos[i].value == 147 && f.form.total_fac[i].value.getVal() != f.form.importe[i].value.getVal()) {
				alert('La suma total del pago de las remisiones debe ser igual al importe del gasto');
				f.form.importe[i].select();
				return false;
			}
	
	if (confirm('¿Son correctos los datos?'))
		f.form.submit();
}

var cambiaCia = function() {
	var c = arguments[0];
	var n = arguments[1];
	var i = arguments[2];
	
	var num_cia = eval('f.form.' + c + '.length') == undefined ? eval('f.form.' + c) : eval('f.form.' + c)[i];
	var nombre_cia = eval('f.form.' + n + '.length') == undefined ? eval('f.form.' + n) : eval('f.form.' + n)[i];
	
	if (num_cia.value.getVal() > 0) {
		new Request({
			url: 'bal_gas_caj.php',
			method: 'get',
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('La compañía ' + num_cia.value + ' no se encuentra en el catálogo');
					
					num_cia.value = '';
					nombre_cia.value = '';
				}
				else
					nombre_cia.value = nombre;
			}
		}).send('c=' + num_cia.value.getVal());
	}
	else {
		num_cia.value = null;
		nombre_cia.value = null;
	}
}

var cambiaPro = function() {
	var p = arguments[0];
	var n = arguments[1];
	var i = arguments[2];
	
	var num_pro = eval('f.form.' + p + '.length') == undefined ? eval('f.form.' + p) : eval('f.form.' + p)[i];
	var nombre_pro = eval('f.form.' + n + '.length') == undefined ? eval('f.form.' + n) : eval('f.form.' + n)[i];
	
	if (num_pro.value.getVal() > 0) {
		new Request({
			url: 'bal_gas_caj.php',
			method: 'get',
			onSuccess: function(nombre)
			{
				if (nombre == '') {
					alert('El proveedor ' + num_pro.value + ' no se encuentra en el catálogo');
					
					num_pro.value = '';
					nombre_pro.value = '';
				}
				else if (nombre == '-1') {
					alert('El proveedor ' + num_pro.value + ' no tiene clave de seguridad');
					
					num_pro.value = '';
					nombre_pro.value = '';
				}
				else
					nombre_pro.value = nombre;
			}
		}).send('p=' + num_pro.value.getVal());
	}
	else {
		num_pro.value = null;
		nombre_pro.value = null;
	}
}

function alternarExtra(i, show) {
	$('extra' + i).style.display = show ? 'table-row' : 'none';
	
	var importe = f.form.importe.length == undefined ? f.form.importe : f.form.importe[i];
	
	if (show)
		importe.set('onkeydown', "if(event.keyCode==13&&f.form.num_cia[" + (i + 1) + "] == undefined)nuevaFila(" + (i + 1) + ");movCursor(event.keyCode," + i + ",false,'num_cia1','comentario',null,'importe','importe')");
	else
		importe.set('onkeydown', "if(event.keyCode==13&&f.form.num_cia[" + (i + 1) + "] == undefined)nuevaFila(" + (i + 1) + ");movCursor(event.keyCode," + i + ",true,'num_cia','comentario',null,'importe','importe')");
}

var validarFactura = function() {
	var i = arguments[0];
	var c = arguments[1];
	var nc = arguments[2];
	var p = arguments[3];
	var np = arguments[4];
	var fac = arguments[5];
	var tot = arguments[6];
	var pag = arguments[7];
	var imp = arguments[8];
	
	var num_cia = eval('f.form.' + c + '.length') == undefined ? eval('f.form.' + c) : eval('f.form.' + c)[i];
	var nombre_cia = eval('f.form.' + nc + '.length') == undefined ? eval('f.form.' + nc) : eval('f.form.' + nc)[i];
	var num_pro = eval('f.form.' + p + '.length') == undefined ? eval('f.form.' + p) : eval('f.form.' + p)[i];
	var nombre_pro = eval('f.form.' + np + '.length') == undefined ? eval('f.form.' + np) : eval('f.form.' + np)[i];
	var num_fact = eval('f.form.' + fac + '.length') == undefined ? eval('f.form.' + fac) : eval('f.form.' + fac)[i];
	var total = eval('f.form.' + tot + '.length') == undefined ? eval('f.form.' + tot) : eval('f.form.' + tot)[i];
	var pagado = eval('f.form.' + pag + '.length') == undefined ? eval('f.form.' + pag) : eval('f.form.' + pag)[i];
	var importe = eval('f.form.' + imp + '.length') == undefined ? eval('f.form.' + imp) : eval('f.form.' + imp)[i];
	
	importe.value = '';
	
	if (num_cia.value.getVal() == 0 || num_pro.value.getVal() == 0 || num_fact.value.getVal() == 0) {
		total.value = '';
		pagado.value = '';
		importe.value = '';
		
		totalFac(i);
		
		return false;
	}
	
	new Request({
		url: 'bal_gas_caj.php',
		method: 'get',
		data: {
			c: num_cia.value.getVal(),
			p: num_pro.value.getVal(),
			f: num_fact.value.getVal()
		},
		onSuccess: function(result)
		{
			if (result.getVal() < 0) {
				switch (result.getVal()) {
					case -1:
						alert('La factura ' + num_fact.value + ' no existe para el proveedor ' + num_pro.value + ' ' + nombre_pro.value);
						num_fact.value = '';
						total.value = '';
						pagado.value = '';
						importe.value = '';
						num_fact.focus();
					break;
					case -2:
						alert('La factura ' + num_fact.value + ' del proveedor ' + num_pro.value + ' ' + nombre_pro.value + ' ya esta pagada');
						num_cia.value = '';
						nombre_cia.value = '';
						num_pro.value = '';
						nombre_pro.value = '';
						num_fact.value = '';
						total.value = '';
						pagado.value = '';
						importe.value = '';
						num_cia.focus();
					break;
					case -3:
						alert('La factura ' + num_fact.value + ' existe pero no es de la compañía ' + num_cia.value + ' ' + nombre_cia.value);
						num_cia.value = '';
						nombre_cia.value = '';
						total.value = '';
						pagado.value = '';
						importe.value = '';
						num_cia.focus();
					break;
				}
				
				totalFac(i);
			}
			else {
				var values = result.split('|');
				
				total.value = values[0].getVal().numberFormat(2, '.', ',');
				pagado.value = values[1].getVal().numberFormat(2, '.', ',');
				importe.value = (values[0].getVal() - values[1].getVal()).numberFormat(2, '.', ',');
				
				totalFac(i);
			}
		},
		onFailure: function(xhr) {
			alert('Mori en el intento XP');
		}
	}).send();
}

function totalFac() {
	var i = arguments[0];
	
	var importe1 = eval('f.form.importe1.length') == undefined ? eval('f.form.importe1') : eval('f.form.importe1')[i];
	var importe2 = eval('f.form.importe2.length') == undefined ? eval('f.form.importe2') : eval('f.form.importe2')[i];
	var importe3 = eval('f.form.importe3.length') == undefined ? eval('f.form.importe3') : eval('f.form.importe3')[i];
	var importe4 = eval('f.form.importe4.length') == undefined ? eval('f.form.importe4') : eval('f.form.importe4')[i];
	var total_fac = eval('f.form.total_fac.length') == undefined ? eval('f.form.total_fac') : eval('f.form.total_fac')[i];
	var total = 0;
	
	total = importe1.value.getVal() + importe2.value.getVal() + importe3.value.getVal() + importe4.value.getVal();
	total_fac.value = total.numberFormat(2, '.', ',');
}

function validarImporte() {
	var i = arguments[0];
	var j = arguments[1];
	
	var total = eval('f.form.total' + j + '.length') == undefined ? eval('f.form.total' + j) : eval('f.form.total' + j)[i];
	var pagado = eval('f.form.pagado' + j + '.length') == undefined ? eval('f.form.pagado' + j) : eval('f.form.pagado' + j)[i];
	var importe = eval('f.form.importe' + j + '.length') == undefined ? eval('f.form.importe' + j) : eval('f.form.importe' + j)[i];
	
	if (importe.value.getVal() > total.value.getVal() - pagado.value.getVal()) {
		alert('El importe a pagar no puede ser mayor a ' + (total.value.getVal() - pagado.value.getVal()).numberFormat(2, '.', ','));
		importe.value = (total.value.getVal() - pagado.value.getVal()).numberFormat(2, '.', ',');
		importe.select();
	}
}

function total() {
	var egresos = 0, ingresos = 0, total = 0;
	
	if (f.form.importe.length == undefined) {
		if (f.form.tipo_mov.value == 'FALSE')
			egresos = f.form.importe.value.getVal();
		else
			ingresos = f.form.importe.value.getVal();
	}
	else
		for (var i = 0; i < f.form.importe.length; i++) {
			if (f.form.tipo_mov[i].value == 'FALSE')
				egresos += f.form.importe[i].value.getVal();
			else
				ingresos += f.form.importe[i].value.getVal();
		}
	
	total = ingresos - egresos;
	
	f.form.total_egresos.value = egresos.numberFormat(2, '.', ',');
	f.form.total_ingresos.value = ingresos.numberFormat(2, '.', ',');
	f.form.total_general.value = total.numberFormat(2, '.', ',');
	f.form.total_general.style.color = total != 0 ? (total < 0 ? '#C00' : '#00C') : '#000';
}

function nuevaFila(i) {
	if ($('row' + i) != null)
		return false;
	
	var newRow = new Element('tr', {
		id: 'row' + i,
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
	});
	
	// Celda 1
	var td1 = new Element('td', {
		align: 'center'
	});
	
	// Número de compañía
	var input_num_cia = $('num_cia_copy').clone();
	input_num_cia.cloneEvents($('num_cia_copy'));
	var Cia = cambiaCia.pass(['num_cia', 'nombre_cia', i]);
	input_num_cia.addEvent('change', Cia);
	input_num_cia.set({
		id: 'num_cia',
		name: 'num_cia[]',
		onkeydown: "movCursor(event.keyCode," + i + ",false,'importe',null,'comentario','num_cia','num_cia')"
	});
	
	// Nombre de compañía
	var input_nombre_cia = $('nombre_cia_copy').clone();
	input_nombre_cia.cloneEvents($('nombre_cia_copy'));
	input_nombre_cia.set({
		id: 'nombre_cia',
		name: 'nombre_cia[]',
		disabled: true
	});
	
	input_num_cia.inject(td1);
	input_nombre_cia.inject(td1);
	
	// Celda 2
	var td2 = new Element('td', {
		align: 'center'
	});
	
	var select_cod_gastos = $('cod_gastos_copy').clone();
	select_cod_gastos.cloneEvents($('cod_gastos_copy'));
	select_cod_gastos.set({
		id: 'cod_gastos',
		name: 'cod_gastos[]',
		onchange: "if(this.value==147)alternarExtra(" + i + ",true);else alternarExtra(" + i + ",false)",
		events: {
			change: function() {
				cod_gastos = this.selectedIndex;
			}
		}
	});
	
	select_cod_gastos.inject(td2);
	
	// Celda 3
	var td3 = new Element('td', {
		align: 'center'
	});
	
	var input_comentario = $('comentario_copy').clone();
	input_comentario.cloneEvents($('comentario_copy'));
	input_comentario.set({
		id: 'comentario',
		name: 'comentario[]',
		onkeydown: "movCursor(event.keyCode," + i + ",false,'importe','num_cia','importe','comentario','comentario')"
	});
	
	input_comentario.inject(td3);
	
	// Celda 4
	var td4 = new Element('td', {
		align: 'center'
	});
	
	// Balance
	var select_clave_balance = $('clave_balance_copy').clone();
	select_clave_balance.cloneEvents($('clave_balance_copy'));
	select_clave_balance.set({
		id: 'clave_balance',
		name: 'clave_balance[]',
		events: {
			change: function() {
				this.style.color = this.value == 'FALSE' ? '#C00' : '#00C';
				clave_balance = this.selectedIndex;
			}
		}
	});
	
	select_clave_balance.inject(td4);
	
	// Celda 5
	var td5 = new Element('td', {
		align: 'center'
	});
	
	// Tipo
	var select_tipo_mov = $('tipo_mov_copy').clone();
	select_tipo_mov.cloneEvents($('tipo_mov_copy'));
	select_tipo_mov.set({
		id: 'tipo_mov',
		name: 'tipo_mov[]',
		events: {
			change: function() {
				this.style.color = this.value == 'TRUE' ? '#00C' : '#C00';
				tipo_mov = this.selectedIndex;
				
				total();
			}
		}
	});
	
	select_tipo_mov.inject(td5);
	
	// Celda 6
	var td6 = new Element('td', {
		align: 'center'
	});
	
	// Importe
	var input_importe = $('importe_copy').clone();
	input_importe.cloneEvents($('importe_copy'));
	input_importe.set({
		id: 'importe',
		name: 'importe[]',
		onkeydown: "if(event.keyCode==13&&f.form.num_cia[" + (i + 1) + "] == undefined)nuevaFila(" + (i + 1) + ");movCursor(event.keyCode," + i + ",true,'num_cia','comentario',null,'importe','importe')",
		events: {
			change: function() {
				total();
			}
		}
	});
	
	input_importe.inject(td6);
	
	td1.inject(newRow);
	td2.inject(newRow);
	td3.inject(newRow);
	td4.inject(newRow);
	td5.inject(newRow);
	td6.inject(newRow);
	newRow.inject(total_row, 'before');
	
	var newRowExtra = new Element('tr', {
		id: 'extra' + i,
		'class': i % 2 == 0 ? 'linea_off' : 'linea_on',
		styles: {
			display: 'none'
		}
	});
	
	var td = new Element('td', {
		align: 'center',
		colspan: 6,
		styles: {
			'border-bottom': 'solid 1px #000'
		}
	});
	
	var newTable = new Element('table');
	var header = $('row_header').clone();
	header.inject(newTable);
	
	for (var j = 1; j <= 4; j++) {
		var tr = new Element('tr', {
			'class': j % 2 == 0 ? 'linea_on' : 'linea_off'
		});
		
		// Celda 1
		var td1 = new Element('td', {
			align: 'center'
		});
		
		// Número de compañía
		var input_num_cia = $('num_cia1_copy').clone();
		input_num_cia.cloneEvents($('num_cia1_copy'));
		var Cia = cambiaCia.pass(['num_cia' + j, 'nombre_cia' + j, i]);
		input_num_cia.addEvent('change', Cia);
		var Fac = validarFactura.pass([i, 'num_cia' + j, 'nombre_cia' + j, 'num_pro' + j, 'nombre_pro' + j, 'num_fact' + j, 'total' + j, 'pagado' + j, 'importe' + j]);
		input_num_cia.addEvent('change', Fac);
		input_num_cia.set({
			id: 'num_cia' + j,
			name: 'num_cia' + j + '[]',
			onkeydown: "movCursor(event.keyCode," + i + ",false,'num_pro" + j + "',null,'num_pro" + j + "',null,null)"
		});
		
		// Nombre de compañía
		var input_nombre_cia = $('nombre_cia1_copy').clone();
		input_nombre_cia.cloneEvents($('nombre_cia1_copy'));
		input_nombre_cia.set({
			id: 'nombre_cia' + j,
			name: 'nombre_cia' + j + '[]',
			disabled: true
		});
		
		input_num_cia.inject(td1);
		input_nombre_cia.inject(td1);
		
		// Celda 2
		var td2 = new Element('td', {
			align: 'center'
		});
		
		// Número de proveedor
		var input_num_pro = $('num_pro1_copy').clone();
		input_num_pro.cloneEvents($('num_pro1_copy'));
		var Pro = cambiaPro.pass(['num_pro' + j, 'nombre_pro' + j, i]);
		input_num_pro.addEvent('change', Pro);
		input_num_pro.addEvent('change', Fac);
		input_num_pro.set({
			id: 'num_pro' + j,
			name: 'num_pro' + j + '[]',
			onkeydown: "movCursor(event.keyCode," + i + ",false,'num_fact" + j + "','num_cia" + j + "','num_fact" + j + "',null,null)"
		});
		
		// Nombre de proveedor
		var input_nombre_pro = $('nombre_pro1_copy').clone();
		input_nombre_pro.cloneEvents($('nombre_pro1_copy'));
		input_nombre_pro.set({
			id: 'nombre_pro' + j,
			name: 'nombre_pro' + j + '[]',
			disabled: true
		});
		
		input_num_pro.inject(td2);
		input_nombre_pro.inject(td2);
		
		// Celda 3
		var td3 = new Element('td', {
			align: 'center'
		});
		
		// Número de factura
		var input_num_fact = $('num_fact1_copy').clone();
		input_num_fact.cloneEvents($('num_fact1_copy'));
		input_num_fact.addEvent('change', Fac);
		input_num_fact.set({
			id: 'num_fact' + j,
			name: 'num_fact' + j + '[]',
			onkeydown: "movCursor(event.keyCode," + i + ",false,'importe" + j + "','num_pro" + j + "',null,null,null)"
		});
		
		input_num_fact.inject(td3);
		
		// Celda 4
		var td4 = new Element('td', {
			align: 'center'
		});
		
		// Total de factura
		var input_total = $('total1_copy').clone();
		input_total.cloneEvents($('total1_copy'));
		input_total.set({
			id: 'total' + j,
			name: 'total' + j + '[]',
			readonly: true
		});
		
		input_total.inject(td4);
		
		// Celda 5
		var td5 = new Element('td', {
			align: 'center'
		});
		
		// Pagos de factura
		var input_pagado = $('pagado1_copy').clone();
		input_pagado.cloneEvents($('pagado1_copy'));
		input_pagado.set({
			id: 'pagado' + j,
			name: 'pagado' + j + '[]',
			readonly: true
		});
		
		input_pagado.inject(td5);
		
		// Celda 6
		var td6 = new Element('td', {
			align: 'center'
		});
		
		// Importe a pagar
		var input_importe = $('importe1_copy').clone();
		input_importe.cloneEvents($('importe1_copy'));
		var totalFacFunc = totalFac.pass([i]);
		var validarImp = validarImporte.pass([i, j]);
		input_importe.addEvent('change', totalFacFunc);
		input_importe.addEvent('change', validarImp);
		input_importe.set({
			id: 'importe' + j,
			name: 'importe' + j + '[]',
			onkeydown: "movCursor(event.keyCode," + i + ",false,'num_cia" + (j < 4 ? j + 1 : 1) + "','num_fact" + j + "',null,null,null)"
		});
		
		input_importe.inject(td6);
		
		td1.inject(tr);
		td2.inject(tr);
		td3.inject(tr);
		td4.inject(tr);
		td5.inject(tr);
		td6.inject(tr);
		tr.inject(newTable);
	}
	
	var tr = new Element('tr');
	
	// Header 1
	var th1 = new Element('th', {
		align: 'right',
		colspan: 5,
		text: 'Total'
	});
	
	var th2 = new Element('th', {
		align: 'center'
	});
	
	// Total facturas
	var input_total = $('total_general').clone();
	input_total.cloneEvents($('total_general'));
	input_total.set({
		id: 'total_fac',
		name: 'total_fac[]',
		disabled: true
	});
	
	input_total.inject(th2);
	
	th1.inject(tr);
	th2.inject(tr);
	tr.inject(newTable);
	
	newTable.inject(td);
	td.inject(newRowExtra);
	newRowExtra.inject(total_row, 'before');
	
	if (cod_gastos != null) {
		if (f.form.cod_gastos.length == undefined) {
			f.form.cod_gastos.selectedIndex = cod_gastos;
			f.form.cod_gastos.fireEvent('change');
			
			alternarExtra(i, f.form.cod_gastos.value == 147 ? true : false);
		}
		else {
			f.form.cod_gastos[i].selectedIndex = cod_gastos;
			f.form.cod_gastos[i].fireEvent('change');
			
			alternarExtra(i, f.form.cod_gastos[i].value == 147 ? true : false);
		}
	}
	
	if (clave_balance != null) {
		if (f.form.clave_balance.length == undefined) {
			f.form.clave_balance.selectedIndex = clave_balance;
			f.form.clave_balance.fireEvent('change');
		}
		else {
			f.form.clave_balance[i].selectedIndex = clave_balance;
			f.form.clave_balance[i].fireEvent('change');
		}
	}
	
	if (tipo_mov != null) {
		if (f.form.tipo_mov.length == undefined) {
			f.form.tipo_mov.selectedIndex = tipo_mov;
			f.form.tipo_mov.fireEvent('change');
		}
		else {
			f.form.tipo_mov[i].selectedIndex = tipo_mov;
			f.form.tipo_mov[i].fireEvent('change');
		}
	}
}

function movCursor(keyCode, index, next, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null) {
		if (index != null) {
			if (eval('f.form.' + enter + '.length') == undefined)
				eval('f.form.' + enter + '.select()');
			else if (eval('f.form.' + enter + '[' + (next ? index + 1 : index) + ']') != undefined)
				eval('f.form.' + enter + '[' + (next ? index + 1 : index) + '].select()');
		}
		else if ($(enter))
				$(enter).select();
	}
	else if (keyCode == 37 && lt != null) {
		if (index != null) {
			if (eval('f.form.' + lt + '.length') == undefined)
				eval('f.form.' + lt + '.select()');
			else if (eval('f.form.' + lt + '[' + index + ']') != undefined)
				eval('f.form.' + lt + '[' + index + '].select()');
		}
		else if ($(lt))
			$(lt).select();
	}
	else if (keyCode == 39 && rt != null) {
		if (index != null) {
			if (eval('f.form.' + rt + '.length') == undefined)
				eval('f.form.' + rt + '.select()');
			else if (eval('f.form.' + rt + '[' + index + ']') != undefined)
				eval('f.form.' + rt + '[' + index + '].select()');
		}
		else if ($(rt))
			$(rt).select();
	}
	else if (keyCode == 38 && up != null) {
		if (index != null) {
			if (eval('f.form.' + up + '.length') != undefined && eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + ']') != undefined)
				eval('f.form.' + up + '[' + (index > 0 ? index - 1 : eval('f.form.' + up + '.length') - 1) + '].select()');
		}
		else if ($(up))
			$(up).select();
	}
	else if (keyCode == 40 && dn != null) {
		if (index != null) {
			if (eval('f.form.' + dn + '.length') != undefined && eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + ']') != undefined)
				eval('f.form.' + dn + '[' + (index < eval('f.form.' + dn + '.length') - 1 ? index + 1 : 0) + '].select()');
		}
		else if ($(dn))
			$(dn).select();
	}
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
