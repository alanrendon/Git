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
<td align="center" valign="middle"><p class="title">Reserva de Folios de Cheques</p>
  <form action="./ban_res_che.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a(s)</th>
      <td class="vtabla"><input name="num_cia1" type="text" class="insert" id="num_cia1" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia2.select()" size="3" />
        a la 
          <input name="num_cia2" type="text" class="insert" id="num_cia2" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) fecha.select()" size="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select>      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) cantidad.select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Cantidad a reservar </th>
      <td class="vtabla"><input name="cantidad" type="text" class="insert" id="cantidad" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia1.select()" value="5" size="3" /></td>
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

function validar() {
	/*if (get_val(f.num_cia1) == 0) {
		alert('Debe especificar una compañía o un rango');
		f.num_cia1.select();
		return false;
	}
	else*/ if (f.fecha.length < 8) {
		alert('Debe especificar la fecha');
		f.fecha.select();
		return false;
	}
	else if (get_val(f.cantidad) == 0) {
		alert('Debe especificar los folios a reservar');
		f.cantidad.select();
		return false;
	}
	
	if (!confirm('¿Desea reservar los folios?'))
		return false;
	
	var myConn = new XHConn();
	if (!myConn) alert('XMLHTTP no disponible. Trate con un nuevo/mejor navegador.');
	
	myConn.connect("./ban_res_che.php", "GET", 'num_cia1=' + f.num_cia1.value + '&num_cia2=' + f.num_cia2.value + '&cuenta=' + f.cuenta.value + '&fecha=' + f.fecha.value + '&cantidad=' + f.cantidad.value, alertResult);
}

var alertResult = function (oXML) {
	var result = oXML.responseText;
	
	alert(result);
}

window.onload = f.num_cia1.select();
//-->
</script>
</body>
</html>
