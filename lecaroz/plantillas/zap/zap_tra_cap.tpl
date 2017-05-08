<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Traspasos de Pares 
  
</p>
  <form action="./zap_tra_cap.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
    </tr>
    <tr>
      <td class="tabla"><select name="mes" class="insert" id="mes" onchange="getTras()">
        <option value=""></option>
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
      <td class="tabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) getTras()" onkeydown="movCursor(event.keyCode,importe[0],null,null,null,importe[0])" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Traspaso</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="vtabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
        {num_cia} {nombre} </td>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="if (inputFormat(this,2,false)) calculaTotal()" onkeydown="movCursor(event.keyCode,importe[{next}],null,null,importe[{back}],importe[{next}])" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th class="rtabla">Total</th>
      <th class="vtabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" /></th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
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

function getTras() {
	if (f.anio.value == '' || f.anio.value == '0' || f.mes.value == '') {
		f.total.value = '0.00';
		
		for (var i = 0; i < f.importe.length; i++)
			f.importe[i].value = '';
		
		return false;
	}
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	// Pedir datos
	myConn.connect("./zap_tra_cap.php", "GET", 'mes=' + get_val(f.mes) + '&anio=' + get_val(f.anio), resultTras);
}

var resultTras = function (oXML) {
	var result = oXML.responseText;
	
	if (result.length == 0) {
		for (var i = 0; i < f.importe.length; i++)
			f.importe[i].value = '';
		
		calculaTotal();
		return false;
	}
	
	var tmp, data = result.split('|'), imp = new Array();
	
	for (var i = 0; i < data.length; i++) {
		tmp = data[i].split(',');
		imp[get_val2(tmp[0])] = get_val2(tmp[1]);
	}
	
	for (i = 0; i < f.num_cia.length; i++)
		if (imp[get_val(f.num_cia[i])] != null)
			f.importe[i].value = numberFormat(imp[get_val(f.num_cia[i])], 2);
		else
			f.importe[i].value = '';
			
	
	calculaTotal();
}

function calculaTotal() {
	var i, total = 0;
	
	for (i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = numberFormat(total, 2);
}

function validar() {
	if (get_val(f.mes) == 0) {
		alert('Debe especificar el mes de captura');
		f.mes.focus();
		return false;
	}
	else if (get_val(f.anio) == 0) {
		alert('Debe especificar el año de captura');
		f.anio.select();
		return false;
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.anio.select();
//-->
</script>
</body>
</html>
