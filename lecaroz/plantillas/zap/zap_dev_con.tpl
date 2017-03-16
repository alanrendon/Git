<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Devoluciones</p>
  <form action="./zap_dev_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="true" class="vnombre" id="nombre_pro" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Criterio de busqueda </th>
      <td class="vtabla"><input name="criterio" type="radio" value="1" checked="checked" />
        Pendientes
          <br />
          <input name="criterio" type="radio" value="2" onclick="folio.select()" />
          Por folio
          <input name="folio" type="text" class="insert" id="folio" size="5" /> </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Consultar por </th>
      <td class="vtabla"><input name="orden" type="radio" value="1" checked="checked" />
        Compa&ntilde;&iacute;a<br />
          <input name="orden" type="radio" value="2" />
          Proveedor</td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre_cia.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre_cia.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaPro() {
	if (f.num_pro.value == '' || f.num_pro.value == '0') {
		f.num_pro.value = '';
		f.nombre_pro.value = '';
	}
	else if (pro[get_val(f.num_pro)] != null)
		f.nombre_pro.value = pro[get_val(f.num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		f.num_pro.value = f.tmp.value;
		f.num_pro.select();
	}
}

function validar() {
	// if (f.criterio.value == 2 && get_val(f.folio) <= 0) {
	// 	alert('Debe especificar el folio del vale a buscar');
	// 	f.folio.select();
	// 	return false;
	// }

	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Devoluciones</p>
  <form action="" method="post" name="form"><table class="tabla">
    <!-- START BLOCK : bloque -->
	<tr>
      <th colspan="9" class="vtabla" scope="col" style="font-size:14pt;">{num} {nombre} </th>
      </tr>
    <tr>
      <td colspan="9" class="tabla">&nbsp;</td>
    </tr>
    <!-- START BLOCK : subbloque -->
	<tr>
      <th colspan="9" class="vtabla" style="font-size:12pt;">{num} {nombre} </th>
      </tr>
	<!-- START BLOCK : vale -->
	<tr>
	  <th colspan="3" class="vtabla" style="font-size:12pt;">Folio: {folio} </th>
	  <th colspan="3" class="vtabla" style="font-size:12pt;">Cheque: {cheque} </th>
	  <th colspan="3" class="vtabla" style="font-size:12pt;">Banco: {banco} </th>
	  </tr>
    <tr>
      <th class="tabla"><input type="checkbox" name="checkbox" value="checkbox" /></th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Modelo</th>
      <th class="tabla">Color</th>
      <th class="tabla">Talla</th>
      <th class="tabla">Piezas</th>
      <th class="tabla">Precio</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Observaciones</th>
      </tr>
    <!-- START BLOCK : fila -->
	<tr onmouseover="overTR(this,'#ACD2DD');" onmouseout="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"{dis} /></td>
      <td class="tabla">{fecha}</td>
      <td class="vtabla">{modelo}</td>
      <td class="vtabla">{color}</td>
      <td class="rtabla">{talla}</td>
      <td class="rtabla">{piezas}</td>
      <td class="rtabla">{precio}</td>
      <td class="rtabla">{importe}</td>
      <td class="vtabla">{obs}</td>
      </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="5" class="rtabla">Total vale</th>
      <th class="rtabla">{piezas}</th>
      <th class="tabla">&nbsp;</th>
      <th class="rtabla">{total}</th>
      <th class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <td colspan="9" class="tabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : vale -->
	<tr>
      <th colspan="5" class="rtabla">Totales</th>
      <th class="rtabla">{piezas}</th>
      <th class="tabla">&nbsp;</th>
      <th class="rtabla">{total}</th>
      <th class="tabla">&nbsp;</th>
      </tr>
	<!-- END BLOCK : subbloque -->
    <tr>
      <td colspan="9" class="tabla">&nbsp;</td>
      </tr>
	<!-- END BLOCK : bloque -->
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./zap_dev_con.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Borrar" onclick="borrar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function borrar() {
	var data = '';

	if (f.id.length == undefined)
		data += f.id.checked ? 'id[]=' + f.id.value : '';
	else
		for (var i = 0; i < f.id.length; i++)
			data += f.id[i].checked ? (data == '' ? '' : '&') + 'id[]=' + f.id[i].value : '';

	if (data == '') {
		alert('Debe seleccionar al menos un registro');
		return false;
	}

	if (!confirm('¿Desea borrar los registros seleccionados?'))
		return false;

	var myConn = new XHConn();

	if (!myConn)
		alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");

	// Formar cadena de datos

	// Mandar datos
	myConn.connect("./zap_dev_con.php", "POST", data, Reload);
}

var Reload = function (oXML) {
	document.location.reload();
}
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
