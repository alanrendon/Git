<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Proveedores </p>
  <form action="./fac_pro_mod_v3.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro()" onkeyup="if (event.keyCode == 13) next.focus()" size="3" />
        <input name="nombre" type="text" class="vnombre" id="nombre" size="40" /></td>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
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
		myConn.connect('./fac_pro_mod_v3.php', 'GET', 'p=' + get_val(f.num_pro), resultPro);
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
		f.nombre.value = result;
}

function validar() {
	if (f.num_pro.value == '')
		alert('Debe especificar el proveedor a modificar');
	else
		f.submit();
}

window.onload = f.num_pro.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Proveedores</p>
  <form action="./fac_pro_mod_v3.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th colspan="6" class="vtabla">DATOS GENERALES</th>
      </tr>
    <tr>
      <th class="vtabla">N&uacute;mero</th>
      <td class="vtabla"><input name="num_proveedor" type="text" class="nombre" id="num_proveedor" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeyup="movCursor(event.keyCode,clave_seguridad,null,clave_seguridad,null,rfc)" value="{num_proveedor}" size="4" readonly="true" /></td>
      <th class="vtabla">Clave</th>
      <td class="vtabla"><input name="clave_seguridad" type="text" class="insert" id="clave_seguridad" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) validarClave()" onkeyup="movCursor(event.keyCode,nombre,num_proveedor,nombre,null,rfc)" value="{clave_seguridad}" size="3" /></td>
      <th class="vtabla">Nombre</th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onchange="toText(this)" onkeyup="movCursor(event.keyCode,rfc,clave_seguridad,null,null,direccion)" value="{nombre}" size="80" /></td>
    </tr>
    <tr>
      <th class="vtabla">R.F.C.</th>
      <td colspan="3" class="vtabla"><input name="rfc" type="text" class="vinsert" id="rfc" onfocus="tmp.value=this.value;this.select()" onchange="validarRFC(this)" onkeyup="movCursor(event.keyCode,direccion,null,direccion,nombre,telefono2)" value="{rfc}" size="13" maxlength="13" /></td>
      <th rowspan="2" class="vtabla">Direcci&oacute;n</th>
      <td rowspan="2" class="vtabla"><textarea name="direccion" cols="40" rows="3" class="insert" id="direccion" style="width:100%;" onblur="toText(this)" onkeyup="movCursor(event.keyCode,telefono1,null,null,null,null)">{direccion}</textarea></td>
    </tr>
    <tr>
      <th class="vtabla">Tipo Persona </th>
      <td colspan="3" class="vtabla"><input name="tipopersona" type="radio" value="TRUE"{tipopersona_t} />
        F&iacute;sica
          <input name="tipopersona" type="radio" value="FALSE"{tipopersona_f} />
          Moral</td>
      </tr>
    <tr>
      <th class="vtabla">Tel&eacute;fono 1 </th>
      <td colspan="3" class="vtabla"><input name="telefono1" type="text" class="vinsert" id="telefono1" onfocus="tmp.value=this.value;this.value=this.value.replace(/[^\d]/g, '');this.select()" onblur="validarTel(this)" onkeyup="movCursor(event.keyCode,telefono2,null,telefono2,rfc,fax)" value="{telefono1}" size="20" maxlength="20" /></td>
      <th class="vtabla">Tel&eacute;fono 2 </th>
      <td class="vtabla"><input name="telefono2" type="text" class="vinsert" id="telefono2" onfocus="tmp.value=this.value;this.value=this.value.replace(/[^\d]/g, '');this.select()" onblur="validarTel(this)" onkeyup="movCursor(event.keyCode,fax,telefono1,null,direccion,email)" value="{telefono2}" size="20" maxlength="20" /></td>
    </tr>
    <tr>
      <th class="vtabla">Fax</th>
      <td colspan="3" class="vtabla"><input name="fax" type="text" class="vinsert" id="fax" onfocus="tmp.value=this.value;this.value=this.value.replace(/[^\d]/g, '');this.select()" onblur="validarTel(this)" onkeyup="movCursor(event.keyCode,email,null,email,telefono1,contacto)" value="{fax}" size="20" maxlength="20" />        </td>
      <th class="vtabla">E-mail</th>
      <td class="vtabla"><input name="email" type="text" class="vinsert" id="email" style="text-transform:none;" onfocus="tmp.value=this.value;this.select()" onblur="validarEmail(this)" onkeyup="movCursor(event.keyCode,contacto,fax,null,telefono2,cuenta)" value="{email}" size="30" maxlength="50" /></td>
    </tr>
    <tr>
      <th class="vtabla">Contacto</th>
      <td colspan="3" class="vtabla"><input name="contacto" type="text" class="vinsert" id="contacto" onchange="toText(this)" onkeyup="movCursor(event.keyCode,cuenta,null,null,fax,cuenta)" value="{contacto}" size="30" maxlength="100" /></td>
      <th class="vtabla">Tipo</th>
      <td class="vtabla"><select name="idtipoproveedor" class="insert" id="idtipoproveedor">
        <option value="0"{idtipoproveedor_0}>PROVEEDOR</option>
        <option value="1"{idtipoproveedor_1}>OTROS</option>
        <option value="2"{idtipoproveedor_2}>EMPAQUE</option>
            </select></td>
    </tr>
    <tr>
      <th class="vtabla">Prioridad</th>
      <td colspan="3" class="vtabla"><input name="prioridad" type="radio" value="TRUE"{prioridad_t} />
        Alta
          <input name="prioridad" type="radio" value="FALSE"{prioridad_f} />
          Baja</td>
      <th class="vtabla">Resta compras </th>
      <td class="vtabla"><input name="restacompras" type="radio" value="TRUE"{restacompras_t} />
        Si
          <input name="restacompras" type="radio" value="FALSE"{restacompras_f} />
          No</td>
    </tr>
    <tr>
      <th class="vtabla">Validar facturas </th>
      <td colspan="3" class="vtabla"><input name="verfac" type="radio" value="TRUE"{verfac_t} />
        Si
          <input name="verfac" type="radio" value="FALSE"{verfac_f} />
          No</td>
      <th class="vtabla">Tipo de documentaci&oacute;n </th>
      <td class="vtabla"><input name="tipo_doc" type="radio" value="2"{tipo_doc_2} />
        Factura
          <input name="tipo_doc" type="radio" value="1"{tipo_doc_1} />
          Remisi&oacute;n</td>
    </tr>
    <tr>
      <th class="vtabla">Observaciones</th>
      <td colspan="5" class="vtabla"><textarea name="observaciones" rows="4" class="insert" id="observaciones" style="width:100%;">{observaciones}</textarea></td>
      </tr>
    <tr>
      <td colspan="6" class="vtabla">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="6" class="vtabla">DATOS DE PAGO </th>
    </tr>
    <tr>
      <th class="vtabla">Forma de pago </th>
      <td colspan="3" class="vtabla"><input name="idtipopago" type="radio" value="1"{idtipopago_1} />
        Cr&eacute;dito
          <input name="idtipopago" type="radio" value="2"{idtipopago_2} />
          Contado</td>
      <th class="vtabla">Tipo de pago </th>
      <td class="vtabla"><input name="trans" type="radio" value="FALSE"{trans_f} />
        Cheque
          <input name="trans" type="radio" value="TRUE"{trans_t} />
          Transferencia electr&oacute;nica </td>
    </tr>
    <tr>
      <th class="vtabla">Banco</th>
      <td colspan="3" class="vtabla"><select name="idbanco" class="insert" id="idbanco">
        <option value=""{idbanco}></option>
		<!-- START BLOCK : banco -->
		<option value="{idbanco}"{selected}>[{num}][{clave}] {nombre}</option>
		<!-- END BLOCK : banco -->
      </select>      </td>
      <th class="vtabla">Leyendas</th>
      <td class="vtabla"><input name="para_abono" type="checkbox" id="para_abono" value="TRUE"{para_abono} />
        Para abono a cuenta </td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta (11 d&iacute;gitos) </th>
      <td colspan="3" class="vtabla"><input name="cuenta" type="text" class="vinsert" id="cuenta" onfocus="tmp.value=this.value;this.select()" onblur="validarCampoNum(this,11)" onkeyup="movCursor(event.keyCode,clabe,null,clabe,diascredito,plaza_banxico)" value="{cuenta}" size="11" maxlength="11" /></td>
      <th class="vtabla">CLABE (18 d&iacute;gitos) </th>
      <td class="vtabla"><input name="clabe" type="text" class="vinsert" id="clabe" onfocus="tmp.value=this.value;this.select()" onblur="validarCampoNum(this,18)" onkeyup="movCursor(event.keyCode,sucursal,cuenta,null,email,sucursal)" value="{clabe}" size="18" maxlength="18" /></td>
    </tr>
    <tr>
      <th class="vtabla">Entidad</th>
      <td colspan="3" class="vtabla"><select name="IdEntidad" class="insert" id="IdEntidad">
        <option value=""{idEntidad}></option>
		<!-- START BLOCK : entidad -->
		<option value="{IdEntidad}"{selected}>{Entidad}</option>
		<!-- END BLOCK : entidad -->
      </select>      </td>
      <th class="vtabla">Sucursal</th>
      <td class="vtabla"><input name="sucursal" type="text" class="vinsert" id="sucursal" onfocus="tmp.value=this.value;this.select()" onblur="validarCampoNum(this,4)" onkeyup="movCursor(event.keyCode,plaza_banxico,null,diascredito,clabe,diascredito)" value="{sucursal}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla">Plaza Banxico </th>
      <td colspan="3" class="vtabla"><input name="plaza_banxico" type="text" class="vinsert" id="plaza_banxico" onfocus="tmp.value=this.value;this.select()" onblur="validarCampoNum(this,5)" onkeyup="movCursor(event.keyCode,diascredito,null,diascredito,cuenta,referencia)" value="{plaza_banxico}" size="5" maxlength="5" /></td>
      <th class="vtabla">D&iacute;as de cr&eacute;dito </th>
      <td class="vtabla"><input name="diascredito" type="text" class="insert" id="diascredito" onfocus="tmp.value=this.value;this.select()" onblur="isInt(this,tmp)" onkeyup="movCursor(event.keyCode,referencia,plaza_banxico,null,sucursal,contacto1)" value="{diascredito}" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <th class="vtabla">Referencia</th>
      <td colspan="3" class="vtabla"><input name="referencia" type="text" class="vinsert" id="referencia" onfocus="tmp.value=this.value;this.select()" onblur="toText(this)" onkeyup="movCursor(event.keyCode,contacto1,null,null,plaza_banxico,contacto1)" value="{referencia}" size="10" maxlength="10" /></td>
      <th class="vtabla">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
    <!-- START BLOCK : zap -->
	<tr>
      <td colspan="6" class="vtabla">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="6" class="vtabla">DATOS DE CONTACTO </th>
      </tr>
    <tr>
      <th class="vtabla">Contacto 1 </th>
      <td colspan="5" class="vtabla"><input name="contacto1" type="text" class="vinsert" id="contacto1" style="width:100%;" onchange="toText(this)" onkeyup="movCursor(event.keyCode,contacto2,null,null,referencia,contacto2)" value="{contacto1}" maxlength="255" /></td>
      </tr>
    <tr>
      <th class="vtabla">Contacto 2 </th>
      <td colspan="5" class="vtabla"><input name="contacto2" type="text" class="vinsert" id="contacto2" style="width:100%;" onchange="toText(this)" onkeyup="movCursor(event.keyCode,contacto3,null,null,contacto1,contacto3)" value="{contacto2}" maxlength="255" /></td>
      </tr>
    <tr>
      <th class="vtabla">Contacto 3 </th>
      <td colspan="5" class="vtabla"><input name="contacto3" type="text" class="vinsert" id="contacto3" style="width:100%;" onchange="toText(this)" onkeyup="movCursor(event.keyCode,contacto4,null,null,contacto2,contacto4)" value="{contacto3}" maxlength="255" /></td>
      </tr>
    <tr>
      <th class="vtabla">Contacto 4 </th>
      <td colspan="5" class="vtabla"><input name="contacto4" type="text" class="vinsert" id="contacto4" style="width:100%;" onchange="toText(this)" onkeyup="movCursor(event.keyCode,desc1,null,null,contacto3,desc1)" value="{contacto4}" maxlength="255" /></td>
      </tr>
    <tr>
      <td colspan="6" class="vtabla">&nbsp;</td>
      </tr>
    <tr>
      <th colspan="6" class="vtabla">DESCUENTOS DE PROVEEDORES</th>
      </tr>
    <tr>
      <th class="vtabla">Descuento 1 </th>
      <td colspan="3" class="vtabla"><input name="desc1" type="text" class="rinsert" id="desc1" onfocus="tmp.value=this.value;this.select()" onblur="inputFormat(this,2)" onkeyup="movCursor(event.keyCode,cod_desc1,null,cod_desc1,contacto4,desc2)" value="{desc1}" size="6" maxlength="6" /></td>
      <th class="vtabla">Concepto 1 </th>
      <td class="vtabla"><input name="cod_desc1" type="text" class="insert" id="cod_desc1" onfocus="tmp.value=this.value;this.select()" onblur="if (isInt(this,tmp)) cambiaCodDesc(1)" onkeyup="movCursor(event.keyCode,desc2,desc1,null,contacto4,cod_desc2)" value="{cod_desc1}" size="2" />
        <input name="con_desc1" type="text" class="vnombre" id="con_desc1" value="{con_desc1}" size="40" readonly="true" />
        <input name="tipo_desc1" type="text" disabled="disabled" class="vnombre" id="tipo_desc1" value="{tipo_desc1}" size="20" /></td>
    </tr>
	<tr>
      <th class="vtabla">Descuento 2 </th>
      <td colspan="3" class="vtabla"><input name="desc2" type="text" class="rinsert" id="desc2" onfocus="tmp.value=this.value;this.select()" onblur="inputFormat(this,2)" onkeyup="movCursor(event.keyCode,cod_desc2,null,cod_desc2,desc1,desc3)" value="{desc2}" size="6" maxlength="6" /></td>
      <th class="vtabla">Concepto 2 </th>
      <td class="vtabla"><input name="cod_desc2" type="text" class="insert" id="cod_desc2" onfocus="tmp.value=this.value;this.select()" onblur="if (isInt(this,tmp)) cambiaCodDesc(2)" onkeyup="movCursor(event.keyCode,desc3,desc2,null,cod_desc1,cod_desc3)" value="{cod_desc2}" size="2" />
        <input name="con_desc2" type="text" class="vnombre" id="con_desc2" value="{con_desc2}" size="40" readonly="true" />
        <input name="tipo_desc2" type="text" disabled="disabled" class="vnombre" id="tipo_desc2" value="{tipo_desc2}" size="20" /></td>
    </tr>
	<tr>
      <th class="vtabla">Descuento 3 </th>
      <td colspan="3" class="vtabla"><input name="desc3" type="text" class="rinsert" id="desc3" onfocus="tmp.value=this.value;this.select()" onblur="inputFormat(this,2)" onkeyup="movCursor(event.keyCode,cod_desc3,null,cod_desc3,desc2,desc4)" value="{desc3}" size="6" maxlength="6" /></td>
      <th class="vtabla">Concepto 3 </th>
      <td class="vtabla"><input name="cod_desc3" type="text" class="insert" id="cod_desc3" onfocus="tmp.value=this.value;this.select()" onblur="if (isInt(this,tmp)) cambiaCodDesc(3)" onkeyup="movCursor(event.keyCode,desc4,desc3,null,cod_desc2,cod_desc4)" value="{cod_desc3}" size="2" />
        <input name="con_desc3" type="text" class="vnombre" id="con_desc3" value="{con_desc3}" size="40" readonly="true" />
        <input name="tipo_desc3" type="text" disabled="disabled" class="vnombre" id="tipo_desc3" value="{tipo_desc3}" size="20" /></td>
    </tr>
	<tr>
      <th class="vtabla">Descuento 4 </th>
      <td colspan="3" class="vtabla"><input name="desc4" type="text" class="rinsert" id="desc4" onfocus="tmp.value=this.value;this.select()" onblur="inputFormat(this,2)" onkeyup="movCursor(event.keyCode,cod_desc4,null,cod_desc4,desc3,null)" value="{desc4}" size="6" maxlength="6" /></td>
      <th class="vtabla">Concepto 4 </th>
      <td class="vtabla"><input name="cod_desc4" type="text" class="insert" id="cod_desc4" onfocus="tmp.value=this.value;this.select()" onblur="if (isInt(this,tmp)) cambiaCodDesc(4)" onkeyup="movCursor(event.keyCode,nombre,desc4,null,cod_desc4,null)" value="{cod_desc4}" size="2" />
        <input name="con_desc4" type="text" class="vnombre" id="con_desc4" value="{cod_desc4}" size="40" readonly="true" />
        <input name="tipo_desc4" type="text" disabled="disabled" class="vnombre" id="tipo_desc4" value="{tipo_desc4}" size="20" /></td>
    </tr>
	<!-- END BLOCK : zap -->
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='fac_pro_mod_v3.php'" />
    &nbsp;&nbsp;
    <input type="button" class="boton" value="Modificar" onclick="validar()" />
</p>
  </form></td>
</tr>
</table>
<!-- START IGNORE -->
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validarClave() {
	if (f.clave_seguridad.value == '' || f.clave_seguridad.value == '0') {
		f.clave_seguridad.value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_pro_mod_v3.php', 'GET', 'cs=' + get_val(f.clave_seguridad), resultClave);
	}
}

var resultClave = function (oXML) {
	var result = oXML.responseText;
	
	if (result == '0') {
		alert('No existe la clave en el catálogo de nombres');
		f.clave_seguridad.value = f.tmp.value;
		f.clave_seguridad.select();
	}
}

function validarCampoNum(obj, longitud) {
	if (obj.value == '')
		return true;
	
	obj.value = obj.value.replace(/[^\d]/g, '');
	
	if (obj.value.length != longitud) {
		alert('La longitud del campo debe ser de ' + longitud + ' dígitos');
		
		obj.value = f.tmp.value;
		obj.select();
	}
}

function cambiaCodDesc(i) {
	if (document.getElementById('cod_desc' + i).value == '' || document.getElementById('cod_desc' + i).value == '0') {
		document.getElementById('cod_desc' + i).value = '';
		document.getElementById('con_desc' + i).value = '';
		document.getElementById('tipo_desc' + i).value = '';
	}
	else {
		var myConn = new XHConn();
	
		if (!myConn)
			alert("XMLHTTP no disponible. Trate con un nuevo/mejor navegador.");
		
		// Pedir datos
		myConn.connect('./fac_pro_mod_v3.php', 'GET', 'cod=' + get_val(document.getElementById('cod_desc' + i)) + '&i=' + i, resultCod);
	}
}

var resultCod = function (oXML) {
	var result = oXML.responseText.split('|');
	
	if (get_val2(result[0]) < 0) {
		alert('No existe el código en el catálogo de conceptos');
		document.getElementById('cod_desc' + abs(get_val2(result[0]))).value = f.tmp.value;
		document.getElementById('cod_desc' + abs(get_val2(result[0]))).select();
	}
	else {
		document.getElementById('con_desc' + result[0]).value = result[1];
		document.getElementById('tipo_desc' + result[0]).value = result[2];
	}
}

function validar() {
	if (f.nombre.value.length < 3) {
		alert('Debe especificar el nombre del proveedor');
		f.nombre.select();
		return false;
	}
	else if (f.trans[1].checked) {
		if (f.idbanco.value == '') {
			alert('Debe seleccionar un banco');
			f.idbanco.focus();
			return false;
		}
		if (f.cuenta.value.replace(/[^a-zA-Z0-9\s]/g, '').length < 11) {
			alert('El número de cuenta debe ser de 11 dígitos');
			f.cuenta.select();
			return false;
		}
		if (f.clabe.value.replace(/[^a-zA-Z0-9\s]/g, '').length < 18) {
			alert('La CLABE debe ser de 18 dígitos');
			f.cuenta.select();
			return false;
		}
		if (f.sucursal.value.replace(/[^a-zA-Z0-9\s]/g, '').length < 4) {
			alert('El número de sucursal debe ser de 4 dígitos');
			f.sucursal.select();
			return false;
		}
		if (f.plaza_banxico.value.replace(/[^a-zA-Z0-9\s]/g, '').length < 5) {
			alert('La plaza banxico debe ser de 5 dígitos');
			f.plaza_banxico.select();
			return false;
		}
		if (f.IdEntidad.value == '') {
			alert('Debe especificar la entidad');
			f.IdEntidad.focus();
			return false;
		}
		if (f.rfc.value.length < 9) {
			alert('Debe especificar el RFC del proveedor');
			f.rfc.select();
			return false;
		}
		if (f.telefono1.value.length < 8) {
			alert('Debe especificar al menos un teléfono');
			f.telefono1.select();
			return false;
		}
	}
	
	if (confirm('¿Son correctos los datos?'))
		f.submit();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && dn != null) dn.select();
}

window.onload = function () {
	if (f.cod_desc1) {
		for (var i = 1; i <= 4; i++)
			cambiaCodDesc(i);
	}
	
	f.clave_seguridad.select();
}
//-->
</script>
<!-- END IGNORE -->
<!-- END BLOCK : mod -->
</body>
</html>
