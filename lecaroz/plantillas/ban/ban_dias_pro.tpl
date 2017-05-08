<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Promedio de D&iacute;as Transcurridos para Pagos</p>
  <form action="ban_dias_pro.php" method="get" name="form">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) meses.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Meses</th>
      <td class="vtabla"><input name="meses" type="text" class="insert" id="meses" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_pro.select()" value="2" size="3" /></td>
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

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./ban_dias_pro.php', 'GET', 'p=' + get_val(f.num_pro), obtenerPro);
	}
}

var obtenerPro = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '') {
		alert('La compañía no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
	else
		f.nombre.value = result;
}

function validar()
{
	f.submit();
}

window.onload = f.num_pro.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<style type="text/css" media="print">
.noDisplay {
	display:none;
}
</style>
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">D&iacute;as transcurridos para pagos<br />
    en un periodo de {meses}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Proveedor</th>
    <th class="print" scope="col">D&iacute;as</th>
    <th class="print" scope="col">M&aacute;ximo</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_pro} {nombre}</td>
    <td class="print">{dias}</td>
    <td class="print">{maximo}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<!-- START BLOCK : porcentajes -->
<br />
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">D&iacute;as</th>
    <th class="print" scope="col">Facturas</th>
    <th class="print" scope="col">%</th>
  </tr>
  <!-- START BLOCK : por -->
  <tr>
    <td class="print">{dias}</td>
    <td class="print">{facs}</td>
    <td class="rprint">{por}</td>
  </tr>
  <!-- END BLOCK : por -->
  <tr>
    <th class="print">&nbsp;</th>
    <th class="print">{facs}</th>
    <th class="rprint">&nbsp;</th>
  </tr>
</table>
<!-- END BLOCK : porcentajes -->
<p align="center" class="noDisplay">
  <input type="button" class="boton" value="Regresar" onclick="document.location='ban_dias_pro.php'" />
</p>
<!-- END BLOCK : listado -->
</body>
</html>
