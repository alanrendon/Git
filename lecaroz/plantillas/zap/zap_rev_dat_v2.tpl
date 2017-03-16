<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : cias -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Validaci&oacute;n de Datos </p>
  <form action="zap_rev_dat_v2.php" method="post" name="form">
    <input name="action" type="hidden" id="action" value="hoja">
    <input name="tmp" type="hidden" id="tmp">  
    <table class="tabla">
  <tr>
    <th class="tabla" scope="col">Usuario</th>
  </tr>
  <tr>
    <td class="tabla" style="font-size:14pt; font-weight:bold;">{usuario}</td>
  </tr>
</table>

  <br>
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla">Compa&ntilde;&iacute;a</th>
      <th class="tabla">Fecha</th>
    </tr>
    <!-- START BLOCK : cia -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="opt" type="radio" value="{opt}" onClick="next.disabled=false;tmp.value=this.value"{disabled}></td>
      <td class="vtabla" style="font-size:12pt; font-weight:bold;">{num_cia} - {nombre} </td>
      <td class="tabla" style="font-size:12pt; font-weight:bold;">{fecha}</td>
    </tr>
	<!-- START BLOCK : void -->
	<tr>
      <td colspan="3" class="tabla" style="font-size:12pt; font-weight:bold;">&nbsp;</td>
      </tr>
	<!-- END BLOCK : void -->
	<!-- END BLOCK : cia -->
	<!-- END BLOCK : no_cias -->
	<tr>
      <td colspan="3" class="tabla" style="font-size:12pt; font-weight:bold;">No se ha recibido nueva informaci&oacute;n por parte de las zapaterias </td>
      </tr>
	  <!-- END BLOCK : no_cias -->
  </table>  
  <p>
    <input name="next" type="button" disabled="true" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	var tmp = f.tmp.value.split('|');
	var url = './zap_rev_dat_v2.php?action=hoja&num_cia=' + tmp[0] + '&fecha=' + escape(tmp[1]) + '&dir=r';
	document.location = url;
}
//-->
</script>
<!-- END BLOCK : cias -->
<!-- START BLOCK : hoja -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
  <p class="title">Hoja de Datos </p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <table width="100%%" >
    <tr>
      <td width="45%"><table align="center" class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Venta del D&iacute;a </th>
      </tr>
    <!-- START BLOCK : bloque_venta -->
	<tr>
      <th class="tabla" scope="col">Tipo</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : venta_row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla">{tipo}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : venta_row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th class="rtabla">Subtotal</th>
      <th class="rtabla">{subtotal}</th>
    </tr>
	<!-- END BLOCK : bloque_venta -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="2" class="tabla">&nbsp;</td>
	  </tr>
	<!-- START BLOCK : esquilmos -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th colspan="2" class="tabla">Esquilmos u otros Ingresos </th>
	  </tr>
	<!-- START BLOCK : esquilmo -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="vtabla">{con}</td>
	  <td class="rtabla">{importe}</td>
	  </tr>
	  <!-- END BLOCK : esquilmo -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <th class="rtabla">Subtotal</th>
	  <th class="rtabla">{subtotal}</th>
	  </tr>
	  <!-- END BLOCK : esquilmos -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td colspan="2" class="vtabla">&nbsp;</td>
	  </tr>
    <tr>
      <th class="rtabla">Total</th>
      <th class="rtabla" style="font-size:12pt;">{total_venta}</th>
    </tr>
  </table></td>
      <td width="5%" align="center" valign="middle"><table class="tabla">
        <tr>
          <th class="tabla" scope="col">Venta Total </th>
          <th class="tabla" scope="col">Total General </th>
        </tr>
        <tr>
          <td class="tabla" style="font-size:14pt; font-weight:bold;">{venta_total}</td>
          <td class="tabla" style="font-size:14pt; font-weight:bold;">{venta_gral}</td>
        </tr>
      </table></td>
      <td width="45%"><table align="center" class="tabla">
        <tr>
          <th colspan="2" class="tabla" scope="col">Gastos</th>
          </tr>
        <tr>
          <th class="tabla" scope="col">Concepto</th>
          <th class="tabla" scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : gasto_hoja -->
		<tr>
          <td class="vtabla">{concepto}</td>
          <td class="rtabla">{importe}</td>
        </tr>
		<!-- END BLOCK : gasto_hoja -->
        <tr>
          <th class="rtabla">Total</th>
          <th class="rtabla" style="font-size:12pt;">{total_gastos}</th>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center" valign="middle">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><table align="center" class="tabla">
  <tr>
    <th colspan="12" class="tabla" scope="col">N&oacute;mina</th>
    </tr>
  <tr>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">S. Diario </th>
    <th class="tabla" scope="col">L</th>
    <th class="tabla" scope="col">M</th>
    <th class="tabla" scope="col">M</th>
    <th class="tabla" scope="col">J</th>
    <th class="tabla" scope="col">V</th>
    <th class="tabla" scope="col">S</th>
    <th class="tabla" scope="col">D</th>
    <th class="tabla" scope="col">Subtotal </th>
    <th class="tabla" scope="col">Comisi&oacute;n</th>
    <th class="tabla" scope="col">Total</th>
  </tr>
  <!-- START BLOCK : nom_r -->
  <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
    <td class="vtabla">{nombre}</td>
    <td class="rtabla">{sueldo}</td>
    <td class="tabla">{1}</td>
    <td class="tabla">{2}</td>
    <td class="tabla">{4}</td>
    <td class="tabla">{8}</td>
    <td class="tabla">{16}</td>
    <td class="tabla">{32}</td>
    <td class="tabla">{64}</td>
    <td class="rtabla">{subtotal}</td>
    <td class="rtabla">{comision}</td>
    <td class="rtabla" style="font-weight:bold;">{total}</td>
  </tr>
  <!-- END BLOCK : nom_r -->
  <tr>
    <th colspan="11" class="rtabla">Total</th>
    <th class="rtabla" style="font-size:12pt;">{total_nom}</th>
  </tr>
</table></td>
      </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td><table align="center" class="tabla">
    <tr>
      <th colspan="4" class="tabla" scope="col">Acreditados</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Acreditado</th>
    </tr>
    <!-- START BLOCK : acre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla">{nombre}</td>
      <td class="vtabla">{concepto}</td>
      <td class="rtabla">{importe}</td>
      <td class="vtabla">{acreditado}</td>
    </tr>
	<!-- END BLOCK : acre -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla" style="font-size:12pt;">{total_acre}</th>
      <th class="tabla">&nbsp;</th>
    </tr>
  </table></td>
      <td align="center" valign="middle">&nbsp;</td>
      <td><table align="center" class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Intercambios</th>
      </tr>
    <tr>
      <th class="tabla" scope="col">Entradas</th>
      <th class="tabla" scope="col">Salidas</th>
    </tr>
    <!-- START BLOCK : inter -->
	<tr>
      <td class="rtabla">{entrada}</td>
      <td class="rtabla">{salida}</td>
    </tr>
	<!-- END BLOCK : inter -->
    <tr>
      <th class="rtabla">{entradas}</th>
      <th class="rtabla">{salidas}</th>
    </tr>
  </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center" valign="middle">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" align="center"><table class="tabla">
        <tr>
          <th class="tabla" scope="col">Observaciones</th>
        </tr>
        <!-- START BLOCK : obs -->
		<tr>
          <td class="vtabla" style="font-size:12pt;">{obs}</td>
        </tr>
		<!-- END BLOCK : obs -->
      </table></td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center" valign="middle">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat_v2.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="document.location='./zap_rev_dat_v2.php?action=acre&num_cia={num_cia}&fecha={fecha}&dir=r'">
  </p>
  </td>
</tr>
</table>
<!-- END BLOCK : hoja -->
<!-- START BLOCK : acreditados -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Acreditados</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="./zap_rev_dat_v2.tpl" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
	<tr>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Acreditado</th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Concepto</th>
      <th class="tabla" scope="col">Remisiones</th>
    </tr>
	<!-- START BLOCK : acre_row -->
    <tr>
      <td class="rtabla"><input name="id[]" type="hidden" id="id" value="{id}" />
        <strong>{importe}</strong>
        <input name="importe[]" type="hidden" id="importe" value="{importe}" /></td>
      <td class="vtabla"><input name="acre[]" type="text" class="insert" id="acre" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaAcre({i})" onkeydown="movCursor(event.keyCode,num{index},null,num{index},acre{back},acre{next})" value="{acre}" size="3" />
        <input name="nombre_acre[]" type="text" disabled="disabled" class="vnombre" id="nombre_acre" value="{nombre_acre}" size="20" />
        <br />
        <span style="color:#0000CC; font-weight:bold;">{acreditado}</span></td>
      <td class="vtabla"><input name="idnombre[]" type="hidden" id="idnombre" value="{idnombre}" />
        <input name="num[]" type="text" class="insert" id="num" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaNombre({i})" onkeydown="movCursor(event.keyCode,num_fact1{index},acre{index},num_fact1{index},num{back},num{next})" value="{num}" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" value="{nombre}" size="20" />
        <br />
        <span style="color:#0000CC; font-weight:bold;">{nombre_arc}</span></td>
      <td class="vtabla"><strong>{concepto}</strong></td>
      <td class="tabla"><input name="num_fact1[]" type="text" class="insert" id="num_fact1" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) getRem({i},1)" onkeydown="movCursor(event.keyCode,num_fact2{index},num{index},num_fact2{index},num_fact1{back},num_fact1{next})" value="{num_fact1}" size="5" />
        <input name="imp1[]" type="hidden" id="imp1" value="{imp1}" />
        <input name="pag1[]" type="hidden" id="pag1" value="{pag1}" />
        <input name="sal1[]" type="hidden" id="sal1" value="{sal1}" />
        <input name="num_fact2[]" type="text" class="insert" id="num_fact2" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) getRem({i},2)" onkeydown="movCursor(event.keyCode,num_fact3{index},num_fact1{index},num_fact3{index},num_fact2{back},num_fact2{next})" value="{num_fact2}" size="5" />
		<input name="imp2[]" type="hidden" id="imp2" value="{imp2}" />
		<input name="pag2[]" type="hidden" id="pag2" value="{pag2}" />
		<input name="sal2[]" type="hidden" id="sal2" value="{sal2}" />
        <input name="num_fact3[]" type="text" class="insert" id="num_fact3" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) getRem({i},3)" onkeydown="movCursor(event.keyCode,num_fact4{index},num_fact2{index},num_fact4{index},num_fact3{back},num_fact3{next})" value="{num_fact3}" size="5" />
		<input name="imp3[]" type="hidden" id="imp3" value="{imp3}" />
		<input name="pag3[]" type="hidden" id="pag3" value="{pag3}" />
		<input name="sal3[]" type="hidden" id="sal3" value="{sal3}" />
        <input name="num_fact4[]" type="text" class="insert" id="num_fact4" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) getRem({i},4)" onkeydown="movCursor(event.keyCode,acre{next},num_fact3{index},null,num_fact4{back},num_fact4{next})" value="{num_fact4}" size="5" />
		<input name="imp4[]" type="hidden" id="imp4" value="{imp4}" />
		<input name="pag4[]" type="hidden" id="pag4" value="{pag4}" />
		<input name="sal4[]" type="hidden" id="sal4" value="{sal4}" /></td>
    </tr>
	<!-- END BLOCK : acre_row -->
  </table>  
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat_v2.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), nom = new Array();
<!-- START BLOCK : c_acre -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c_acre -->
<!-- START BLOCK : nom -->
nom[{num}] = new Array({id}, '{nombre}');
<!-- END BLOCK : nom -->

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
	var id = f.idnombre.length == undefined ? f.idnombre : f.idnombre[i];
	
	if (num.value == '' || num.value == '0') {
		num.value = '';
		idnombre.value = '';
		nombre.value = '';
	}
	else if (nom[get_val(num)] != null) {
		idnombre.value = nom[get_val(num)][0];
		nombre.value = nom[get_val(num)][1];
	}
	else {
		alert('El nombre no se encuentra en el catálogo');
		num.value = f.tmp.value;
		num.select();
	}
	
	for (var j = 1; j < 4; j++)
		if (f.importe.length == undefined) {
			f.eval('num_fact' + j).value = '';
			f.eval('imp' + j).value = '';
			f.eval('pag' + j).value = '';
			f.eval('sal' + j).value = '';
		}
		else {
			f.eval('num_fact' + j)[i].value = '';
			f.eval('imp' + j)[i].value = '';
			f.eval('pag' + j)[i].value = '';
			f.eval('sal' + j)[i].value = '';
		}
}

function getRem(i, r) {
	var num = f.num.length == undefined ? get_val(f.num) : get_val(f.num[i]);
	var rem = f.eval('num_fact' + r).length == undefined ? get_val(f.eval('num_fact' + r)) : get_val(f.eval('num_fact' + r)[i]);
	
	if (num == 0 || rem == 0) {
		if (f.importe.length == undefined) {
			f.eval('num_fact' + r).value = '';
			f.eval('imp' + r).value = '';
			f.eval('pag' + r).value = '';
			f.eval('sal' + r).value = '';
		}
		else {
			f.eval('num_fact' + r)[i].value = '';
			f.eval('imp' + r)[i].value = '';
			f.eval('pag' + r)[i].value = '';
			f.eval('sal' + r)[i].value = '';
		}
		return false;
	}
	
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');
	
	myConn.connect("./ban_dep_otros_v2.php", "GET", 'num=' + num + '&rem=' + rem + '&i=' + i + '&r=' + r, procRem);
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
		f.eval('num_fact' + data[2] + (f.id.length != undefined ? '[' + get_val2(data[1]) + ']' : '')).value = f.tmp.value;
		f.eval('num_fact' + data[2] + (f.id.length != undefined ? '[' + get_val2(data[1]) + ']' : '')).select();
		return false;
	}
	
	// Importe de la remisión e importe de lo pagado con anterioridad
	f.eval('imp' + data[1] + (f.id.length != undefined ? '[' + get_val2(data[0]) + ']' : '')).value = get_val2(data[2]);
	f.eval('pag' + data[1] + (f.id.length != undefined ? '[' + get_val2(data[0]) + ']' : '')).value = get_val2(data[3]);
	
	// Llamar a función que decuenta remisiones del importe del depósito
	descRem(get_val2(data[0]));
}

function descRem(i) {
	var importe = f.importe.length == undefined ? get_val(f.importe) : get_val(f.importe[i]);
	var imp = new Array(), pag = new Array(), sal = new Array();
	
	// Obtener importe de la remisión y lo pagado con anterioridad
	for (var j = 1; j <= 4; j++) {
		imp[j] = f.importe.length == undefined ? get_val(f.eval('imp' + j)) : get_val(f.eval('imp' + j)[i]);
		pag[j] = f.importe.length == undefined ? get_val(f.eval('pag' + j)) : get_val(f.eval('pag' + j)[i]);
		sal[j] = 0;
	}
	
	for (j = 1; j <= 4; j++)
		if (imp[j] == 0) {
			if (f.importe.length == undefined) f.eval('sal' + j).value = '';
			else f.eval('sal' + j)[i].value = '';
		}
		// Si el importe del depósito es mayor a cero y el importe de la remisión menos lo pagado es menor al depósito, pagar remisión completa
		else if (importe > 0 && imp[j] - pag[j] <= importe) {
			importe -= imp[j] - pag[j];
			if (f.importe.length == undefined) f.eval('sal' + j).value = 0;
			else f.eval('sal' + j)[i].value = 0;
		}
		// Si el importe del depósito no cubre la remisión, descontarlo y calcular el remanente de la remisión
		else if (importe > 0 && imp[j] - pag[j] > importe) {
			sal[j] = imp[j] - pag[j] - importe;
			importe = 0;
			
			if (f.importe.length == undefined) f.eval('sal' + j).value = sal[j];
			else f.eval('sal' + j)[i].value = sal[j];
		}
		// Si el importe del depósito ha llegado a cero, descartar las remisiones sobrantes
		else if (importe <= 0 && imp[j] - pag[j] > 0) {
			alert('El importe del depósito ha llegado a cero y no se puede cubrir el pago de la remision ' + (f.importe.length == undefined ? f.eval('num_fact' + j).value : f.eval('num_fact' + j)[i].value));
			if (f.importe.length == undefined) {
				f.eval('num_fact' + j).value = '';
				f.eval('imp' + j).value = '';
				f.eval('pag' + j).value = '';
				f.eval('sal' + j).value = '';
			}
			else {
				f.eval('num_fact' + j)[i].value = '';
				f.eval('imp' + j)[i].value = '';
				f.eval('pag' + j)[i].value = '';
				f.eval('sal' + j)[i].value = '';
			}
		}
}

function validar(dir) {
	for (var i = 0; i < f.id.length; i++) {
		if (get_val(f.num[i]) > 0 && get_val(f.importe[i]) > Round(Round(get_val(f.imp1[i]) - get_val(f.pag1[i]), 2) + Round(get_val(f.imp2[i]) - get_val(f.pag2[i]), 2) + Round(get_val(f.imp3[i]) - get_val(f.pag3[i]), 2) + Round(get_val(f.imp4[i]) - get_val(f.pag4[i]), 2), 2)) {
			alert('La suma de los importes de las remisiones no cubren el importe del depósito');
			f.importe[i].select();
			return false;
		}
	}
	
	f.action = './zap_rev_dat_v2.php?action=acre_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
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

window.onload = f.id.length == undefined ? f.acre.select() : f.acre[0].select();
//-->
</script>
<!-- END BLOCK : acreditados -->
<!-- START BLOCK : gastos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Gastos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <form method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">&nbsp;</th>
      <th class="tabla" scope="col">Concepto</th>
	  <th class="tabla" scope="col">C&oacute;digo</th>
      <th class="tabla" scope="col">Importe</th>
      </tr>
    <!-- START BLOCK : gas_row -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla">	    <span class="rtabla">
	    <input name="omitir{i}" type="checkbox" id="omitir{i}" value="{id}" {omitir} />
	    </span></td>
	  <td class="vtabla" style="color:#0000CC; font-weight:bold;">{concepto}</td>
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
      <input name="codgastos[]" type="text" class="insert" id="codgastos" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaGasto({i})" onKeyDown="if (codgastos.length == undefined) movCursor(event.keyCode,null,null,null,null,null);
else movCursor(event.keyCode,codgastos[{next}],null,null,codgastos[{back}],codgastos[{next}])" value="{codgastos}" size="3">
        <input name="desc[]" type="text" disabled="true" class="vnombre" id="desc" value="{desc}" size="40" maxlength="255"></td>
      <td class="rtabla" style="font-weight:bold;">{importe}</td>
      </tr>
	<!-- END BLOCK : gas_row -->
	<!-- START BLOCK : gas_pre -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
	  <td class="tabla">&nbsp;</td>
	  <td class="vtabla" style="color:#0000CC; font-weight:bold;">{concepto}</td>
      <td class="vtabla" style="font-weight:bold;">41 - PRESTAMO EMPLEADO </td>
      <td class="rtabla" style="font-weight:bold;">{importe}</td>
      </tr>
	<!-- END BLOCK : gas_pre -->
    <tr>
      <th colspan="3" class="rtabla">Total</th>
      <th class="rtabla" style="font-size:12pt; ">{total}</th>
      </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
&nbsp;&nbsp;
<input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat_v2.php'"> 
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, gasto = new Array();

<!-- START BLOCK : gasto -->
gasto[{codgastos}] = "{desc}";
<!-- END BLOCK : gasto -->

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function cambiaGasto(i) {
	var inputGasto = null, nombreGasto = null;
	
	inputGasto = f.codgastos.length == undefined ? f.codgastos : f.codgastos[i];
	nombreGasto = f.desc.length == undefined ? f.desc : f.desc[i];
	
	if (inputGasto.value == "" || inputGasto.value == "0") {
		inputGasto.value = "";
		nombreGasto.value = "";
	}
	else if (gasto[get_val(inputGasto)] != null)
		nombreGasto.value = gasto[get_val(inputGasto)];
	else {
		alert("El código de gasto no se encuentra en el catálogo");
		inputGasto.value = f.tmp.value;
	}
}

function validar(dir) {
	// Validar que todos los gastos hayan sido codificados
	if (f.codgastos.length == undefined && get_val(f.codgastos) <= 0) {
		alert("Debe códificar todos los gastos");
		f.codgastos.select();
		return false;
	}
	else
		for (var i = 0; i < f.codgastos.length; i++)
			if (get_val(f.codgastos[i]) <= 0) {
				alert("Debe códificar todos los gastos");
				f.codgastos[i].select();

				return false;
			}
	
	f.action = './zap_rev_dat_v2.php?action=gastos_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.codgastos.length == undefined ? f.codgastos.select() : f.codgastos[0].select();
//-->
</script>
<!-- END BLOCK : gastos -->
<!-- START BLOCK : pres -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Prestamos</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <form action="" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cat&aacute;logo
        <input type="button" class="boton" value="Listar" onClick="listar({num_cia})"></th>
      <th class="tabla" scope="col">Nombre</th>
      <th class="tabla" scope="col">Saldo<br>
        Anterior</th>
      <th class="tabla" scope="col">Prestamo</th>
      <th class="tabla" scope="col">Abono</th>
      <th class="tabla" scope="col">Nuevo<br>
        Saldo</th>
    </tr>
	<!-- START BLOCK : pres_row -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="hidden" id="id" value="{id}">
	  <input name="id_emp[]" type="hidden" id="id_emp" value="{id_emp}">
	  <input name="num_emp[]" type="text" class="insert" id="num_emp" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaEmp({i})" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) {
if (num_emp.length == undefined) this.blur();
else num_emp[{next}].select();
}
else if (event.keyCode == 38) {
if (num_emp.length == undefined) this.blur();
else num_emp[{back}].select();
}" value="{num_emp}" size="4">
      <input name="nombre[]" type="text" class="vnombre" id="nombre" value="{nombre_real}" size="30"></td>
      <td class="vtabla" style="font-weight:bold;">{nombre}</td>
      <td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_ini}&nbsp;</td>
      <td class="rtabla" style="color:#CC0000; font-weight:bold;">&nbsp;{cargo}&nbsp;</td>
      <td class="rtabla" style="color:#0000CC; font-weight:bold;">&nbsp;{abono}&nbsp;</td>
      <td class="rtabla" style="font-weight:bold;">&nbsp;{saldo_fin}&nbsp;</td>
    </tr>
	<!-- END BLOCK : pres_row -->
    <tr>
      <th colspan="2" class="rtabla">Total</th>
      <th class="rtabla">{saldo_ini}</th>
      <th class="rtabla" style="color:#CC0000;">{cargos}</th>
      <th class="rtabla" style="color:#0000CC;">{abonos}</th>
      <th class="rtabla">{saldo_fin}</th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="validar('l')">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat_v2.php'">
    &nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente >>" onClick="validar('r')">
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, emp = new Array();
<!-- START BLOCK : emp -->
emp[{num_emp}] = new Array();
emp[{num_emp}][0] = {id_emp};
emp[{num_emp}][1] = "{nombre}";
<!-- END BLOCK : emp -->

function listar(num_cia) {
	var win = window.open("./listar_emp.php?num_cia=" + num_cia,"listar_emp.php?num_cia=" + num_cia,"toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=400,height=400");
	win.focus();
}

function cambiaEmp(i) {
	var num_emp, nombre, id_emp;
	num_emp = f.num_emp.length == undefined ? f.num_emp : f.num_emp[i];
	nombre = f.nombre.length == undefined ? f.nombre : f.nombre[i];
	id_emp = f.id_emp.length == undefined ? f.id_emp : f.id_emp[i];
	
	if (num_emp.value == '' || num_emp.value == '0') {
		num_emp.value = '';
		nombre.value = '';
		id_emp.value = '';
	}
	else if (emp[get_val(num_emp)] != null) {
		id_emp.value = emp[get_val(num_emp)][0];
		nombre.value = emp[get_val(num_emp)][1];
	}
	else {
		alert("El empleado no se encuentra en el catálogo");
		num_emp.value = f.tmp.value;
		num_emp.select();
	}
}

function validar(dir) {
	if (f.num_emp.length == undefined && f.num_emp.value == '') {
		alert('Debe especificar el número de empleado para el movimiento');
		f.num_emp.select();
		return false;
	}
	else
		for (var i = 0; i < f.num_emp.length; i++)
			if (f.num_emp[i].value == '') {
				alert('Debe especificar el número de empleado para el movimiento');
				f.num_emp[i].select();
				return false;
			}
	
	f.action = './zap_rev_dat_v2.php?action=pres_mod&num_cia={num_cia}&fecha={fecha}&dir=' + dir;
	f.submit();
}

window.onload = f.num_emp.length == undefined ? f.num_emp.select() : f.num_emp[0].select();
//-->
</script>
<!-- END BLOCK : pres -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{num_cia} - {nombre_cia} </td>
      <td class="tabla" style="font-size:16pt; font-weight:bold;">{_fecha}</td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row" style="font-size:16pt;">Venta </th>
      <td class="rtabla" style="font-size:16pt; font-weight:bold; color:#0000CC;">{venta}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size:16pt;">Gastos</th>
      <td class="rtabla" style="font-size:16pt; font-weight:bold; color:#CC0000;">{gastos}</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row" style="font-size:16pt;">Efectivo</th>
      <th class="rtabla" style="font-size:20pt;">{efectivo}</th>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="<< Regresar" onClick="document.location='./zap_rev_dat_v2.php?action=pres&num_cia={num_cia}&fecha={fecha}&dir=l'">
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Inicio" onClick="document.location='./zap_rev_dat_v2.php'">
    &nbsp;&nbsp;    
<input name="terminar" type="button" class="boton" id="terminar" onClick="validar(this)" value="Terminar">
  </p></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(boton) {
	var url = './zap_rev_dat_v2.php?action=finish&num_cia={num_cia}&fecha={fecha}&dir=r';
	
	if (confirm("¿Todos los datos son correctos?")) {
		boton.disabled = true;
		document.location = url;
	}
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
