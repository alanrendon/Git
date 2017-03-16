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
<td align="center" valign="middle"><p class="title">B&uacute;squeda de Hojas Diarias para Siniestros de Seguros</p>
  <form action="hoja_diaria_esp.php" method="get" name="form" target="hoja_esp">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="col">Compa&ntilde;&iacute;a</th>
      <td colspan="3" class="vtabla" scope="col"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="cambiaCia()" onkeydown="if(event.keyCode==13)importe[0].select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
      </tr>
    <tr>
      <th class="vtabla">Importe 1 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[0].select()" size="10" /></td>
      <th class="vtabla">Fecha 1</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[1].select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla">Importe 2 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[1].select()" size="10" /></td>
      <th class="vtabla">Fecha 2</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[2].select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla">Importe 3 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[2].select()" size="10" /></td>
      <th class="vtabla">Fecha 3</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[3].select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla">Importe 4 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[3].select()" size="10" /></td>
      <th class="vtabla">Fecha 4</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[4].select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla">Importe 5 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[4].select()" size="10" /></td>
      <th class="vtabla">Fecha 5</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[5].select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla">Importe 6 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[5].select()" size="10" /></td>
      <th class="vtabla">Fecha 6</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[6].select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla">Importe 7 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[6].select()" size="10" /></td>
      <th class="vtabla">Fecha 7</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[7].select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla">Importe 8 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[7].select()" size="10" /></td>
      <th class="vtabla">Fecha 8</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[8].select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla">Importe 9 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[8].select()" size="10" /></td>
      <th class="vtabla">Fecha 9</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)importe[9].select()" size="10" maxlength="10" /></td>
    </tr>
	<tr>
      <th class="vtabla">Importe 10 </th>
      <td class="vtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if(event.keyCode==13)fecha[9].select()" size="10" /></td>
      <th class="vtabla">Fecha 10</th>
      <td class="vtabla"><input name="fecha[]" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)num_cia.select()" size="10" maxlength="10" /></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./pan_hoj_dia_esp.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
	}
}

var obtenerCia = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
	else
		f.nombre.value = result;
}

function validar() {
	var total = 0;
	
	for (var i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	if (total == 0) {
		alert('Debe especificar al menos un importe para la búsqueda');
		return false;
	}
	
	if (confirm('¿Son correctos los datos?')) {
		var win = window.open('', 'hoja_esp', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
		f.submit();
		win.focus();
	}
	else
		f.fecha.select();
}

window.onload = f.num_cia.select();
//-->
</script>
</body>
</html>
