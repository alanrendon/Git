<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Carta de Bonificaciones Forzadas </p>
  <form action="./ban_car_bon_for.php" method="post" name="form" target="carta">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto</th>
      <td class="vtabla"><input name="contacto" type="text" class="vinsert" id="contacto" onkeydown="if (event.keyCode == 13) num_cia_dep[0].select();" value="{contacto}" /></td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a Entrada </th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Importe</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a Destino </th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia_dep[]" type="text" class="insert" id="num_cia_dep" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCiaDep({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],null,fecha[{i}],num_cia_dep[{back}],num_cia_dep[{next}])" size="3" />
        <input name="nombre_dep[]" type="text" disabled="disabled" class="vnombre" id="nombre_dep" size="30" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,importe[{i}],num_cia_dep[{i}],importe[{i}],fecha[{back}],fecha[{next}])" value="{fecha}" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="movCursor(event.keyCode,num_cia_des[{i}],fecha[{i}],num_cia_des[{i}],importe[{back}],importe[{next}])" value="{importe}" size="10" /></td>
      <td class="tabla"><input name="num_cia_des[]" type="text" class="insert" id="num_cia_des" onfocus="tmp.value=this.value;this.select()" onchange="cambiaCiaDes({i})" onkeydown="movCursor(event.keyCode,num_cia_dep[{next}],importe[{i}],null,num_cia_des[{back}],num_cia_des[{next}])" size="3" />
        <input name="nombre_des[]" type="text" disabled="disabled" class="vnombre" id="nombre_des" size="30" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCiaDep(i) {
	if (f.num_cia_dep[i].value == '' || f.num_cia_dep[i].value == '0') {
		f.num_cia_dep[i].value = '';
		f.nombre_dep[i].value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_car_bon_for.php', 'GET', 'c=' + get_val(f.num_cia_dep[i]) + '&i=' + i, obtenerCiaDep);
	}
}

var obtenerCiaDep = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (result[1] == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia_dep[result[0]].value = f.tmp.value;
		f.num_cia_dep[result[0]].select();
	}
	else
		f.nombre_dep[get_val2(result[0])].value = result[1];
}

function cambiaCiaDes(i) {
	if (f.num_cia_des[i].value == '' || f.num_cia_des[i].value == '0') {
		f.num_cia_des[i].value = '';
		f.nombre_des[i].value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_car_bon_for.php', 'GET', 'c=' + get_val(f.num_cia_des[i]) + '&i=' + i, obtenerCiaDes);
	}
}

var obtenerCiaDes = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (result[1] == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia_des[i].value = f.tmp.value;
		f.num_cia_des[i].select();
	}
	else
		f.nombre_des[get_val2(result[0])].value = result[1];
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	var win = window.open('', 'carta', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	f.submit();
	f.reset();
	win.focus();
}

window.onload = f.contacto.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : error -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold;">Los siguientes dep&oacute;sitos no se encuentran en el estado de cuenta</p>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Banco</th>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Cuenta</th>
      <th class="tabla" scope="col">Fecha</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <!-- START BLOCK : fila_error -->
	<tr>
      <td class="vtabla">{banco}</td>
      <td class="vtabla">{num_cia} {nombre} </td>
      <td class="vtabla">{cuenta}</td>
      <td class="tabla">{fecha}</td>
      <td class="rtabla">{importe}</td>
    </tr>
	<!-- END BLOCK : fila_error -->
  </table>  
  <p>
    <input type="button" class="boton" value="Cerrar" onclick="self.close()" />
</p></td>
</tr>
</table>
<!-- END BLOCK : error -->
</body>
</html>
