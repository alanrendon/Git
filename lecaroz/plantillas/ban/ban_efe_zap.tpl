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
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Efectivos</p>
  <form action="./ban_efe_zap.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Fecha</th>
    </tr>
    <tr>
      <td class="tabla"><input name="fecha" type="text" class="insert" id="fecha" style="font-size:14pt; " onFocus="tmp.value=this.value;this.select()" onChange="if (inputDateFormat(this)) pedirDatos()" onKeyDown="movCursor(event.keyCode,venta[0],null,null,null,venta[0])" value="{fecha}" size="10" maxlength="10"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th rowspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th rowspan="2" class="tabla" scope="col">Venta</th>
      <th rowspan="2" class="tabla" scope="col">Errores</th>
      <th rowspan="2" class="tabla" scope="col">Venta <br>
        Total </th>
      <th rowspan="2" class="tabla" scope="col">Otros</th>
      <th rowspan="2" class="tabla" scope="col">Gastos</th>
      <th rowspan="2" class="tabla" scope="col">Efectivo</th>
      <th colspan="4" class="tabla" scope="col">Nota</th>
      <th rowspan="2" class="tabla" scope="col">Clientes</th>
      <th rowspan="2" class="tabla" scope="col">Pares</th>
    </tr>
    <tr>
      <th class="tabla" scope="col">1</th>
      <th class="tabla" scope="col">2</th>
      <th class="tabla" scope="col">3</th>
      <th class="tabla" scope="col">4</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="vtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}">
        {num_cia} {nombre} </td>
      <td class="tabla"><input name="venta[]" type="text" class="rinsert" id="venta" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaTotal({i})" onKeyDown="movCursor(event.keyCode,errores[{i}],null,errores[{i}],venta[{back}],venta[{next}])" value="{venta}" size="8"></td>
      <td class="tabla"><input name="errores[]" type="text" class="rinsert" id="errores" style="color:#CC0000;" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) ventaTotal({i})" onKeyDown="movCursor(event.keyCode,otros[{i}],venta[{i}],otros[{i}],errores[{back}],errores[{next}])" value="{errores}" size="8"></td>
      <td class="tabla"><input name="venta_total[]" type="text" class="rnombre" id="venta_total" value="{venta_total}" size="8" readonly="true"></td>
      <td class="tabla"><input name="otros[]" type="text" class="rinsert" id="otros" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaEfectivo({i})" onKeyDown="movCursor(event.keyCode,nota1[{i}],errores[{i}],nota1[{i}],otros[{back}],otros[{next}])" value="{otros}" size="8"></td>
      <td class="tabla"><input name="gastos[]" type="text" class="rnombre" id="gastos" style="color:#CC0000;" onClick="consultarGastos({num_cia},{i})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" value="{gastos}" size="6" readonly="true"></td>
      <td class="tabla"><input name="efectivo[]" type="text" readonly="true" class="rnombre" id="efectivo" value="{efectivo}" size="8"></td>
      <td class="tabla"><input name="nota1_ini[]" type="hidden" id="nota1_ini" value="{am_ini}">
	  <input name="nota1[]" type="text" class="insert" id="nota1" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) calculaClientes({i})" onKeyDown="movCursor(event.keyCode,nota2[{i}],otros[{i}],nota2[{i}],nota1[{back}],nota1[{next}])" value="{rec_ini}" size="4"></td>
      <td class="tabla"><input name="nota2_ini[]" type="hidden" id="nota2_ini" value="{pm_ini}">
	  <input name="nota2[]" type="text" class="insert" id="nota2" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) calculaClientes({i})" onKeyDown="movCursor(event.keyCode,nota3[{i}],nota1[{i}],nota3[{i}],nota2[{back}],nota2[{next}])" value="{rec_ini}" size="4"></td>
      <td class="tabla"><input name="nota3_ini[]" type="hidden" id="nota3_ini" value="{pm_ini}">
	  <input name="nota3[]" type="text" class="insert" id="nota3" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) calculaClientes({i})" onKeyDown="movCursor(event.keyCode,nota4[{i}],nota2[{i}],nota4[{i}],nota3[{back}],nota3[{next}])" value="{rec_ini}" size="4"></td>
      <td class="tabla"><input name="nota4_ini[]" type="hidden" id="nota4_ini" value="{pm_ini}">
	  <input name="nota4[]" type="text" class="insert" id="nota4" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) calculaClientes({i})" onKeyDown="movCursor(event.keyCode,pares[{i}],nota3[{i}],pares[{i}],nota4[{back}],nota4[{next}])" value="{rec_ini}" size="4"></td>
      <td class="tabla"><input name="clientes[]" type="text" class="rnombre" id="clientes" value="{clientes}" size="3" readonly="true"></td>
      <td class="tabla"><input name="pares[]" type="text" class="rinsert" id="pares" onFocus="tmp.value=this.value;this.select()" onChange="input_format(this,-1,true)" onKeyDown="movCursor(event.keyCode,venta[{next}],nota4[{i}],null,pares[{back}],pares[{next}])" value="{pares}" size="3"></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter != null && enter) enter.select();
	else if (keyCode == 37 && lt != null && lt) lt.select();
	else if (keyCode == 39 && rt != null && rt) rt.select();
	else if (keyCode == 38 && up != null && up) up.select();
	else if (keyCode == 40 && dn != null && dn) dn.select();
}

function consultarGastos(num_cia, i) {
	if (f.fecha.value.length < 8 || f.gastos[i].value == '')
		return false;
	
	var win = window.open('./ros_gas_con_v2.php?num_cia=' + num_cia + '&fecha=' + f.fecha.value,"con_gas","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=800,height=600");
	win.focus();
}

function ventaTotal(i) {
	var total = 0;
	
	total = get_val(f.venta[i]) - get_val(f.errores[i]);
	
	f.venta_total[i].value = total != 0 ? number_format(total, 2) : '';
	f.venta_total[i].style.color = total < 0 ? "CC0000" : "000000";
	
	calculaEfectivo(i);
}

function calculaEfectivo(i) {
	var efectivo;
	efectivo = get_val(f.venta_total[i]) + get_val(f.otros[i]) - get_val(f.gastos[i]);
	f.efectivo[i].value = efectivo != 0 ? number_format(efectivo, 2) : '';
	f.efectivo[i].style.color = efectivo < 0 ? "CC0000" : "0000CC";
}

function calculaClientes(i) {
	if (f.nota1[i].value == '' && f.nota2[i].value == '' && f.nota3[i].value == '' && f.nota4[i].value == '') {
		f.clientes[i].value = '';
		return false;
	}
	
	var nota1, nota2, nota3, nota4, clientes = 0;
	
	nota1_ini = get_val(f.nota1_ini[i]);
	nota2_ini = get_val(f.nota2_ini[i]);
	nota3_ini = get_val(f.nota3_ini[i]);
	nota4_ini = get_val(f.nota4_ini[i]);
	nota1 = get_val(f.nota1[i]);
	nota2 = get_val(f.nota2[i]);
	nota3 = get_val(f.nota3[i]);
	nota4 = get_val(f.nota4[i]);
	
	clientes += nota1 > nota1_ini ? nota1 - nota1_ini : nota1;
	clientes += nota2 > nota2_ini ? nota2 - nota2_ini : nota2;
	clientes += nota3 > nota3_ini ? nota3 - nota3_ini : nota3;
	clientes += nota4 > nota4_ini ? nota4 - nota4_ini : nota4;
	
	f.clientes[i].value = clientes > 0 ? number_format(clientes, -1) : '';
}

var mostrarDatos = function (oXML) {
	var data = oXML.responseText, mod, tmp, imp = [];
	
	data = data.split('\n');
	mod = data[0] == '1' ? false : true;
	data = data[1] && data[1].length > 0 ? data[1].split('||') : false;
	
	f.next.disabled = mod;
	
	if (!data)
		return false;
	
	for (var i = 0; i < data.length; i++)
		if (data[i].length > 0) {
			tmp = data[i].split('|');
			imp[get_val2(tmp[0])] = [get_val2(tmp[1]), get_val2(tmp[2]), get_val2(tmp[3]), get_val2(tmp[4]), get_val2(tmp[5]), get_val2(tmp[6]), get_val2(tmp[7]), get_val2(tmp[8]), get_val2(tmp[9]), get_val2(tmp[10]), get_val2(tmp[11]), get_val2(tmp[12]), get_val2(tmp[13]), get_val2(tmp[14]), get_val2(tmp[15])];
		}
	
	for (i = 0; i < f.num_cia.length; i++)
		if (imp[f.num_cia[i].value] != undefined) {
			f.venta[i].value = imp[f.num_cia[i].value][0] != 0 ? number_format(imp[f.num_cia[i].value][0], 2) : "";
			f.errores[i].value = imp[f.num_cia[i].value][1] != 0 ? number_format(imp[f.num_cia[i].value][1], 2) : "";
			f.venta_total[i].value = imp[f.num_cia[i].value][0] - imp[f.num_cia[i].value][1] != 0 ? number_format(imp[f.num_cia[i].value][0] - imp[f.num_cia[i].value][1], 2) : "";
			f.venta_total[i].style.color = imp[f.num_cia[i].value][0] - imp[f.num_cia[i].value][1] < 0 ? 'CC0000' : '000000';
			f.otros[i].value = imp[f.num_cia[i].value][2] != 0 ? number_format(imp[f.num_cia[i].value][2], 2) : "";
			f.clientes[i].value = imp[f.num_cia[i].value][3] != 0 ? number_format(imp[f.num_cia[i].value][3], -1) : "";
			f.pares[i].value = imp[f.num_cia[i].value][4] != 0 ? number_format(imp[f.num_cia[i].value][4], -1) : "";
			f.nota1[i].value = imp[f.num_cia[i].value][5] != 0 ? imp[f.num_cia[i].value][5] : "";
			f.nota2[i].value = imp[f.num_cia[i].value][6] != 0 ? imp[f.num_cia[i].value][6] : "";
			f.nota3[i].value = imp[f.num_cia[i].value][7] != 0 ? imp[f.num_cia[i].value][7] : "";
			f.nota4[i].value = imp[f.num_cia[i].value][8] != 0 ? imp[f.num_cia[i].value][8] : "";
			f.nota1_ini[i].value = imp[f.num_cia[i].value][9] != 0 ? imp[f.num_cia[i].value][9] : "";
			f.nota2_ini[i].value = imp[f.num_cia[i].value][10] != 0 ? imp[f.num_cia[i].value][10] : "";
			f.nota3_ini[i].value = imp[f.num_cia[i].value][11] != 0 ? imp[f.num_cia[i].value][11] : "";
			f.nota4_ini[i].value = imp[f.num_cia[i].value][12] != 0 ? imp[f.num_cia[i].value][12] : "";
			f.gastos[i].value = imp[f.num_cia[i].value][13] != 0 ? number_format(imp[f.num_cia[i].value][13], 2) : "";
			f.efectivo[i].value = imp[f.num_cia[i].value][14] != 0 ? number_format(imp[f.num_cia[i].value][14], 2) : "";
			f.efectivo[i].style.color = imp[f.num_cia[i].value][14] <= 0 ? 'CC0000' : '0000CC';
		}
}

function pedirDatos() {
	for (var i = 0; i < f.num_cia.length; i++) {
		f.venta[i].value = "";
		f.errores[i].value = "";
		f.venta_total[i].value = "";
		f.otros[i].value = "";
		f.nota1[i].value = "";
		f.nota2[i].value = "";
		f.nota3[i].value = "";
		f.nota4[i].value = "";
		f.clientes[i].value = "";
		f.pares[i].value = "";
		f.gastos[i].value = "";
		f.efectivo[i].value = "";
	}
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	if (f.fecha.value.length >= 8)
		// Pedir datos
		myConn.connect("./ban_efe_zap.php", "GET", "fecha=" + escape(f.fecha.value), mostrarDatos);
	else
		f.next.disabled = false;
}

function validar() {
	for (var i = 0; i < f.venta_total.length; i++)
		if (get_val(f.venta_total[i]) < 0) {
			alert("La venta del día no puede ser negativa");
			f.venta[i].select();
			return false;
		}
//		else if (f.nota1[i].value != '' && get_val(f.nota1[i]) <= get_val(f.nota1_ini[i])) {
//			alert("La nota 1 debe ser mayor a " + f.nota1_ini[i].value);
//			f.nota1[i].select();
//			return false;
//		}
//		else if (f.nota2[i].value != '' && get_val(f.nota2[i]) <= get_val(f.nota2_ini[i])) {
//			alert("La nota 2 debe ser mayor a " + f.nota2_ini[i].value);
//			f.nota2[i].select();
//			return false;
//		}
//		else if (f.nota3[i].value != '' && get_val(f.nota3[i]) <= get_val(f.nota3_ini[i])) {
//			alert("La nota 3 debe ser mayor a " + f.nota3_ini[i].value);
//			f.nota3[i].select();
//			return false;
//		}
//		else if (f.nota4[i].value != '' && get_val(f.nota4[i]) <= get_val(f.nota4_ini[i])) {
//			alert("La nota 4 debe ser mayor a " + f.nota4_ini[i].value);
//			f.nota4[i].select();
//			return false;
//		}
		
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.fecha.select();
}

window.onload = function () { f.fecha.select(); showAlert = true;};
//-->
</script>
<!-- END BLOCK : captura -->
<!-- START BLOCK : valid -->
<script language="javascript" type="text/javascript">
<!--
var f = top.mainFrame ? top.mainFrame.document.form : top.document.form;

function validar(mensaje, campo) {
	alert(mensaje);
	f.eval(campo).select();
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
		top.mainFrame.location = './ban_efe_zap.php';
	else
		top.location = './ban_efe_zap.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
