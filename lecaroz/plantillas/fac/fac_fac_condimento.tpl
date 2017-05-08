<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Facturaci&oacute;n de Condimento</p>
  <form action="fac_fac_condimento.php" method="post" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Kilos</th>
      <th class="tabla" scope="col">Precio</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaCia({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],null,fecha[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,kilos[{i}],num_cia[{i}],kilos[{i}],fecha[{back}],fecha[{next}])" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="kilos[]" type="text" class="rinsert" id="kilos" onfocus="tmp.value=this.value;this.select()" onchange="if(inputFormat(this,2,true))importeFactura({i})" onkeydown="movCursor(event.keyCode,precio[{i}],fecha[{i}],precio[{i}],kilos[{back}],kilos[{next}])" size="8" /></td>
      <td class="tabla"><input name="precio[]" type="text" class="rinsert" id="precio" onfocus="tmp.value=this.value;this.select()" onchange="if(inputFormat(this,2,true))importeFactura({i})" onkeydown="movCursor(event.keyCode,num_cia[{next}],kilos[{i}],null,precio[{back}],precio[{next}])" size="8" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rnombre" id="importe" size="10" readonly="true" /></td>
    </tr>
    <!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="tabla"><input name="total_kilos" type="text" disabled="disabled" class="rnombre" id="total_kilos" value="0.00" size="8" /></th>
      <th class="tabla">&nbsp;</th>
      <th class="tabla"><input name="total" type="text" disabled="disabled" class="rnombre" id="total" value="0.00" size="10" /></th>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Consultar" onclick="document.location='fac_fac_condimento_con.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Precio" onclick="imponerPrecio()" />    
&nbsp;&nbsp;
    <input type="button" class="boton" value="Capturar" onclick="validar()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre[i].value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_fac_condimento.php', 'GET', 'c=' + get_val(f.num_cia[i]) + '&i=' + i, obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (result.length < 2) {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[result].value = f.tmp.value;
		f.num_cia[result].select();
	}
	else
		f.nombre[result[0]].value = result[1];
}

function importeFactura(i) {
	var importe = 0, kilos = 0, precio = 0;
	
	kilos = get_val(f.kilos[i]);
	precio = get_val(f.precio[i])
	if (kilos > 0 && precio > 0) {
		importe = kilos * precio;
		
		f.importe[i].value = numberFormat(importe, 2);
	}
	else
		f.importe[i].value = '';
	
	totales();
}

function totales() {
	var totalKilos = 0, totalFactura = 0;
	
	for (var i = 0; i < f.kilos.length; i++)
		totalKilos += get_val(f.kilos[i]);
	
	for (var i = 0; i < f.importe.length; i++)
		totalFactura += get_val(f.importe[i]);
	
	f.total_kilos.value = numberFormat(totalKilos, 2);
	f.total.value = numberFormat(totalFactura, 2);
}

function imponerPrecio() {
	var importe, kilos, precio, tmp;
	
	do {
		tmp = prompt('Introdusca el precio por kilo de condimento', '12.50');
		precio = get_val2(tmp);
	} while (precio == 0);
	
	if (precio == 0)
		return false;
	
	for (var i = 0; i < f.precio.length; i++) {
		f.precio[i].value = numberFormat(precio, 2);
		kilos = get_val(f.kilos[i]);
		if (kilos > 0) {
			importe = kilos * precio;
		
			f.importe[i].value = numberFormat(importe, 2);
		}
		else
			f.importe[i].value = '';
	}
	
	totales();
	
	f.num_cia[0].select();
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
	else
		f.num_cia[0].select();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = imponerPrecio();
//-->
</script>
</body>
</html>
