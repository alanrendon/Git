<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : captura -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Inventario de Zapaterias</p>
  <form action="./bal_inv_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Mes</th>
      <td class="vtabla" scope="col"><select name="mes" class="insert" id="mes" onchange="pedirDatos()">
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
      <th class="vtabla">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) pedirDatos()" onkeydown="desp(event.keyCode,importe[0],null,null,null,importe[0])" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th colspan="2" class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="rtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
        {num_cia}</td>
      <td class="vtabla">{nombre}</td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (input_format(this,2,true)) calculaTotal()" onkeydown="desp(event.keyCode,importe[{next}],null,null,importe[{back}],importe[{next}])" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="tabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true" /></th>
    </tr>
  </table>
  <p>
    <input name="next" type="button" class="boton" id="next" onclick="validar()" value="Siguiente" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function desp(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && lt && lt != null) rt.select();
	else if (keyCode == 38 && lt && lt != null) up.select();
	else if (keyCode == 40 && lt && lt != null) dn.select();
}

function calculaTotal() {
	var total = 0;
	
	for (var i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = number_format(total, 2);
}

var mostrarDatos = function (oXML) {
	var data = oXML.responseText, mod, tmp, imp = new Array();
	
	data = data.split('\n');
	mod = data[0] == '1' ? false : true;
	data = data[1] && data[1].length > 0 ? data[1].split('||') : false;
	
	for (var i = 0; i < f.importe.length; i++) {
		f.importe[i].value = '';
		f.importe[i].disabled = mod;
	}
	f.next.disabled = mod;
	
	if (!data)
		return false;
	
	for (i = 0; i < data.length; i++)
		if (data[i].length > 0) {
			tmp = data[i].split('|');
			imp[tmp[0]] = tmp[1];
		}
	
	for (i = 0; i < f.num_cia.length; i++)
		if (imp[f.num_cia[i].value] != null)
			f.importe[i].value = number_format(imp[f.num_cia[i].value], 2);
	
	calculaTotal();
}

function pedirDatos() {
	for (var i = 0; i < f.importe.length; i++)
		f.importe[i].value = "";
	f.total.value = "0.00";
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	if (get_val(f.mes) > 0 && get_val(f.anio) > 0) {
		// Pedir datos
		myConn.connect("./bal_inv_cap.php", "GET", "mes=" + get_val(f.mes) + "&anio=" + get_val(f.anio), mostrarDatos);
	}
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.anio.select();
}

window.onload = f.anio.select();
//-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
