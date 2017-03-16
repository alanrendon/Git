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
<td align="center" valign="middle"><p class="title">Pagos Pendientes de Infonavit </p>
  <form action="./fac_inf_pen.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Empleado</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,anio[{i}],null,anio[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre_cia[]" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
      <td class="vtabla"><select name="id[]" class="insert" id="id" style="width:100%;">
        <option value=""></option>
      </select>
      </td>
      <td class="tabla"><select name="mes[]" class="insert" id="mes">
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
      <td class="tabla"><input name="anio[]" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="movCursor(event.keyCode,importe[{i}],num_cia[{i}],importe[{i}],anio[{back}],anio[{next}])" value="{anio}" size="4" maxlength="4" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown=" movCursor(event.keyCode,num_cia[{next}],anio[{i}],null,importe[{back}],importe[{next}])" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia(i) {
	if (f.num_cia[i].value == '' || f.num_cia[i].value == '0') {
		f.num_cia[i].value = '';
		f.nombre_cia[i].value = '';
	}
	else if (cia[get_val(f.num_cia[i])] != null)
		f.nombre_cia[i].value = cia[get_val(f.num_cia[i])];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[i].value = f.tmp.value;
		f.num_cia[i].select();
		return false;
	}
	
	listaEmpleados(i);
}

function listaEmpleados(i) {
	if (get_val(f.num_cia[i]) <= 0) {
		f.id[i].length = 1;
		f.id[i].options[0].value = '';
		f.id[i].options[0].text = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_inf_pen.php', 'GET', 'num_cia=' + get_val(f.num_cia[i]) + '&i=' + i, generarListadoEmpleados);
	}
}

var generarListadoEmpleados = function (oXML) {
	var result = oXML.responseText, i, j, tmp;
	
	result = result.split('|');
	i = get_val2(result[0]);
	
	if (result[1] == '-1') {
		alert('La compañía no tiene empleados con Infonavit');
		
		f.num_cia[i].value = '';
		f.nombre_cia[i].value = '';
		f.num_cia[i].select();
		
		f.id[i].length = 1;
		f.id[i].options[0].value = '';
		f.id[i].options[0].text = '';
		
		return false;
	}
	
	f.id[i].length = result.length - 1;
	for (j = 1; j < result.length; j++) {
		tmp = result[j].split('/');
		
		f.id[i].options[j - 1].value = tmp[0];
		f.id[i].options[j - 1].text = tmp[1];
	}
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

window.onload = f.num_cia[0].select();
-->
</script>
<!-- END BLOCK : captura -->
</body>
</html>
