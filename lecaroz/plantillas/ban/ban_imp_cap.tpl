<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"	height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Distribuci&oacute;n de Impuestos</p>
	<form action="./ban_imp_cap.php" method="post" name="form" target="valid">
		<input name="tmp" type="hidden" id="tmp">
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
			</select></td>
		</tr>
		<tr>
			<th class="vtabla" scope="row">A&ntilde;o</th>
			<td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[0].select()" value="{anio}" size="4" maxlength="4"></td>
		</tr>
	</table>
	<br>	<table class="tabla">
		<tr>
			<th rowspan="2" class="tabla" scope="col">Cia</th>
			<th rowspan="2" class="tabla" scope="col">ISR</th>
			<!-- <th rowspan="2" class="tabla" scope="col">IETU</th> -->
			<!-- <th rowspan="2" class="tabla" scope="col">IEPS</th> -->
			<th rowspan="2" class="tabla" scope="col">IEPS<br>Gravado</th>
			<th rowspan="2" class="tabla" scope="col">IEPS<br>Acreditable</th>
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			ISR<br>
			Renta</th>
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			ISR<br>
			Honorarios</th>
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			Honorarios<br>
			Consejo</th>
			<th rowspan="2" class="tabla" scope="col">Cr&eacute;dito<br>
			al<br>
			Salario</th>
			<th rowspan="2" class="tabla" scope="col">Total<br>
			ISR<br>
			Pagar</th>
		 <!--	<th rowspan="2" class="tabla" scope="col">IDE<br>
				Retenido</th>
			<th rowspan="2" class="tabla" scope="col">ISR<br>
				Acre.<br>
				vs IDE</th>
			<th rowspan="2" class="tabla" scope="col">IDE a<br>
				favor<br>
				dev.</th>
			<th rowspan="2" class="tabla" scope="col">ISR<br>
				Neto a<br>
				Cargo</th> -->
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			IVA<br>
			Honorarios</th>
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			IVA<br>
			Renta</th>
			<th rowspan="2" class="tabla" scope="col">Retenci&oacute;n<br>
			IVA<br>
			Fletes</th>
			<th rowspan="2" class="tabla" scope="col">Total<br>
			Ret IVA<br>
			a Pagar </th>
			<th colspan="2" class="tabla" scope="col">IVA</th>
			<th rowspan="2" class="tabla" scope="col">IVA<br>
				a<br>
				Declarar</th>
		<th rowspan="2" class="tabla" scope="col">Declaraci&oacute;n <br>
				Anual </th>
			<th rowspan="2" class="tabla" scope="col">Acumulado<br>anual</th>
		</tr>
		<tr>
			<th class="tabla" scope="col">Trasladado</th>
			<th class="tabla" scope="col">Acreditable</th>
		</tr>
		<!-- START BLOCK : fila -->
	<tr>
			<td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia({i})" onKeyDown="movInput(event.keyCode,isr[{i}],null,isr[{i}],num_cia[{back}],num_cia[{next}])" value="{num_cia}" size="3"></td>
			<td class="tabla"><input name="isr[]" type="text" class="rinsert" id="isr" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
ISRpago({i});
total('isr');
total('isr_pago');
}" onKeyDown="movInput(event.keyCode,ieps_gravado[{i}],num_cia[{i}],ieps_gravado[{i}],isr[{back}],isr[{next}])" value="{isr}" size="5"></td>
			<!-- <td class="tabla"><input name="ietu[]" type="text" class="rinsert" id="ietu" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) total('ietu',total_ietu)" onKeyDown="movInput(event.keyCode,ieps[{i}],isr[{i}],ieps[{i}],ietu[{back}],ietu[{next}])" value="{ietu}" size="5"></td>
			<td class="tabla"><input name="ieps[]" type="text" class="rinsert" id="ieps" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
				ISRpago({i});
				total('ieps',total_ieps);
				total('isr_pago');
			}" onKeyDown="movInput(event.keyCode,ieps_gravado[{i}],isr[{i}],ieps_gravado[{i}],ieps[{back}],ieps[{next}])" value="{ieps}" size="5"></td> -->
			<td class="tabla"><input name="ieps_gravado[]" type="text" class="rinsert" id="ieps_gravado" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
				ISRpago({i});
				total('ieps_gravado',total_ieps_gravado);
				total('isr_pago');
			}" onKeyDown="movInput(event.keyCode,ieps_excento[{i}],isr[{i}],ieps_excento[{i}],ieps_gravado[{back}],ieps_gravado[{next}])" value="{ieps_gravado}" size="5"></td>
			<td class="tabla"><input name="ieps_excento[]" type="text" class="rinsert" id="ieps_excento" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
				ISRpago({i});
				total('ieps_excento',total_ieps_excento);
				total('isr_pago');
			}" onKeyDown="movInput(event.keyCode,ret_isr_ren[{i}],ieps_gravado[{i}],ret_isr_ren[{i}],ieps_excento[{back}],ieps_excento[{next}])" value="{ieps_excento}" size="5"></td>
			<td class="tabla"><input name="ret_isr_ren[]" type="text" class="rinsert" id="ret_isr_ren" onChange="if (input_format(this,2,false)) {
ISRpago({i});
total('ret_isr_ren');
total('isr_pago');
}" onKeyDown="movInput(event.keyCode,ret_isr_hon[{i}],ieps_excento[{i}],ret_isr_hon[{i}],ret_isr_ren[{back}],ret_isr_ren[{next}])" value="{ret_isr_ren}" size="5"></td>
			<td class="tabla"><input name="ret_isr_hon[]" type="text" class="rinsert" id="ret_isr_hon" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
ISRpago({i});
total('ret_isr_hon');
total('isr_pago');
}" onKeyDown="movInput(event.keyCode,ret_hon_con[{i}],ret_isr_ren[{i}],ret_hon_con[{i}],ret_isr_hon[{back}],ret_isr_hon[{next}])" value="{ret_isr_hon}" size="5"></td>
			<td class="tabla"><input name="ret_hon_con[]" type="text" class="rinsert" id="ret_hon_con" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
ISRpago({i});
total('ret_hon_con');
total('isr_pago');
}" onKeyDown="movInput(event.keyCode,cre_sal[{i}],ret_isr_hon[{i}],cre_sal[{i}],ret_hon_con[{back}],ret_hon_con[{next}])" value="{ret_hon_con}" size="5"></td>
			<td class="tabla"><input name="cre_sal[]" type="text" class="rinsert" id="cre_sal" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
ISRpago({i});
total('cre_sal');
total('isr_pago');
}" onKeyDown="movInput(event.keyCode,ret_iva_hon[{i}],ret_hon_con[{i}],ret_iva_hon[{i}],cre_sal[{back}],cre_sal[{next}])" value="{cred_sal}" size="5"></td>
			<td class="tabla"><input name="isr_pago[]" type="text" class="rnombre" id="isr_pago" value="{isr_pago}" size="5" readonly="true"></td>
			<!-- <td class="tabla"><input name="ide_ret[]" type="text" class="rinsert" id="ide_ret" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) total('ide_ret',total_ide_ret)" onKeyDown="movInput(event.keyCode,isr_acr_ide[{i}],cre_sal[{i}],isr_acr_ide[{i}],ide_ret[{back}],ide_ret[{next}])" value="{ide_ret}" size="5"></td>
			<td class="tabla"><input name="isr_acr_ide[]" type="text" class="rinsert" id="isr_acr_ide" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) total('isr_acr_ide',total_isr_acr_ide)" onKeyDown="movInput(event.keyCode,ide_dev[{i}],ide_ret[{i}],ide_dev[{i}],isr_acr_ide[{back}],isr_acr_ide[{next}])" value="{isr_acr_ide}" size="5"></td>
			<td class="tabla"><input name="ide_dev[]" type="text" class="rinsert" id="ide_dev" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) total('ide_dev',total_ide_dev)" onKeyDown="movInput(event.keyCode,isr_neto[{i}],isr_neto[{i}],isr_acr_ide[{i}],isr_neto[{back}],ide_dev[{next}])" value="{ide_dev}" size="5"></td>
			<td class="tabla"><input name="isr_neto[]" type="text" class="rinsert" id="isr_neto" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) total('isr_neto',total_isr_neto)" onKeyDown="movInput(event.keyCode,ret_iva_hon[{i}],ide_dev[{i}],ret_iva_hon[{i}],ret_iva_hon[{back}],isr_neto[{next}])" value="{isr_neto}" size="5"></td> -->
			<td class="tabla"><input name="ret_iva_hon[]" type="text" class="rinsert" id="ret_iva_hon" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVApago({i});
total('ret_iva_hon');
total('iva_pago');
}" onKeyDown="movInput(event.keyCode,ret_iva_ren[{i}],cre_sal[{i}],ret_iva_ren[{i}],ret_iva_hon[{back}],ret_iva_hon[{next}])" value="{ret_iva_hon}" size="5"></td>
			<td class="tabla"><input name="ret_iva_ren[]" type="text" class="rinsert" id="ret_iva_ren" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVApago({i});
total('ret_iva_ren');
total('iva_pago');
}" onKeyDown="movInput(event.keyCode,ret_iva_fle[{i}],ret_iva_hon[{i}],ret_iva_fle[{i}],ret_iva_ren[{back}],ret_iva_ren[{next}])" value="{ret_iva_ren}" size="5"></td>
			<td class="tabla"><input name="ret_iva_fle[]" type="text" class="rinsert" id="ret_iva_fle" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVApago({i});
total('ret_iva_fle');
total('iva_pago');
}" onKeyDown="movInput(event.keyCode,iva_tras[{i}],ret_iva_ren[{i}],iva_tras[{i}],ret_iva_fle[{back}],ret_iva_fle[{next}])" value="{ret_iva_fle}" size="5"></td>
			<td class="tabla"><input name="iva_pago[]" type="text" class="rnombre" id="iva_pago" value="{iva_pago}" size="5"></td>
			<td class="tabla"><input name="iva_tras[]" type="text" class="rinsert" id="iva_tras" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVAdec({i});
total('iva_tras');
total('iva_dec');
}" onKeyDown="movInput(event.keyCode,iva_acre[{i}],ret_iva_fle[{i}],iva_acre[{i}],iva_tras[{back}],iva_tras[{next}])" value="{iva_tras}" size="5"></td>
			<td class="tabla"><input name="iva_acre[]" type="text" class="rinsert" id="iva_acre" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVAdec({i});
total('iva_acre');
total('iva_dec');
}" onKeyDown="movInput(event.keyCode,dec_anu[{i}],iva_tras[{i}],dec_anu[{i}],iva_acre[{back}],iva_acre[{next}])" value="{iva_acre}" size="5"></td>
			<td class="tabla"><input name="iva_dec[]" type="text" class="rnombre" id="iva_dec" value="{iva_dec}" size="5" readonly="true"></td>
		<td class="tabla"><input name="dec_anu[]" type="text" class="rinsert" id="dec_anu" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,false)) {
IVAdec({i});
total('dec_anu');
total('iva_dec');
}" onKeyDown="movInput(event.keyCode,num_cia[{next}],iva_acre[{i}],null,dec_anu[{back}],dec_anu[{next}])" value="{dec_anu}" size="5"></td>
		<td class="tabla"><input name="acu_anual[]" type="text" class="rnombre" id="acu_anual" value="{acu_anual}" readonly="true" size="7"></td>
		</tr>
	<!-- END BLOCK : fila -->
		<tr>
			<th class="tabla">Totales</th>
			<th class="tabla"><input name="total_isr" type="text" class="rnombre" id="total_isr" value="{total_isr}" size="5" readonly="true"></th>
			<!-- <th class="tabla"><input name="total_ietu" type="text" class="rnombre" id="total_ietu" value="{total_ietu}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ieps" type="text" class="rnombre" id="total_ieps" value="{total_ieps}" size="5" readonly="true"></th> -->
			<th class="tabla"><input name="total_ieps_gravado" type="text" class="rnombre" id="total_ieps_gravado" value="{total_ieps_gravado}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ieps_excento" type="text" class="rnombre" id="total_ieps_excento" value="{total_ieps_excento}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ret_isr_ren" type="text" class="rnombre" id="total_ret_isr_ren" value="{total_ret_isr_ren}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ret_isr_hon" type="text" class="rnombre" id="total_ret_isr_hon" value="{total_ret_isr_hon}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ret_hon_con" type="text" class="rnombre" id="total_ret_hon_con" value="{total_ret_hon_con}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_cre_sal" type="text" class="rnombre" id="total_cre_sal" value="{total_cre_sal}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_isr_pago" type="text" class="rnombre" id="total_isr_pago" value="{total_isr_pago}" size="5" readonly="true"></th>
			<!-- <th class="tabla"><input name="total_ide_ret" type="text" class="rnombre" id="total_ide_ret" value="{total_ide_ret}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_isr_acr_ide" type="text" class="rnombre" id="total_isr_acr_ide" value="{total_isr_acr_ide}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ide_dev" type="text" class="rnombre" id="total_ide_dev" value="{total_ide_dev}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_isr_neto" type="text" class="rnombre" id="total_isr_neto" value="{total_isr_neto}" size="5" readonly="true"></th> -->
			<th class="tabla"><input name="total_ret_iva_hon" type="text" class="rnombre" id="total_ret_iva_hon" value="{total_ret_iva_hon}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ret_iva_ren" type="text" class="rnombre" id="total_ret_iva_ren" value="{total_ret_iva_ren}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_ret_iva_fle" type="text" class="rnombre" id="total_ret_iva_fle" value="{total_ret_iva_fle}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_iva_pago" type="text" class="rnombre" id="total_iva_pago" value="{total_iva_pago}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_iva_tras" type="text" class="rnombre" id="total_iva_tras" value="{total_iva_tras}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_iva_acre" type="text" class="rnombre" id="total_iva_acre" value="{total_iva_acre}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_iva_dec" type="text" class="rnombre" id="total_iva_dec" value="{total_iva_dec}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_dec_anu" type="text" class="rnombre" id="total_dec_anu" value="{total_dec_anu}" size="5" readonly="true"></th>
			<th class="tabla"><input name="total_acu_anual" type="text" class="rnombre" id="total_acu_anual" value="{total_acu_anual}" size="7" readonly="true"></th>
		</tr>
	</table>
	<p>
		<input type="button" class="boton" value="Siguiente" onClick="validar()">
	</p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre_corto}";
<!-- END BLOCK : cia -->

var mostrarDatos = function (oXML) {
	var data = oXML.responseText;

	if (data == "")
		return false;

	var responseArray = data.split('||'), tmp;

	for (var i = 0; i < responseArray.length; i++)
		if (responseArray[i].length > 0) {
			tmp = responseArray[i].split('|');
			eval('f.' + tmp[0]).value = get_val2(tmp[1]) != 0 ? number_format(get_val2(tmp[1]), 2) : "";
			if (tmp[0].substr(0, tmp[0].indexOf('[')) == "isr_pago" || tmp[0].substr(0, tmp[0].indexOf('[')) == "iva_dec" || tmp[0].substr(0, tmp[0].indexOf('[')) == "acu_anual")
				eval('f.' + tmp[0]).style.color = get_val2(tmp[1]) < 0 ? "CC0000" : "000000";
			total(tmp[0].substr(0, tmp[0].indexOf('[')));
		}
}

function pedirDatos(i) {
	var myConn = new XHConn();

	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

	if (get_val(f.num_cia[i]) > 0 && get_val(f.mes) > 0 && get_val(f.anio) > 0) {
		// Pedir datos
		myConn.connect("./ban_imp_cap.php", "GET", "num_cia=" + get_val(f.num_cia[i]) + "&mes=" + get_val(f.mes) + "&anio=" + get_val(f.anio) + "&i=" + i, mostrarDatos);
	}
}

function cambiaCia(i) {
	if (f.num_cia[i].value == "" || f.num_cia[i].value == "0") {
		f.num_cia[i].value = "";
		f.isr[i].value = "";
		// f.ietu[i].value = "";
		// f.ieps[i].value = "";
		f.ieps_gravado[i].value = "";
		f.ieps_excento[i].value = "";
		f.ret_isr_ren[i].value = "";
		f.ret_isr_hon[i].value = "";
		f.ret_hon_con[i].value = "";
		f.cre_sal[i].value = "";
		f.isr_pago[i].value = "";
		f.ret_iva_hon[i].value = "";
		f.ret_iva_ren[i].value = "";
		f.ret_iva_fle[i].value = "";
		f.iva_pago[i].value = "";
		f.iva_tras[i].value = "";
		f.iva_acre[i].value = "";
		f.iva_dec[i].value = "";
		f.dec_anu[i].value = "";
		f.acu_anual[i].value = "";
		// [29-Ago-2008] Campos agregados por nuevo calculo
		// [02-Abr-2014] Ya no aplican para marzo de 2014
		// f.ide_ret[i].value = '';
		// f.isr_acr_ide[i].value = '';
		// f.ide_dev[i].value = '';
		// f.isr_neto[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null) {
		pedirDatos(i);
		return;
	}
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
		return;
	}
}

function movInput(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function ISRpago(i) {
	var isr = get_val(f.isr[i]), ret_isr_ren = get_val(f.ret_isr_ren[i]), ret_isr_hon = get_val(f.ret_isr_hon[i]), ret_hon_con = get_val(f.ret_hon_con[i]);
	var cre_sal = get_val(f.cre_sal[i]), isr_pago = 0, /*ieps = get_val(f.ieps[i])*/ieps_gravado = get_val(f.ieps_gravado[i]), ieps_excento = get_val(f.ieps_excento[i]);

	// [29-Ago-2008] A partir de Agosto 2008 el 'ISR' ya no se va a sumar al importe del 'ISR a Pagar'
	// [02-Abr-2014] A partir de marzo de 2014 al 'ISR' se le suma el 'IEPS'
	// [11-Abr-2014] Ahora el IEPS se desglosa en IEPS gravado e IEPS excento
	// [03-Ago-2015] No sumar IEPS gravado e IEPS excento al ISR a pagar
	// [06-Sep-2015] Sumar diferencia de IEPS gravado e IEPS excento al ISR a pagar
	isr_pago = isr + (ieps_gravado - ieps_excento) + ret_isr_ren + ret_isr_hon + ret_hon_con - cre_sal;
	f.isr_pago[i].value = isr_pago != 0 ? number_format(isr_pago, 2) : "";
	f.isr_pago[i].style.color = isr_pago < 0 ? "CC0000" : "Black";
}

function IVApago(i) {
	var ret_iva_hon = get_val(f.ret_iva_hon[i]), ret_iva_ren = get_val(f.ret_iva_ren[i]), ret_iva_fle = get_val(f.ret_iva_fle[i]), iva_pago = 0;

	iva_pago = ret_iva_hon + ret_iva_ren + ret_iva_fle;
	f.iva_pago[i].value = iva_pago != 0 ? number_format(iva_pago, 2) : "";
	f.iva_pago[i].style.color = iva_pago < 0 ? "CC0000" : "Black";
}

function IVAdec(i) {
	var iva_pago = get_val(f.iva_pago[i]);
	var iva_tras = get_val(f.iva_tras[i]);
	var iva_acre = get_val(f.iva_acre[i]);
	var iva_dec = 0;

	// iva_dec = iva_tras - iva_acre;
	iva_dec = iva_tras - iva_acre/* - iva_pago*/;
	f.iva_dec[i].value = iva_dec != 0 ? number_format(iva_dec, 2) : "";
	f.iva_dec[i].style.color = iva_dec < 0 ? "CC0000" : "Black";
}

function total(campo) {
	var sum = 0;

	for (var i = 0; i < eval('f.' + campo).length; i++)
		sum += get_val(eval('f.' + campo + "[" + i + "]"));

	eval("f.total_" + campo).value = number_format(sum, 2);
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.num_cia[0].select();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame ? top.mainFrame.document.form : top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	eval('f.' + campo).select();
	return false;
}

window.onload = validar("{mensaje}", "{campo}");
//-->
</script>
<!-- END BLOCK : valid -->
<!-- START BLOCK : redir -->
<script language="javascript" type="text/javascript">
<!--
function redir() {
	if (top.mainFrame)
		top.mainFrame.location = './ban_imp_cap.php';
	else
		top.location = './ban_imp_cap.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
