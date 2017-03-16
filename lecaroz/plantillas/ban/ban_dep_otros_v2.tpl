<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Otros Dep&oacute;sitos</p>
  <form action="./ban_dep_otros_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) filas.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Filas</th>
      <td class="vtabla"><input name="filas" type="text" class="insert" id="filas" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) anio.select()" value="{filas}" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Acumulado</th>
      <td class="vtabla"><input name="tipo" type="radio" value="acumulado" checked="checked" />
        Acumulado
          <input name="tipo" type="radio" value="nuevo" />
          Nuevo</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Pantalla</th>
      <td class="vtabla"><input name="screen" type="radio" value="captura" checked="checked" />
        Captura
          <input name="screen" type="radio" value="listado" />
          Listado</td>
    </tr>
    <tr id="lstNombre">
      <th class="vtabla" scope="row">Nombre</th>
      <td class="vtabla"><select name="nombre" class="insert" id="nombre">
        <option value="" selected="selected"></option>
        <!-- START BLOCK : n -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : n -->
      </select>
      </td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (get_val(f.anio) == 0) {
		alert('Debe especificar el año');
		f.anio.select();
	}
	else if (get_val(f.filas) == 0) {
		alert('Debe especificar el número de filas de la captura');
		f.filas.select();
	}
	else
		f.submit();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Otros Dep&oacute;sitos</p>
  <form action="./ban_dep_otros_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="mes" type="hidden" id="mes" value="{mes}" />
    <input name="anio" type="hidden" id="anio" value="{anio}" />
    <input name="con" type="hidden" id="con" value="{con}" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{mes_escrito}</td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{anio}</td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
	<tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">D&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Acreditado</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Remisiones</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,dia{index},null,dia{index},num_cia{back},num_cia{next})" value="{num_cia}" size="3" />
        <input name="nombre_cia[]" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="20" /></td>
      <td class="tabla"><input name="dia[]" type="text" class="insert" id="dia" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,importe{index},num_cia{index},importe{index},dia{back},{next})" value="{dia}" size="2" maxlength="2" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2,false);if(get_val(num[{i}])&gt;0)descRem({i})" onkeydown="movCursor(event.keyCode,acre{index},dia{index},acre{index},importe{back},importe{next})" value="{importe}" size="8" /></td>
      <td class="tabla"><input name="acre[]" type="text" class="insert" id="acre" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaAcre({i})" onkeydown="movCursor(event.keyCode,num{index},importe{index},num{index},acre{back},acre{next})" value="{acre}" size="3" />
        <input name="nombre_acre[]" type="text" disabled="disabled" class="vnombre" id="nombre_acre" value="{nombre_acre}" size="20" /></td>
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}" />
        <input name="num[]" type="text" class="insert" id="num" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaNombre({i})" onkeydown="movCursor(event.keyCode,concepto{index},acre{index},concepto{index},num{back},num{next})" value="{num}" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" value="{nombre}" size="20" /></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onkeydown="movCursor(event.keyCode,get_val(num{index})&gt;0?num_fact1{index}:num_cia{next},num{index},get_val(num{index})&gt;0?num_fact1{index}:null,concepto{back},concepto{next})" value="{concepto}" size="20" maxlength="100" /></td>
      <td class="tabla"><input name="num_fact1[]" type="text" class="insert" id="num_fact1" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();if (this.value.trim() != '') getRem({i},1)" onkeydown="movCursor(event.keyCode,num_fact2{index},concepto{index},num_fact2{index},num_fact1{back},num_fact1{next})" value="{num_fact1}" size="5" />
        <input name="imp1[]" type="hidden" id="imp1" value="{imp1}" />
        <input name="pag1[]" type="hidden" id="pag1" value="{pag1}" />
        <input name="sal1[]" type="hidden" id="sal1" value="{sal1}" />
        <input name="num_fact2[]" type="text" class="insert" id="num_fact2" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();if (this.value.trim() != '') getRem({i},2)" onkeydown="movCursor(event.keyCode,num_fact3{index},num_fact1{index},num_fact3{index},num_fact2{back},num_fact2{next})" value="{num_fact2}" size="5" />
		<input name="imp2[]" type="hidden" id="imp2" value="{imp2}" />
		<input name="pag2[]" type="hidden" id="pag2" value="{pag2}" />
		<input name="sal2[]" type="hidden" id="sal2" value="{sal2}" />
        <input name="num_fact3[]" type="text" class="insert" id="num_fact3" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();if (this.value.trim() != '') getRem({i},3)" onkeydown="movCursor(event.keyCode,num_fact4{index},num_fact2{index},num_fact4{index},num_fact3{back},num_fact3{next})" value="{num_fact3}" size="5" />
		<input name="imp3[]" type="hidden" id="imp3" value="{imp3}" />
		<input name="pag3[]" type="hidden" id="pag3" value="{pag3}" />
		<input name="sal3[]" type="hidden" id="sal3" value="{sal3}" />
        <input name="num_fact4[]" type="text" class="insert" id="num_fact4" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();if (this.value.trim() != '') getRem({i},4)" onkeydown="movCursor(event.keyCode,num_cia{next},num_fact3{index},null,num_fact4{back},num_fact4{next})" value="{num_fact4}" size="5" />
		<input name="imp4[]" type="hidden" id="imp4" value="{imp4}" />
		<input name="pag4[]" type="hidden" id="pag4" value="{pag4}" />
		<input name="sal4[]" type="hidden" id="sal4" value="{sal4}" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='{cancelar}'" />
    &nbsp;&nbsp;&nbsp;
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), nom = new Array();
<!-- START BLOCK : cia -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : cia -->
<!-- START BLOCK : nom -->
nom[{num}] = new Array({id}, '{nombre}');
<!-- END BLOCK : nom -->

function cambiaCia(i, nombre) {
	var num_cia = f.num_cia.length == undefined ? f.num_cia : f.num_cia[i];
	var nombre_cia = f.nombre_cia.length == undefined ? f.nombre_cia : f.nombre_cia[i];
	
	if (num_cia.value == '' || num_cia.value == '0') {
		num_cia.value = '';
		nombre_cia.value = '';
	}
	else if (cia[get_val(num_cia)] != null)
		nombre_cia.value = cia[get_val(num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		num_cia.value = f.tmp.value;
		num_cia.select();
	}
}

function cambiaAcre(i) {
	var acre = f.acre.length == undefined ? f.acre : f.acre[i];
	var nombre_acre = f.nombre_acre.length == undefined ? f.nombre_acre : f.nombre_acre[i];
	
	if (acre.value == '' || acre.value == '0') {
		acre.value = '';
		nombre_acre.value = '';
	}
	else if (cia[get_val(acre)] != null)
		nombre_acre.value = cia[get_val(acre)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		acre.value = f.tmp.value;
		acre.select();
	}
}

function cambiaNombre(i) {
	var num = f.num.length == undefined ? f.num : f.num[i];
	var nombre = f.nombre.length == undefined ? f.nombre : f.nombre[i];
	var id = f.id.length == undefined ? f.id : f.id[i];
	
	if (num.value == '' || num.value == '0') {
		num.value = '';
		id.value = '';
		nombre.value = '';
	}
	else if (nom[get_val(num)] != null) {
		id.value = nom[get_val(num)][0];
		nombre.value = nom[get_val(num)][1];
	}
	else {
		alert('El nombre no se encuentra en el catálogo');
		num.value = f.tmp.value;
		num.select();
	}
	
	for (var j = 1; j < 4; j++)
		if (f.importe.length == undefined) {
			eval('f.num_fact' + j).value = '';
			eval('f.imp' + j).value = '';
			eval('f.pag' + j).value = '';
			eval('f.sal' + j).value = '';
		}
		else {
			eval('f.num_fact' + j)[i].value = '';
			eval('f.imp' + j)[i].value = '';
			eval('f.pag' + j)[i].value = '';
			eval('f.sal' + j)[i].value = '';
		}
}

function getRem(i, r) {
	var num = f.num.length == undefined ? get_val(f.num) : get_val(f.num[i]);
	var rem = eval('f.num_fact' + r).length == undefined ? eval('f.num_fact' + r).value : eval('f.num_fact' + r)[i].value;
	var anio = get_val(f.anio);
	
	if (num == 0 || rem == '') {
		if (f.importe.length == undefined) {
			eval('f.num_fact' + r).value = '';
			eval('f.imp' + r).value = '';
			eval('f.pag' + r).value = '';
			eval('f.sal' + r).value = '';
		}
		else {
			eval('f.num_fact' + r)[i].value = '';
			eval('f.imp' + r)[i].value = '';
			eval('f.pag' + r)[i].value = '';
			eval('f.sal' + r)[i].value = '';
		}
		return false;
	}
	
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');
	
	myConn.connect("./ban_dep_otros_v2.php", "GET", 'num=' + num + '&rem=' + rem + '&anio=' + anio + '&i=' + i + '&r=' + r, procRem);
}

var procRem = function(oXML) {
	var result = oXML.responseText;
	
	// Obtener componentes del resultado
	var data = result.split('|'), error;
	
	// Gestion de errores
	if (get_val2(data[0]) < 0) {
		// Mostrar mensaje segun sea el error
		switch (get_val2(data[0])) {
			case -1: alert('La factura no existe'); break;
			case -2: alert('Ya esta pagada la factura'); break;
			case -3: alert('No tiene original de factura'); break;
			case -4: alert('El porcentaje de descuento no esta autorizado'); break;
		}
		// Regresar al valor anterior
		eval('f.num_fact' + data[2])[get_val2(data[1])].value = f.tmp.value;
		eval('f.num_fact' + data[2])[get_val2(data[1])].select();
		return false;
	}
	
	// Importe de la remisión e importe de lo pagado con anterioridad
	eval('f.imp' + data[1])[get_val2(data[0])].value = get_val2(data[2]);
	eval('f.pag' + data[1])[get_val2(data[0])].value = get_val2(data[3]);
	
	// Llamar a función que decuenta remisiones del importe del depósito
	descRem(get_val2(data[0]));
}

function descRem(i) {
	var importe = f.importe.length == undefined ? get_val(f.importe) : get_val(f.importe[i]);
	var imp = new Array(), pag = new Array(), sal = new Array();
	
	// Obtener importe de la remisión y lo pagado con anterioridad
	for (var j = 1; j <= 4; j++) {
		imp[j] = f.importe.length == undefined ? get_val(eval('f.imp' + j)) : get_val(eval('f.imp' + j)[i]);
		pag[j] = f.importe.length == undefined ? get_val(eval('f.pag' + j)) : get_val(eval('f.pag' + j)[i]);
		sal[j] = 0;
	}
	
	for (j = 1; j <= 4; j++)
		if (imp[j] == 0) {
			if (f.importe.length == undefined) eval('f.sal' + j).value = '';
			else eval('f.sal' + j)[i].value = '';
		}
		// Si el importe del depósito es mayor a cero y el importe de la remisión menos lo pagado es menor al depósito, pagar remisión completa
		else if (importe > 0 && imp[j] - pag[j] <= importe) {
			importe -= imp[j] - pag[j];
			if (f.importe.length == undefined) eval('f.sal' + j).value = 0;
			else eval('f.sal' + j)[i].value = 0;
		}
		// Si el importe del depósito no cubre la remisión, descontarlo y calcular el remanente de la remisión
		else if (importe > 0 && imp[j] - pag[j] > importe) {
			sal[j] = Round(imp[j] - pag[j] - importe, 2);
			importe = 0;
			
			if (f.importe.length == undefined) eval('f.sal' + j).value = sal[j];
			else eval('f.sal' + j)[i].value = sal[j];
		}
		// Si el importe del depósito ha llegado a cero, descartar las remisiones sobrantes
		else if (importe <= 0 && imp[j] - pag[j] > 0) {
			alert('El importe del depósito ha llegado a cero y no se puede cubrir el pago de la remision ' + (f.importe.length == undefined ? eval('f.num_fact' + j).value : eval('f.num_fact' + j)[i].value));
			if (f.importe.length == undefined) {
				eval('f.num_fact' + j).value = '';
				eval('f.imp' + j).value = '';
				eval('f.pag' + j).value = '';
				eval('f.sal' + j).value = '';
			}
			else {
				eval('f.num_fact' + j)[i].value = '';
				eval('f.imp' + j)[i].value = '';
				eval('f.pag' + j)[i].value = '';
				eval('f.sal' + j)[i].value = '';
			}
		}
}

function validar() {
	var dias = new Array(31, get_val(f.anio) % 4 == 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
	for (var i = 0; i < f.num_cia.length; i++)
		if (get_val(f.num_cia[i]) > 0 && get_val(f.dia[i]) > 0 && get_val(f.importe[i])) {
			// Validar día
			if (get_val(f.dia[i]) > dias[get_val(f.mes) - 1]) {
				alert('El mes solo es de ' + dias[get_val(f.mes)] + ' dias');
				f.dia[i].select();
				return false;
			}
			// Validar en caso de que nombre sea mayor a 0 que la suma total de las remisiones sean igual o mayor al importe del depósito
			else if (get_val(f.num[i]) > 0 && (f.num_fact1[i].value != '' || f.num_fact2[i].value != '' || f.num_fact3[i] != '' || f.num_fact4[i] != '') && get_val(f.importe[i]) > Round(Round(get_val(f.imp1[i]) - get_val(f.pag1[i]), 2) + Round(get_val(f.imp2[i]) - get_val(f.pag2[i]), 2) + Round(get_val(f.imp3[i]) - get_val(f.pag3[i]), 2) + Round(get_val(f.imp4[i]) - get_val(f.pag4[i]), 2), 2)) {
				alert('La suma de los importes de las remisiones no cubren el importe del depósito');
				f.importe[i].select();
				return false;
			}
		}
	
	if (confirm('¿Son correctos todos los datos?'))
		f.submit();
}

function Round(Numero, decimales)
{
	//Convertimos el valor a entero de acuerdo al # de decimales
	var Valor = Numero;
	var ndecimales = Math.pow(10, decimales);
	var Valor = Valor * ndecimales;

	//Redondeamos y luego dividimos por el # de decimales
	Valor = Math.round(Valor);
	Valor = Valor / ndecimales
	return Valor;
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = f.num_cia.length == undefined ? f.num_cia.select() : f.num_cia[0].select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Otros Dep&oacute;sitos Capturados <br>
      al {dia} de {mes} de {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Fecha</th>
    <th class="print" scope="col">Acreditado</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Remisiones</th>
    <th class="print" scope="col">Concepto</th>
    <th class="print" scope="col">Importe</th>
  </tr>
  <!-- START BLOCK : grupo -->
  <!-- START BLOCK : dep -->
  <tr>
    <td class="vprint">{num_cia} {nombre_cia} </td>
    <td class="vprint">{fecha}</td>
    <td class="vprint">{acreditado}</td>
    <td class="vprint">{nombre}</td>
    <td class="vprint">{rem}</td>
    <td class="vprint">{concepto}</td>
    <td class="rprint">{importe}</td>
  </tr>
  <!-- END BLOCK : dep -->
  <!-- START BLOCK : total -->
  <tr>
    <th colspan="6" class="rprint">Total</th>
    <th class="rprint">{total}</th>
  </tr>
  <!-- END BLOCK : total -->
  <tr>
    <td colspan="7" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : grupo -->
  <tr>
    <th colspan="6" class="rprint">Gran Total </th>
    <th class="rprint_total">{gran_total}</th>
  </tr>
  <tr>
    <th colspan="6" class="rprint">Total Mes Anterior </th>
    <th class="rprint_total">{mes_ant}</th>
  </tr>
  <tr>
    <th colspan="6" class="rprint">Total del Mes </th>
    <th class="rprint_total">{mes_act}</th>
  </tr>
</table>
<!-- END BLOCK : listado -->
</body>
</html>
