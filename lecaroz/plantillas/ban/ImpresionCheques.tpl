<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Impresi&oacute;n de Cheques</title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<!-- START BLOCK : datos -->
<div id="contenedor">
  <div id="titulo"> Impresi&oacute;n de Cheques </div>
  <div id="captura" align="center">
    <form action="ImpresionCheques.php" method="get" name="Datos" id="Datos" class="formulario">
	  <table align="center" class="tabla_captura">
        <tr>
          <th align="left" scope="row">Banco</th>
          <td align="left" class="linea_off">
            <select name="cuenta" id="cuenta">
              <option value="1" selected="selected">BANORTE</option>
              <option value="2">SANTANDER</option>
            </select>
          </td>
        </tr>
        <tr>
          <th align="left" scope="row">Último folio</th>
          <td align="left" class="linea_off"><input name="folio" type="text" class="cap toPosInt" id="folio" onkeyup="if(event.keyCode==13)this.blur();" size="8" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Orden</th>
          <td align="left" class="linea_off"><input name="orden" type="radio" class="checkbox" id="orden" value="-1" checked="checked" />
            Ascendetente
            <input name="orden" type="radio" class="checkbox" id="orden" value="1" />
            Descendente</td>
        </tr>
      </table>
      <p>
        <input type="button" class="boton" value="Siguiente" onclick="validar()" />
      </p>
      <!-- START BLOCK : polizas -->
      <p>
        <!-- START BLOCK : cuenta -->
        {nbsp}<input type="button" class="boton" value="Polizas {banco}" onclick="polizas({cuenta})" />
        <!-- END BLOCK : cuenta -->
      </p>
      <!-- END BLOCK : polizas -->
    </form>
  </div>
</div>
<script type="text/javascript">
<!--
var f;

window.addEvent('domready', function()
{
	f = new Formulario('Datos');
	
	f.form.folio.select();
});

function validar()
{
	if (f.form.folio.value.getVal() <= 0)
	{
		if (!confirm('¿Imprimir documentos en papel-poliza?'))
		{
			f.form.folio.select();
			return false;
		}
	}
	
	f.form.submit();
}

function polizas(cuenta)
{
	if (confirm('¿Desea imprimir las polizas de ' + (cuenta == 1 ? 'Banorte' : 'Santander') + '?'))
	{
		alert('Introduzca papel poliza en la impresora');
		document.location = 'ImpresionCheques.php?polizas=' + cuenta;
	}
}
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<div id="contenedor">
  <div id="titulo"> Impresi&oacute;n de Cheques </div>
  <div id="captura" align="center">
    <form action="ImpresionCheques.php" method="post" name="Listado" class="formulario" id="Listado">
      <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}" />
      <input name="folio" type="hidden" id="folio" value="{folio}" />
      <input name="orden" type="hidden" id="orden" value="{orden}" />
      <table class="tabla_captura">
        <!-- START BLOCK : pro -->
        <tr>
          <th colspan="7" align="left" style="font-size:12pt;" scope="col">{num_pro} {nombre}</th>
        </tr>
        <tr>
          <th><input name="checkBlock[]" type="checkbox" class="checkbox" id="checkBlock" onclick="check(this,{ini},{fin});calculaTotal()" checked="checked" /></th>
          <th>Compa&ntilde;&iacute;a</th>
          <th>Cuenta</th>
          <th>Fecha</th>
          <th>Folio</th>
          <th>Concepto</th>
          <th>Importe</th>
        </tr>
        <!-- START BLOCK : fila -->
        <tr class="{color}">
          <td align="center"><input name="id[]" type="checkbox" class="checkbox" id="id" onclick="if(!this.checked)$$('[id=checkBlock]')[{block}].checked=false;calculaTotal()" value="{id}" checked="checked" />
            <input name="importe[]" type="hidden" id="importe" value="{importe}" /></td>
          <td align="left">{num_cia} {nombre}</td>
          <td align="center">{cuenta}</td>
          <td align="center">{fecha}</td>
          <td align="center">{folio}</td>
          <td align="left">{concepto}</td>
          <td align="right">{importe}</td>
        </tr>
	    <!-- END BLOCK : fila -->
        <tr>
          <th colspan="6" align="right">Total</th>
          <th align="right">{total}</th>
        </tr>
        <tr>
          <td colspan="7">&nbsp;</td>
        </tr>
	    <!-- END BLOCK : pro -->
        <tr>
          <th colspan="6" align="right">Total General</th>
          <th align="right" id="TotalGeneral" style="font-size:12pt;">{gran_total}</th>
        </tr>
        <tr>
          <th colspan="6" align="right">N&uacute;mero de documentos</th>
          <th align="right" id="NumDocs" style="font-size:12pt;">{num_docs}</th>
        </tr>
        <tr>
          <th colspan="7" align="center" id="rango" style="font-size:16pt;">{rango}</th>
        </tr>
      </table>
      <p>
        <input name="back" type="button" class="boton" id="back" value="Regresar" onclick="document.location='ImpresionCheques.php'" />
        &nbsp;&nbsp;
        <input name="print" type="button" class="boton" id="print" value="Imprimir" onclick="imprimir()" />
      </p>
    </form>
  </div>
</div>
<script type="text/javascript">
<!--
window.addEvent('domready', function()
{
	f = new Formulario('Listado');
});

function check(el, ini, fin)
{
	for (var i = ini; i <= fin; i++)
	{
		f.form.id[i].checked = el.checked;
	}
}

function calculaTotal()
{
	var total = 0, cont = 0;
	
	$$(document.getElementsByName('id[]')).each(function(el, i)
	{
		total += el.checked ? (f.form.importe.length == undefined ? f.form.importe.value.getVal() : f.form.importe[i].value.getVal()) : 0;
		cont += el.checked ? 1 : 0;
	});
	
	$('TotalGeneral').set('html', total.numberFormat(2, '.', ','));
	$('NumDocs').set('html', cont.numberFormat(0, '', ','));
	
	if (cont == 0)
	{
		$('rango').set('html', 'Debe seleccionar al menos un registro');
		f.form.print.disabled = true;
	}
	else if (f.form.folio.value.getVal() > 0)
	{
		$('rango').set('html', 'Inserte cheques en la impresora del folio ' + f.form.folio.value + ' al ' + (f.form.folio.value.getVal() + cont - 1));
		f.form.print.disabled = false;
	}
	else
	{
		$('rango').set('html', 'Inserte papel poliza en la impresora');
		f.form.print.disabled = false;
	}
}

function imprimir()
{
	if (confirm('¿Desea imprimir los documentos seleccionados?'))
		f.form.submit();
}
//-->
</script>
<!-- END BLOCK : result -->
<!-- START BLOCK : estatus -->
<div id="contenedor">
  <div id="titulo">
    Estatus de Impresi&oacute;n de Cheques
  </div>
  <div id="captura" align="center">
    <table class="tabla_captura">
      <tr>
        <th scope="col">Fecha y Hora</th>
        <th scope="col">Usuario</th>
        <th scope="col">Banco</th>
        <th scope="col">Rango</th>
        <th scope="col">Orden</th>
      </tr>
      <tr style="font-size:12pt;font-weight:bold;">
        <td align="center">{ts}</td>
        <td align="center">{usuario}</td>
        <td align="center">{banco}</td>
        <td align="center">{rango}</td>
        <td align="center">{orden}</td>
      </tr>
    </table>
    <br />
    <form action="ImpresionCheques.php" method="get" name="EstatusCheques" class="formulario" id="EstatusCheques">
      <input name="idstatus" type="hidden" id="idstatus" value="{id}" />
	  <input name="accion" type="hidden" id="accion" value="" />
      <table class="tabla_captura">
        <tr>
          <th><input name="opcion" type="radio" class="checkbox" id="opcion" onclick="Cambiar('Recorrer')" value="shift" checked="checked" /></th>
          <th align="left">Recorrer</th>
        </tr>
        <tr>
          <th><input name="opcion" type="radio" class="checkbox" id="opcion" onclick="Cambiar('Reimprimir')" value="print" /></th>
          <th align="left">Reimprimir</th>
        </tr>
      </table>
      <p>
        <input type="button" class="boton" value="Terminar" onclick="Terminar()" />
        &nbsp;&nbsp;
        <input name="accion" type="button" id="accion" class="boton" value="Recorrer" onclick="Accion()" />
		<!-- START BLOCK : cartas_nomina -->
		&nbsp;&nbsp;
        <input name="cartas" type="button" id="cartas" class="boton" value="Cartas de n&oacute;mina" onclick="CartaSolicitudRemesa({id}, '{firma}')" />
		<!-- END BLOCK : cartas_nomina -->
      </p>
	</form>
  </div>
</div>
<script language="javascript" type="application/javascript">
<!--
window.addEvent('domready', function()
{
	f = new Formulario('EstatusCheques');
	
	/*
	@ [09-Mar-2010] Imprimir cartas para gastos de nómina
	*/
	if ($defined($('cartas'))) {
		CartaSolicitudRemesa($('idstatus').get('value'), '{firma}');
	}
});

function CartaSolicitudRemesa(idstatus, firma) {
	var firma = '';
	
	do {
		firma = prompt('Proporcione el nombre de la persona que firmara las cartas de n' + String.fromCharCode(243) + 'mina', firma).trim().clean();
	}
	while (firma == '' || firma == null);
	
	var url = 'CartaSolicitudRemesa.php?idstatus=' + idstatus + '&firma=' + firma;
	var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=200';
	
	var win = window.open(url, 'cartas_nomina', opt);
	win.focus();
}

function Cambiar(leyenda) {
	$('boton').value = leyenda;
}

function Terminar()
{
	if (confirm('¿El proceso de impresión termino correctamente?'))
	{
		$('accion').value = 'finish';
		$('EstatusCheques').submit();
	}
}

function Accion()
{
	$('accion').value = $('opcion').value;
	$('EstatusCheques').submit();
}
//-->
</script>
<!-- END BLOCK : estatus -->
<!-- START BLOCK : recorrer -->
<div id="contenedor">
  <div id="titulo">
    Recorrer Cheques
  </div>
  <div id="captura" align="center">
    <table class="tabla_captura">
      <tr>
        <th scope="col">Fecha y Hora</th>
        <th scope="col">Usuario</th>
        <th scope="col">Banco</th>
        <th scope="col">Rango</th>
        <th scope="col">Orden</th>
      </tr>
      <tr style="font-size:12pt;font-weight:bold;">
        <td align="center">{ts}</td>
        <td align="center">{usuario}</td>
        <td align="center">{banco}</td>
        <td align="center">{rango}</td>
        <td align="center">{orden}</td>
      </tr>
    </table>
    <br />
	<form action="ImpresionCheques.php" method="post" name="RecorrerCheques" class="formulario" id="RecorrerCheques">
	  <input name="idstatus" type="hidden" id="idstatus" value="{id}" />
	  <input name="folio_ini" type="hidden" id="folio_ini" value="{folio_ini}" />
	  <input name="folio_fin" type="hidden" id="folio_fin" value="{folio_fin}" />
	  <input name="orden" type="hidden" id="orden" value="{orden}" />
	  <table class="tabla_captura">
        <tr>
          <th scope="row">Cantidad de Folios </th>
          <td><input name="num_folios" type="text" id="num_folios" class="cap toPosInt alignCenter" size="8" /></td>
        </tr>
      </table>
	  <div style="display:none;">
        <table class="tabla_captura">
          <tr>
            <th scope="col">Folios</th>
          </tr>
          <tr>
            <td align="center"><input name="folio_copy" type="text" class="cap toPosInt alignCenter" id="folio_copy" size="10" /></td>
          </tr>
        </table>
	  </div>
	  <div id="Folios">
	  </div>
	  <input name="recorrer" type="button" class="boton" id="recorrer" value="Recorrer" />
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript">
var f;

window.addEvent('domready', function() {
	f = new Formulario('RecorrerCheques');
	
	$('num_folios').addEvent('change', function() {
		if (this.value.getVal() == 0) {
			$('Folios').set('html', '');
			$('num_folios').set('value', '');
		}
		else {
			$('Folios').set('html', '');
			
			if (this.value.getVal() > ($('folio_fin').get('value').toInt() - $('folio_ini').get('value').toInt() + 1)) {
				alert('El número de folios a recorrer no puede ser mayor a ' + ($('folio_fin').get('value').toInt() - $('folio_ini').get('value').toInt() + 1));
				$('num_folios').set('value', '');
				$('num_folios').focus();
				return false;
			}
			
			var TABLE = new Element('table', {
				'class': 'tabla_captura'
			});
			var TR = new Element('tr');
			var TH = new Element('th', {
				text: 'Folios'
			});
			
			TH.inject(TR);
			TR.inject(TABLE);
			
			for (var i = 0; i < this.value.getVal(); i++) {
				var TR = new Element('tr', {
					'class': i % 2 == 0 ? 'linea_off' : 'linea_on'
				});
				var TD = new Element('td');
				var INPUT = $('folio_copy').clone();
				INPUT.cloneEvents($('folio_copy'));
				INPUT.addEvent('change', function() {
					if (this.get('value').toInt() > 0 && this.get('value').toInt() < $('folio_ini').get('value').toInt() || this.get('value').toInt() > $('folio_fin').get('value').toInt()) {
						alert('El número de folio debe de estar entre ' + $('folio_ini').get('value') + ' y ' + $('folio_fin').get('value'));
						this.set('value', '');
						return false;
					}
				});
				INPUT.set({
					id: 'folio',
					name: 'folio[]',
					onkeydown: 'if(f.form.folio.length!=undefined&&event.keyCode==13)f.form.folio[' + (i < this.value.getVal() - 1 ? i + 1 : 0) + '].select()'
				});
				INPUT.inject(TD);
				TD.inject(TR);
				TR.inject(TABLE);
			}
			
			TABLE.inject($('Folios'));
			
			if (this.value.getVal() > 1)
				f.form.folio[0].select();
			else
				f.form.folio.select();
		}
	});
	
	$('recorrer').addEvent('click', function() {
		if ($('num_folios').get('value').toInt() == 0) {
			alert('No hay folios por recorrer');
			$('num_folios').focus();
			return false;
		}
		else if (confirm('¿Son correctos los datos?'))
			f.form.submit();
	});
	
	$('num_folios').focus();
});
</script>
<!-- END BLOCK : recorrer -->
<!-- START BLOCK : reimprimir -->
<div id="contenedor">
  <div id="titulo">
    Reimprimir Cheques
  </div>
  <div id="captura">
    <table class="tabla_captura">
      <tr>
        <th scope="col">Fecha y Hora</th>
        <th scope="col">Usuario</th>
        <th scope="col">Banco</th>
        <th scope="col">Rango</th>
        <th scope="col">Orden</th>
      </tr>
      <tr style="font-size:12pt;font-weight:bold;">
        <td align="center">{ts}</td>
        <td align="center">{usuario}</td>
        <td align="center">{banco}</td>
        <td align="center">{rango}</td>
        <td align="center">{orden}</td>
      </tr>
    </table>
    <br />
  </div>
</div>
<!-- END BLOCK : reimprimir -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
