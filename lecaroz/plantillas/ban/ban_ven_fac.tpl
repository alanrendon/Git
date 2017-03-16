<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Fechas de Vencimiento de Facturas</p>
  <form action="./ban_ven_fac.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Fecha de <br />
        Vencimiento</th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia({i})" onkeydown="movCursor(event.keyCode,fecha[{i}],null,fecha[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
        <input name="nombre[]" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      <td class="tabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="movCursor(event.keyCode,num_cia[{next}],num_cia[{i}],null,fecha[{back}],fecha[{next}])" size="10" maxlength="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
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
		myConn.connect('./ban_ven_fac.php', 'GET', 'c=' + get_val(f.num_cia[i]) + '&i=' + i, obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (result[1] == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia[get_val2(result[0])].value = f.tmp.value;
		f.num_cia[get_val2(result[0])].select();
	}
	else
		f.nombre[get_val2(result[0])].value = result[1];
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

function validar() {
	if (confirm('¿Son correctos todos los datos?'))
		f.submit();
	else
		f.num_cia[0].select();
}

window.onload = f.num_cia[0].select();
//-->
</script>
</body>
</html>
