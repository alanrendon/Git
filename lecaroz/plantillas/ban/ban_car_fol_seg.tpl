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
<td align="center" valign="middle"><p class="title">Seguimiento de Asuntos
 </p>
  <form action="./ban_car_fol_seg.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if(isInt(this,tmp))cambiaCia()" onkeydown="if(event.keyCode==13)folio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Folio</th>
      <td class="vtabla"><input name="folio" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if(event.keyCode==13)atencion.select()" size="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Dirigida a </th>
      <td class="vtabla"><input name="atencion" type="text" class="vinsert" id="atencion" onkeydown="if(event.keyCode==13)referencia.select()" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Referencia</th>
      <td class="vtabla"><input name="referencia" type="text" class="vinsert" id="referencia" onkeydown="if(event.keyCode==13)palabras.select()" size="30" /></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Palabra(s) clave </th>
      <td class="vtabla"><input name="palabras" type="text" class="vinsert" id="palabras" onkeydown="if(event.keyCode==13)num_cia.select()" style="width:98%;" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Asuntos Relacionados</th>
      <td class="vtabla"><input name="rel" type="checkbox" id="rel" value="1" />
        Incluir</td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
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
		myConn.connect('./ban_car_fol_seg.php', 'GET', 'c=' + get_val(f.num_cia), obtenerCia);
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
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : resultado -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Seguimiento de Asuntos</p>
  <form action="ban_car_fol_seg.php" method="post" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Cia</th>
      <th class="tabla" scope="col">Folio</th>
      <th class="tabla" scope="col">Dirigida a</th>
      <th class="tabla" scope="col">Referencia</th>
      <th class="tabla" scope="col">Fecha<br />
        de Inicio</th>
      <th class="tabla" scope="col">Fecha de<br />
        Respuesta</th>
      <th class="tabla" scope="col">Dependencia</th>
      <th class="tabla" scope="col">Responsable</th>
      <th class="tabla" scope="col">Expediente</th>
      <th class="tabla" scope="col">Observaciones</th>
      <th class="tabla" scope="col">Asociar</th>
      <th class="tabla" scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
    </tr>
    <!-- START BLOCK : fila -->
    <tr>
      <td class="vtabla"><input name="id[]" type="hidden" id="id" value="{id}" />{num_cia} {nombre}</td>
      <td class="tabla">{folio}</td>
      <td class="vtabla">{atencion}</td>
      <td class="vtabla">{referencia}</td>
      <td class="tabla"><input name="fecha[]" type="hidden" id="fecha" value="{fecha}" />
        {fecha}</td>
      <td class="tabla"><input name="fecha_respuesta[]" type="text" class="insert" id="fecha_respuesta" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeyup="movCursor(event.keyCode,dependencia[{i}],null,dependencia[{i}],fecha_respuesta[{back}],fecha_respuesta[{next}])" value="{fecha_respuesta}" size="10" maxlength="10" /></td>
      <td class="tabla"><input name="dependencia[]" type="text" class="vinsert" id="dependencia" onkeyup="movCursor(event.keyCode,responsable[{i}],fecha_respuesta[{i}],responsable[{i}],dependencia[{back}],dependencia[{next}])" value="{dependencia}" size="25" maxlength="100" /></td>
      <td class="tabla"><input name="responsable[]" type="text" class="vinsert" id="responsable" onkeyup="movCursor(event.keyCode,expediente[{i}],dependencia[{i}],expediente[{i}],responsable[{back}],responsable[{next}])" value="{responsable}" size="25" /></td>
      <td class="tabla"><input name="expediente[]" type="text" class="vinsert" id="expediente" onkeyup="movCursor(event.keyCode,observaciones[{i}],responsable[{i}],observaciones[{i}],expediente[{back}],expediente[{next}])" value="{expediente}" size="20" /></td>
      <td class="tabla"><textarea name="observaciones[]" cols="40" rows="3" class="insert" id="observaciones">{observaciones}</textarea></td>
      <td class="tabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyUp="movCursor(event.keyCode,folio[{i}],observaciones[{i}],folio[{i}],num_cia[{back}],num_cia[{next}])" size="3" />
      <input name="folio[]" type="text" class="insert" id="folio" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp))buscarFolio(get_val(this))" onkeyup="movCursor(event.keyCode,fecha_respuesta[{next}],num_cia[{i}],null,folio[{back}],folio[{next}])" size="3" /></td>
      <td class="tabla"><img src="imagenes/scanner16x16.png" width="16" height="16" onclick="scan({id})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" /><img src="menus/search.gif" width="16" height="16" onclick="detalle({id})" onMouseOver="this.style.cursor='pointer'" onMouseOut="this.style.cursor='default'" /><img src="imagenes/WhiteSheet16x16.png" width="16" height="16" onclick="carta({id})" onmouseover="this.style.cursor='pointer'" onmouseout="this.style.cursor='default'" /></td>
    </tr>
    <!-- END BLOCK : fila -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='ban_car_fol_seg.php'" />&nbsp;&nbsp;
    <input type="button" class="boton" value="Actualizar" onclick="validar()" />
  </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function carta(id) {
	var url = '_carta.php'
	var param = '?id=' + id;
	var win;
	
	win = window.open(url + param, 'carta', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function detalle(id) {
	var url = 'reporte_asunto.php'
	var param = '?id=' + id;
	var win;
	
	win = window.open(url + param, 'reporte', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function scan(id) {
	var url = 'ban_car_fol_scan.php'
	var param = '?id=' + id;
	var win;
	
	win = window.open(url + param, 'scan', 'toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768');
	win.focus();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.focus();
	else if (keyCode == 37 && lt && lt != null) lt.focus();
	else if (keyCode == 39 && rt && rt != null) rt.focus();
	else if (keyCode == 38 && up && up != null) up.focus();
	else if (keyCode == 40 && dn && dn != null) dn.focus();
}

function validarFechaRespuesta(folio, fecha) {
}

function buscarFolio(folio) {
}

function validar() {
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.fecha_respuesta[0].select();
//-->
</script>
<!-- END BLOCK : resultado -->
</body>
</html>
