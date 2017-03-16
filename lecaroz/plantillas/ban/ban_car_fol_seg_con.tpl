<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Asuntos</p>
  <form action="ban_car_fol_seg_con.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaCia()" onkeydown="if(event.keyCode==13)fecha.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if(event.keyCode==13)folio.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if(event.keyCode==13)atencion.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dirigida a </th>
      <td class="vtabla"><input name="atencion" type="text" class="vinsert" id="atencion" onkeydown="if(event.keyCode==13)referencia.focus()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Referencia</th>
      <td class="vtabla"><input name="referencia" type="text" class="vinsert" id="referencia" onkeydown="if(event.keyCode==13)responsable.select()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dependencia</th>
      <td class="vtabla"><input name="dependencia" type="text" class="vinsert" id="dependencia" onkeydown="if(event.keyCode==13)responsable.select()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Responsable</th>
      <td class="vtabla"><input name="responsable" type="text" class="vinsert" id="responsable" onkeydown="if(event.keyCode==13)expediente.select()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Expediente</th>
      <td class="vtabla"><input name="expediente" type="text" class="vinsert" id="expediente" onkeydown="if(event.keyCode==13)palabras.select()" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Palabra(s) clave </th>
      <td class="vtabla"><input name="palabras" type="text" class="vinsert" id="palabras" onkeydown="if(event.keyCode==13)num_cia.select()" style="width:98%;" /></td>
    </tr>
    <tr>
      <th height="23" class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" id="radio" value="1" checked="checked" />
        Pendientes<br />
        <input type="radio" name="tipo" id="radio2" value="2" />
        Aclarados</td>
    </tr>
  </table>  
  <p>
    <input name="" type="button" class="boton" onclick="validar()" value="Siguiente" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
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
		myConn.connect('./ban_car_fol_seg_con.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
	f.submit();
}

window.onload = f.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : resultado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Consulta de Asuntos</p>
<table class="tabla">
  <tr>
    <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="tabla" scope="col">Folio</th>
    <th class="tabla" scope="col">Dirigida a</th>
    <th class="tabla" scope="col">Fecha</th>
    <th class="tabla" scope="col">Referencia</th>
    <th class="tabla" scope="col"><img src="./imagenes/tool16x16.png" alt="Herramientas" width="16" height="16" /></th>
  </tr>
  <!-- START BLOCK : carta -->
  <tr id="carta{id}">
    <td class="vtabla" style="font-size:12pt;font-weight:bold;">{num_cia} {nombre}</td>
    <td class="tabla">{folio}</td>
    <td class="vtabla" style="color:#00C;">{atencion}</td>
    <td class="tabla">{fecha}</td>
    <td class="vtabla">{referencia}</td>
    <td class="tabla"><img src="./imagenes/plus16x16.png" width="16" height="16" onclick="expandir({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" /><img src="./imagenes/minus16x16.png" width="16" height="16" onclick="contraer({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" /><img src="./imagenes/fax16x16.png" width="16" height="16" onclick="imprimir({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" /><img src="./imagenes/WhiteSheet16x16.png" width="16" height="16" onclick="carta({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" /></td>
  </tr>
  <tr id="detalle{id}" style="display:none">
    <td colspan="6" class="vtabla" id="detalleCell{id}">&nbsp;</td>
    </tr>
  <tr id="emptyRow{id}" style="display:none">
    <td colspan="6" class="vtabla">&nbsp;</td>
  </tr>
    <!-- END BLOCK : carta -->
</table>
<p>
  <input type="button" class="boton" value="Regresar" onclick="document.location='ban_car_fol_seg_con.php'" />
</p></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
function expandir(id) {
	var myConn = new XHConn();
	
	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
	
	// Pedir datos
	myConn.connect('./ban_car_fol_seg_con.php', 'GET', 'id=' + id, expandirAsunto);
}

var expandirAsunto = function(oXML) {
	var result = oXML.responseText.split('|||');
	
	document.getElementById('detalleCell' + result[0]).innerHTML = result[1];
	document.getElementById('detalle' + result[0]).style.display = 'table-row';
	document.getElementById('emptyRow' + result[0]).style.display = 'table-row';
}

function contraer(id) {
	document.getElementById('detalleCell' + id).innerHTML = '&nbsp;';
	document.getElementById('detalle' + id).style.display = 'none';
	document.getElementById('emptyRow' + id).style.display = 'none';
}

function imprimir(id) {
	var url = 'reporte_asunto.php'
	var param = '?id=' + id;
	var win;
	
	win = window.open(url + param, 'reporte', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function carta(id) {
	var url = '_carta.php'
	var param = '?id=' + id;
	var win;
	
	win = window.open(url + param, 'carta', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function show(id) {
	var win = window.open('img_doc_car.php?id=' + id + '&width=965', '', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1000,height=600');
	win.focus();
}
//-->
</script>
<!-- END BLOCK : resultado -->
</body>
</html>
