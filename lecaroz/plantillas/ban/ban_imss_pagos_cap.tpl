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
<div id="loading" style="position:absolute; left:645px; top:187px; width:70px; height:70px; z-index:1; display:none;">
<img src="./AJAXTest/loading_ani2.gif">
</div>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Captura de Importes Pagados al IMSS</p>
  <form action="./ban_imss_pagos_cap.php" method="post" name="form" target="valid">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) cambiaCia()" onKeyDown="if (event.keyCode == 13) anio.select()" value="{num_cia}" size="3">
        <input name="nombre" type="text" class="vnombre" id="nombre" value="{nombre}" size="30"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="tmp.value=this.value;this.select()" onChange="if (isInt(this,tmp)) pedirDatos()" onKeyDown="if (event.keyCode == 13) importe[0].select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
  </table>  
  <br>
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">Importe</th>
    </tr>
    <tr>
      <!-- START BLOCK : fila -->
	  <td class="vtabla"><input name="mes[]" type="hidden" id="mes" value="{mes}">
        {mes} {nombre} </td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onFocus="tmp.value=this.value;this.select()" onChange="if (input_format(this,2,true)) calculaTotal()" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) importe[{next}].select();
else if (event.keyCode == 38) importe[{back}].select()" value="{importe}" size="10"></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th class="rtabla">Total</th>
      <th class="tabla"><input name="total" type="text" class="rnombre" id="total" value="{total}" size="10" readonly="true"></th>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<iframe name="valid" style="display:none;"></iframe>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : cia -->
cia[{num_cia}] = "{nombre}";
<!-- END BLOCK : cia -->

function cambiaCia() {
	if (f.num_cia.value == "" || f.num_cia.value == "0") {
		f.num_cia.value = "";
		f.nombre.value = "";
	}
	else if (cia[get_val(f.num_cia)] != null) {
		f.nombre.value = cia[get_val(f.num_cia)];
		
		// Llamar función para cargar datos
		pedirDatos();
	}
	else {
		alert("La compañía no se encuentra en el catálogo");
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

var mostrarDatos = function (oXML) {
	var data = oXML.responseText;
	
	// Ocultar imagen de carga
	document.getElementById("loading").style.setProperty("display", "none", null);
	
	if (data == "")
		return false;
	
	var responseArray = data.split('\n'), tmp;
	for (var i = 0; i < responseArray.length; i++)
		if (responseArray[i].length > 0) {
			tmp = responseArray[i].split('|');
			f.importe[get_val2(tmp[0]) - 1].value = number_format(get_val2(tmp[1]), 2);
		}
	
	calculaTotal();
}

function pedirDatos() {
	for (var i = 0; i < f.importe.length; i++)
		f.importe[i].value = "";
	f.total.value = "0.00";
	
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	if (get_val(f.num_cia) > 0 && get_val(f.anio) > 0) {
		// Mostrar imagen de carga
		document.getElementById("loading").style.setProperty("display", "block", null);
		// Pedir datos
		myConn.connect("./ban_imss_pagos_cap.php", "GET", "num_cia=" + get_val(f.num_cia) + "&anio=" + get_val(f.anio), mostrarDatos);
	}
}

function calculaTotal() {
	var total = 0;
	
	for (var i = 0; i < f.importe.length; i++)
		total += get_val(f.importe[i]);
	
	f.total.value = number_format(total, 2);
}

function validar() {
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		f.importe[0].select();
}

window.onload = f.num_cia.select();
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
		top.mainFrame.location = './ban_imss_pagos_cap.php';
	else
		top.location = './ban_imss_pagos_cap.php';
}

window.onload = redir();
//-->
</script>
<!-- END BLOCK : redir -->
</body>
</html>
