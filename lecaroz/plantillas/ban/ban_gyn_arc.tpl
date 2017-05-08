<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Importar Archivo de Gastos y Nominas</p>
  <form action="./ban_gyn_arc.php" method="post" enctype="multipart/form-data" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Archivo</th>
      <td class="vtabla"><input name="archivo" type="file" class="vinsert" id="archivo" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fecha</th>
      <td class="vtabla"><input name="fecha" type="text" class="insert" id="fecha" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_pro.select()" value="{fecha}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) codgastos.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="disabled" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="if (event.keyCode == 13) concepto.select()" size="3" />
        <input name="desc" type="text" disabled="disabled" class="vnombre" id="desc" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Concepto</th>
      <td class="vtabla"><input name="concepto" type="text" class="vinsert" id="concepto" onkeydown="if (event.keyCode == 13) fecha.select()" size="50" maxlength="50" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select>      </td>
    </tr>
  </table>
  <p style=" font-family:Arial, Helvetica, sans-serif; font-weight:bold;">NOTA: El formato del archivo debe ser 'CSV delimitado por comas', sin t&iacute;tulos ni encabezados y los importes no deben contener 'coma' como separador de miles. Las columnas del archivo deben ir en el siguiente orden: (1) N&uacute;mero Compa&ntilde;&iacute;a, (2) Importe</p>
  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_gyn_arc.php', 'GET', 'pro=' + get_val(f.num_pro), resultPro);
	}
}

var resultPro = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	else
		f.nombre_pro.value = result;
}

function cambiaCod() {
	if (f.codgastos.value == '' || f.codgastos.value == '0') {
		f.codgastos.value = '';
		f.desc.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_gyn_arc.php', 'GET', 'cod=' + get_val(f.codgastos), resultCod);
	}
}

var resultCod = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('El código no se encuentra en el catálogo');
		f.codgastos.value = f.tmp.value;
		f.codgastos.select();
	}
	else
		f.desc.value = result;
}

function validar() {
	if (f.fecha.length < 8) {
		alert('Debe especificar la fecha');
		f.fecha.select();
	}
	else if (get_val(f.codgastos) == 0) {
		alert('Debe especificar el gasto');
		f.codgastos.select();
	}
	else if (f.concepto.length < 3) {
		alert('Debe poner un concepto');
		f.concepto.select();
	}
	else if (f.archivo.value.length < 3) {
		alert('Debe especificar la ruta del archivo de datos');
		f.archivo.focus();
	}
	else if (confirm('¿Son correctos todos los datos?'))
		f.submit();
}

window.onload = f.fecha.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->

<!-- END BLOCK : listado -->
</body>
</html>
