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
<td align="center" valign="middle"><p class="title">Consulta de Facturas</p>
  <form action="./zap_fac_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) num_pro.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="true" class="vnombre" id="nombre_cia" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeydown="if (event.keyCode == 13) clave.select()" size="3" />
        <input name="nombre_pro" type="text" disabled="true" class="vnombre" id="nombre_pro" size="40" />
        <input name="clave" type="text" class="insert" id="clave" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_fact.select()" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Factura</th>
      <td class="vtabla"><input name="num_fact" type="text" class="insert" id="num_fact" onfocus="tmp.value=this.value;this.select()" onchange="this.value=this.value.trim().toUpperCase();" onkeydown="if (event.keyCode == 13) fecha1.select()" size="8" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha_ent1.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo de Entrega </th>
      <td class="vtabla"><input name="fecha_ent1" type="text" class="insert" id="fecha_ent1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha_ent2.select()" size="10" maxlength="10" />
        al
          <input name="fecha_ent2" type="text" class="insert" id="fecha_ent2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha_inv2.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo de Inventariado </th>
      <td class="vtabla"><input name="fecha_inv1" type="text" class="insert" id="fecha_inv1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha_inv2.select()" size="10" maxlength="10" />
        al
          <input name="fecha_inv2" type="text" class="insert" id="fecha_inv2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) codgastos.select()" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Gasto</th>
      <td class="vtabla"><input name="codgastos" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod()" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="desc" type="text" disabled="disabled" class="vnombre" id="desc" size="40" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Criterio</th>
      <td class="vtabla"><input name="criterio" type="radio" value="0" checked="checked" />
        Todas<br />
		<input name="criterio" type="radio" value="1" />
        Pendientes<br />        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="tipo_pen" type="radio" value="0" checked="checked" />
        Todos<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="tipo_pen" type="radio" value="1" />
        Pendientes<br />        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="tipo_pen" type="radio" value="2" />
        Acreditados<br />
        <input name="criterio" type="radio" value="2" />
        Pagadas        </td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de documento</th>
      <td class="vtabla">
        <input type="radio" name="tipo" value="" checked="checked" /> Todos<br />
        <input type="radio" name="tipo" value="1" /> Facturas<br />
        <input type="radio" name="tipo" value="2" /> Remisiones
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Orden</th>
      <td class="vtabla"><input name="orden" type="radio" value="1" checked="checked" />
        Compa&ntilde;&iacute;a
          <input name="orden" type="radio" value="2" />
          Proveedor</td>
    </tr>
  </table>
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), pro = new Array(), gas = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->
<!-- START BLOCK : cod -->
gas[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

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

function cambiaCod() {
	if (f.codgastos.value == '' || f.codgastos.value == '0') {
		f.codgastos.value = '';
		f.desc.value = '';
	}
	else if (gas[get_val(f.codgastos)] != null)
		f.desc.value = gas[get_val(f.codgastos)];
	else {
		alert('El código no se encuentra en el catálogo');
		f.codgastos.value = f.tmp.value;
		f.codgastos.select();
	}
}

function validar() {
	f.submit();
}

window.onload = function () { showAlert = true; f.num_cia.select(); };
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-core-1.4.5.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/mootools-more-1.4.0.1.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/string.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/number.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/array.implement.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Core.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Modal.Confirm.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mbox/mBox.Tooltip.js"></script>
<script type="text/javascript" src="/lecaroz/jscripts/mootools1.4/FormValidator.js"></script>
<style type="text/css">
.icono {
  opacity: 0.6;
}
.icono:hover {
  opacity: 1;
  cursor: pointer;
}
</style>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Facturas </p>
  <form action="" method="get" name="form"><table class="tabla">
    <!-- START BLOCK : bloque -->
	<tr>
      <th colspan="21" class="vtabla" scope="col" style="font-size:14pt;">{num} {nombre} </th>
      </tr>
    <tr>
      <td colspan="21" class="tabla">&nbsp;</td>
      </tr>
    <!-- START BLOCK : subbloque -->
	<tr>
      <th colspan="21" class="vtabla" style="font-size:12pt;">{num} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla"><input type="checkbox" /></th>
      <th class="tabla">Factura</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Recibido</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Gasto</th>
      <th class="tabla">Importe</th>
      <th class="tabla">Faltantes</th>
      <th class="tabla">Dif.<br />
        Precio </th>
      <th class="tabla">Devoluciones</th>
      <th class="tabla">Descuentos</th>
      <th class="tabla">I.V.A.</th>
      <th class="tabla">Retenciones</th>
      <th class="tabla">Fletes</th>
      <th class="tabla">Otros</th>
      <th class="tabla">Total</th>
      <th class="tabla">Cheque</th>
      <th class="tabla">Banco</th>
      <th class="tabla">Conciliado</th>
      <th class="tabla">&nbsp;</th>
      <th class="tabla"><img src="iconos/article_text.png" /></th>
    </tr>
    <!-- START BLOCK : fila -->
	<tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}"{dis} /></td>
      <td class="rtabla">{num_fact}</td>
      <td class="tabla">{fecha}</td>
      <td class="tabla">{fecha_rec}</td>
      <td class="vtabla">{concepto}</td>
      <td class="vtabla">{codgastos} {desc} </td>
      <td class="rtabla" style="font-weight:bold; color:#0000CC;">{importe}</td>
      <td class="rtabla" style="font-weight:bold; color:#006600;">{faltantes}</td>
      <td class="rtabla" style="font-weight:bold; color:#006600;">{dif_precio}</td>
      <td class="rtabla" style="font-weight:bold; color:#006600;">{dev}</td>
      <td class="rtabla" style="font-weight:bold; color:#006600;">{descuentos}</td>
      <td class="rtabla" style="font-weight:bold; color:#CC0000;">{iva}</td>
      <td class="rtabla" style="font-weight:bold; color:#0000CC;">{ret}</td>
      <td class="rtabla" style="font-weight:bold; color:#0000CC;">{fletes}</td>
      <td class="rtabla" style="font-weight:bold; color:#0000CC;">{otros}</td>
      <td class="rtabla" style="font-weight:bold; color:#0000CC;">{total}</td>
      <td class="rtabla">{cheque}</td>
      <td class="vtabla">{banco}</td>
      <td class="tabla" style="color:#CC0000;">{fecha_con}</td>
      <td class="tabla"><input type="button" class="boton" value="..." onclick="mod({id})"{dis} /></td>
	    <td class="tabla">
        <img src="/lecaroz/iconos/magnify{cfd_disabled}.png" alt="{id}" name="visualizar"{icono_class} style="margin-right:4px;" id="visualizar" width="16" height="16" />
        <img src="/lecaroz/iconos/printer{cfd_disabled}.png" alt="{id}" name="imprimir"{icono_class} style="margin-right:4px;" id="imprimir" width="16" height="15" />
        <img src="/lecaroz/iconos/download{cfd_disabled}.png" alt="{id}" name="descargar"{icono_class} id="descargar" width="16" height="16" />
      </td>
	</tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="6" class="rtabla">Total</th>
      <th class="rtabla">{importe}</th>
      <th class="rtabla">{faltantes}</th>
      <th class="rtabla">{dif_precio}</th>
      <th class="rtabla">{dev}</th>
      <th class="rtabla">{descuentos}</th>
      <th class="rtabla">{iva}</th>
      <th class="rtabla">{ret}</th>
      <th class="rtabla">{fletes}</th>
      <th class="rtabla">{otros}</th>
      <th class="rtabla" style="font-size:12pt;">{total}</th>
      <th colspan="5" class="tabla">&nbsp;</th>
      </tr>
    <tr>
      <td colspan="21" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : subbloque -->
    <tr>
      <th colspan="6" class="rtabla">Total de Facturas </th>
      <th class="rtabla">{importe}</th>
      <th class="rtabla">{faltantes}</th>
      <th class="rtabla">{dif_precio}</th>
      <th class="rtabla">{dev}</th>
      <th class="rtabla">{descuentos}</th>
      <th class="rtabla">{iva}</th>
      <th class="rtabla">{ret}</th>
      <th class="rtabla">{fletes}</th>
      <th class="rtabla">{otros}</th>
      <th class="rtabla" style="font-size:14pt;">{total}</th>
      <th colspan="5" class="tabla">&nbsp;</th>
      </tr>
	  <tr>
	  <td colspan="21" class="rtabla">&nbsp;</td>
	  </tr>
	<!-- END BLOCK : bloque -->
	<tr>
      <th colspan="6" class="rtabla">Total General </th>
      <th class="rtabla">{importe}</th>
      <th class="rtabla">{faltantes}</th>
      <th class="rtabla">{dif_precio}</th>
      <th class="rtabla">{dev}</th>
      <th class="rtabla">{descuentos}</th>
      <th class="rtabla">{iva}</th>
      <th class="rtabla">{ret}</th>
      <th class="rtabla">{fletes}</th>
      <th class="rtabla">{otros}</th>
      <th class="rtabla" style="font-size:14pt;">{total}</th>
      <th colspan="5" class="tabla">&nbsp;</th>
      </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='zap_fac_con.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Borrar" onclick="borrar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function mod(id) {
	var opt = 'left=0,top=0,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768';
	var url = './zap_fac_mod_v2.php?id=' + id;
	var win = window.open(url, 'mod', opt);
	win.focus();
}

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
	myConn.connect("./zap_fac_con.php", "POST", data, Reload);
}

var Reload = function (oXML) {
	document.location.reload();
}

var visualizar_cfd = function(id)
{
  var url = 'zap_fac_con.php';
  var data = '?visualizar_cfd=1&id=' + id;
  var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768';
  var win = window.open(url + data, 'CFDview', opt);
}

var imprimir_cfd = function(id) {
  new Request({
    'url': 'zap_fac_con.php',
    'data': 'imprimir_cfd=1&id=' + id,
    'onRequest': function() {
    },
    'onSuccess': function() {
    }
  }).send();
}

var descargar_cfd = function(id) {
  var url = 'obtenerCFDProveedorZapaterias.php';
  var data = '?id=' + id;
  var opt = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=5,height=5';
  var win = window.open(url + data, 'CFDdownload', opt);
}

$$('img[id=visualizar][src!=/lecaroz/iconos/magnify_gray.png]').each(function(el) {
  var id = el.get('alt').getNumericValue();

  el.removeProperty('alt').addEvents({
    'click': visualizar_cfd.pass(id)
  });
});

$$('img[id=imprimir][src!=/lecaroz/iconos/printer_gray.png]').each(function(el) {
  var id = el.get('alt').getNumericValue();

  el.removeProperty('alt').addEvents({
    'click': imprimir_cfd.pass(id)
  });
});

$$('img[id=descargar][src!=/lecaroz/iconos/download_gray.png]').each(function(el) {
  var id = el.get('alt').getNumericValue();

  el.removeProperty('alt').addEvents({
    'click': descargar_cfd.pass(id)
  });
});

//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
