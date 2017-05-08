<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturas de Proveedores Especiales</title>
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
  <div id="titulo"> Facturas de Proveedores Especiales </div>
  <div id="captura" align="center">
    <form action="FacturasProveedoresEspeciales.php" method="post" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th colspan="2" align="left" scope="row">Compa&ntilde;ia</th>
          <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" /><input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="40" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Proveedor</th>
          <td class="linea_on"><input name="num_pro" type="text" class="cap toPosInt alignCenter" id="num_pro" value="{num_pro}" size="1" />
          <input name="nombre_pro" type="text" class="disabled" id="nombre_pro" size="40" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Factura</th>
          <td class="linea_off"><input name="num_fact" type="text" class="cap onlyNumbersAndLetters toUpper" id="num_fact" size="10" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Fecha</th>
          <td class="linea_on"><input name="fecha" type="text" class="cap toDate alignCenter" id="fecha" size="10" maxlength="10" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Gasto</th>
          <td class="linea_off"><input name="codgastos" type="text" class="cap toPosInt alignCenter" id="codgastos" value="{codgastos}" size="1" />
          <input name="desc" type="text" class="disabled" id="desc" size="40" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Concepto</th>
          <td class="linea_on"><input name="concepto" type="text" class="cap toUpper clean" id="concepto" style="width:98%;" value="{concepto}" size="40" maxlength="50" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Tipo</th>
          <td class="linea_off"><select name="tipo_factura" id="tipo_factura">
            <option value="0" selected="selected">FACTURA</option>
            <option value="1">RECIBO HONORARIOS</option>
            <option value="2">RECIBO RENTA</option>
            <option value="3">OTROS</option>
          </select>          </td>
        </tr>
        <tr id="row_anio" style="display:none;">
          <th rowspan="2" align="left" scope="row">Agua</th>
          <th align="left" scope="row">A&ntilde;o</th>
          <td class="linea_on"><input name="anio" type="text" class="cap toPosInt alignCenter" id="anio" size="4" maxlength="4" /></td>
        </tr>
        <tr id="row_bimestre" style="display:none;">
          <th align="left" scope="row">Bimestre</th>
          <td class="linea_off"><input name="bimestre" type="text" class="cap toPosInt alignCenter" id="bimestre" size="1" /></td>
        </tr>
        <tr class="linea_on">
          <td colspan="3" align="left" class="linea_on" scope="row">&nbsp;</td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">Importe</th>
          <td align="right" class="linea_off"><input name="importe" type="text" class="readOnly alignRight Red" id="importe" size="16" /></td>
        </tr>
        <tr>
          <th colspan="2" align="left" scope="row">I.V.A.</th>
          <td align="right" class="linea_on"><span class="linea_off">
            <input name="cap_iva" type="checkbox" class="checkbox" id="cap_iva" value="1" />
            <span style="font-size:6pt;">Capturar</span>&nbsp;&nbsp;&nbsp;&nbsp;
            %
            <input name="piva" type="text" class="cap numPosFormat2 alignRight Red" id="piva" value="16" size="5" />
          </span>&nbsp;=&nbsp;
          <input name="iva" type="text" class="cap numPosFormat2 alignRight Red" id="iva" size="16" /></td></tr>
        <tr>
          <th colspan="2" align="left" scope="row">Total</th>
          <td align="right" class="linea_off"><input name="total" type="text" class="cap numPosFormat2 alignRight fontSize14pt bold Red" id="total" size="16" /></td>
        </tr>
      </table>
      <p>
        <input name="borrar" type="button" class="boton" id="borrar" value="Borrar" />
        &nbsp;&nbsp;
        <input name="capturar" type="button" class="boton" id="capturar" value="Capturar" />
      </p>
    </form>
  </div>
</div>
<!-- START IGNORE -->
<script language="javascript" type="text/javascript">
<!--
var f, r;
var canModify = true;

window.addEvent('domready', function() {
	f = new Formulario('Datos');
	
	$('num_cia').addEvents({
		change: cambiaConcepto.pass([$('num_cia'), $('nombre_cia'), 'c']),
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_pro').select();
			}
		}
	});
	
	$('num_pro').addEvents({
		change: cambiaConcepto.pass([$('num_pro'), $('nombre_pro'), 'p']),
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_fact').select();
			}
		}
	});
	$('num_pro').fireEvent('change');
	$('num_pro').addEvent('change', validarFac);
	
	$('num_fact').addEvents({
		change: validarFac,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('fecha').select();
			}
		}
	});
	
	$('fecha').addEvents({
		change: validarFecha,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('codgastos').select();
			}
		}
	});
	
	$('codgastos').addEvents({
		change: cambiaConcepto.pass([$('codgastos'), $('desc'), 'g']),
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('concepto').select();
			}
		}
	});
	$('codgastos').addEvent('change', mostrarAgua);
	$('codgastos').fireEvent('change');
	
	$('concepto').addEvents({
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				if ($('codgastos').get('value') == '79')
					$('anio').select();
				else {
					if ($('cap_iva').get('checked')) {
						$('iva').select();
					}
					else {
						$('piva').select();
					}
				}
			}
		}
	});
	
	$('anio').addEvents({
		'change': function() {
			validarBim();
		},
		'keydown': function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('bimestre').select();
			}
		}
	});
	
	$('bimestre').addEvents({
		change: function() {
			if (!(this.get('value').toInt() <= 6 || this.get('value').toInt() == 12)) {
				alert('El valor del bimestre debe estar entre 1 y 6 o 12 si es anual');
				this.set('value', '');
				this.focus();
			}
			else {
				validarBim();
			}
		},
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				if ($('cap_iva').get('checked')) {
					$('iva').select();
				}
				else {
					$('piva').select();
				}
			}
		}
	});
	
	$('cap_iva').addEvent('click', function() {
		if (this.get('checked')) {
			$('piva').set('readonly', true);
			$('iva').set('readonly', false);
			$('iva').select();
		}
		else {
			$('piva').set('readonly', false);
			$('iva').set('readonly', true);
			$('piva').select();
		}
	});
	$('cap_iva').addEvent('click', calculaTotal);
	
	$('piva').addEvents({
		change: calculaTotal,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('total').select();
			}
		}
	});
	
	$('iva').addEvents({
		change: calculaTotal,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('total').select();
			}
		}
	});
	$('iva').set('readonly', true);
	
	$('total').addEvents({
		change: calculaTotal,
		keydown: function(e) {
			if (e.key == 'enter') {
				e.stop();
				$('num_cia').select();
			}
		}
	});
	
	$('borrar').addEvent('click', function() {
		if (confirm('¿Limpiar formulario de captura?'))
			f.form.reset();
	});
	
	$('capturar').addEvent('click', validarDatos);
	
	$('num_cia').focus();
});

var cambiaConcepto = function() {
	var clave = arguments[0];
	var concepto = arguments[1];
	var tipo = arguments[2];
	
	if (clave.get('value').getVal() == 0) {
		clave.set('value', '');
		concepto.set('value', '');
	}
	else {
		new Request({
			url: 'FacturasProveedoresEspeciales.php',
			method: 'post',
			data: {
				action: 'retrieve',
				type: tipo,
				clave: clave.get('value')
			},
			onSuccess: function(result) {
				if (result == '') {
					alert('La clave no se encuentra en el catálogo');
					clave.set('value', '');
					concepto.set('value', '');
				}
				else {
					var r = result.split('|');
					
					if (r.length > 1) {
						concepto.set('value', r[0]);
						
						alert(r[1]);
					}
					else {
						concepto.set('value', r[0]);
					}
				}
			}
		}).send();
	}
}

var mostrarAgua = function() {
	if ($('codgastos').get('value') == 79) {
		$('row_anio').setStyle('display', 'table-row');
		$('row_bimestre').setStyle('display', 'table-row');
	}
	else {
		$('row_anio').setStyle('display', 'none');
		$('row_bimestre').setStyle('display', 'none');
	}
}

var validarFac = function() {
	if ($('num_pro').get('value').getVal() > 0 && $('num_fact').get('value').getVal() > 0) {
		new Request({
			url: 'FacturasProveedoresEspeciales.php',
			method: 'post',
			data: {
				action: 'validFac',
				p: $('num_pro').get('value'),
				f: $('num_fact').get('value')
			},
			onSuccess: function(result) {
				if (result != '') {
					var data = result.split('|');
					
					alert('La factura ' + $('num_fact').get('value') + ' ya esta capturada con fecha \'' + data[2] + '\' para la compañía ' + data[0] + ' ' + data[1]);
					
					$('num_fact').set('value', '');
					$('num_fact').focus();
				}
			}
		}).send();
	}
}

var validarFecha = function() {
	if ($('fecha').get('value') != '') {
		new Request({
			url: 'FacturasProveedoresEspeciales.php',
			method: 'post',
			data: {
				action: 'validDate',
				fecha: $('fecha').get('value')
			},
			onSuccess: function(result) {
				if (result == 't') {
					alert('La fecha de la factura no puede ser de meses anteriores debido a que ya se generaron balances');
					$('fecha').set('value', '');
					$('fecha').focus();
				}
			}
		}).send();
	}
}

var validarBim = function() {
	if ($('anio').get('value').getVal() > 0 && $('bimestre').get('value').getVal() > 0) {
		new Request({
			'url': 'FacturasProveedoresEspeciales.php',
			'data': {
				'action': 'validBim',
				'num_cia': $('num_cia').get('value'),
				'anio': $('anio').get('value'),
				'bim': $('bimestre').get('value')
			},
			'onRequest': function() {
			},
			'onSuccess': function(result) {
				if (result == -1) {
					$('anio').set('value', '');
					$('bimestre').set('value', '');
					alert('Una factura de agua del mismo bimestre ya esta capturada en el sistema');
					$('anio').select();
				}
			}
		}).send();
	}
}

var calculaTotal = function() {
	var total = $('total').get('value').getVal();
	
	if ($('cap_iva').get('checked')) {
		var iva = $('iva').get('value').getVal();
		var importe = (total - iva).round(2);
		
		$('importe').set('value', importe.numberFormat(2, '.', ','));
	}
	else {
		var importe = (total / ($('piva').get('value').getVal() / 100 + 1)).round(2);
		var iva = (total - importe).round(2);
		
		$('importe').set('value', importe.numberFormat(2, '.', ','));
		$('iva').set('value', iva.numberFormat(2, '.', ','));
	}
}

var validarDatos = function() {
	if ($('num_cia').get('value').getVal() == 0) {
		alert('Debe especificar la compañía');
		$('num_cia').focus();
	}
	else if ($('num_pro').get('value').getVal() == 0) {
		alert('Debe especificar el proveedor');
		$('num_pro').focus();
	}
	else if ($('num_fact').get('value').getVal() == 0) {
		alert('Debe especificar el número de factura');
		$('num_fact').focus();
	}
	else if ($('fecha').get('value').length < 8) {
		alert('Debe especificar la fecha');
		$('fecha').focus();
	}
	else if ($('codgastos').get('value').getVal() == 0) {
		alert('Debe especificar el código de gasto');
		$('codgastos').focus();
	}
	else if ($('concepto').length == 0) {
		alert('Debe escribir el concepto de la factura');
		$('concepto').focus();
	}
	else if ($('codgastos').get('value') == '79' && $('anio').get('value').getVal() == 0) {
		alert('Para facturas con código 79 AGUA debe especificar el año');
		$('anio').focus();
	}
	else if ($('codgastos').get('value') == '79' && $('bimestre').get('value').getVal() == 0) {
		alert('Para facturas con código 79 AGUA debe especificar el bimestre');
		$('bimestre').focus();
	}
	else if ($('total').get('value').getVal() == 0) {
		alert('El importe de la factura no puede ser cero');
		$('importe').focus();
	}
	else if (confirm('¿Son correctos todos los datos?')) {
		$('Datos').submit();
	}
}
//-->
</script>
<!-- END IGNORE -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
