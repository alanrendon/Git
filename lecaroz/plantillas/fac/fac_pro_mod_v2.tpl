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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Proveedor</p>
  <form action="./fac_pro_mod_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_pro" type="text" class="insert" id="num_pro" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) next.focus()" size="4" maxlength="4"></td>
    </tr>
  </table>  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.num_pro.value <= 0) {
		alert("Debe especificar el proveedor");
		f.num_pro.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_pro.select();
-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificar Proveedor</p>
  <form action="./fac_pro_mod_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Nombre</th>
      <td colspan="3" class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" style="width:100%;" onKeyDown="if (event.keyCode == 13) referencia.select()" value="{nombre}" size="30" maxlength="100"></td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Proveedor</th>
      <td class="vtabla"><input name="num_proveedor" type="text" class="insert" id="num_proveedor" value="{num_pro}" size="4" maxlength="4" readonly="true"></td>
      <th class="vtabla">Banco</th>
      <td class="vtabla"><select name="idbanco" class="insert" id="idbanco" onChange="if (this.selectedIndex == 8) san[0].checked = true; else san[1].checked = true">
        <!-- START BLOCK : banco -->
		<option value="{id}" {selected}>{nombre}</option>
		<!-- END BLOCK : banco -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Referencia bancaria</th>
      <td class="vtabla"><input name="referencia" type="text" class="vinsert" id="referencia" onFocus="this.select()" onKeyDown="if (event.keyCode == 13) cuenta.select()" value="{referencia}" size="10" maxlength="10"></td>
      <th class="vtabla">CLABE o Cuenta</th>
      <td class="vtabla"><input name="cuenta" type="text" class="vinsert" id="cuenta" onKeyDown="if (event.keyCode == 13) plaza_banxico.select()" value="{cuenta}" size="18" maxlength="18"></td>
    </tr>
    <tr>
      <th rowspan="3" class="vtabla" scope="row">Direcci&oacute;n</th>
      <td rowspan="3" class="vtabla"><textarea name="direccion" cols="28" rows="3" class="insert" id="textarea">{dir}</textarea></td>
      <th class="vtabla">Plaza Banxico </th>
      <td class="vtabla"><input name="plaza_banxico" type="text" class="insert" id="plaza_banxico" onKeyDown="if (event.keyCode == 13) sucursal.select()" value="{plaza}" size="5" maxlength="5"></td>
    </tr>
    <tr>
      <th class="vtabla">Sucursal</th>
      <td class="vtabla"><input name="sucursal" type="text" class="insert" id="sucursal" onKeyDown="if (event.keyCode == 13) diascredito.select()" value="{suc}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla">Cuenta Santander </th>
      <td class="vtabla"><input name="san" type="radio" value="TRUE" {san_t}>
        Si
          <input name="san" type="radio" value="FALSE" {san_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tel&eacute;fono 1 </th>
      <td class="vtabla"><input name="telefono1" type="text" class="vinsert" id="telefono1" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) telefono2.select()" value="{tel1}" size="15" maxlength="20"></td>
      <th class="vtabla">Para Abono a Cuenta </th>
      <td class="vtabla"><input name="para_abono" type="radio" value="TRUE" {abono_t}>
        Si
          <input name="para_abono" type="radio" value="FALSE" {abono_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tel&eacute;fono 2 </th>
      <td class="vtabla"><input name="telefono2" type="text" class="vinsert" id="telefono2" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) fax.select()" value="{tel2}" size="15" maxlength="20"></td>
      <th class="vtabla">Pago por Transferencia Electr&oacute;nica</th>
      <td class="vtabla"><input name="trans" type="radio" value="TRUE" {trans_t}>
        Si
          <input name="trans" type="radio" value="FALSE" {trans_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Fax</th>
      <td class="vtabla"><input name="fax" type="text" class="vinsert" id="fax" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) email.select()" value="{fax}" size="15" maxlength="20"></td>
      <th class="vtabla">Tipo de Pago </th>
      <td class="vtabla"><select name="idtipopago" class="insert" id="idtipopago">
        <option value="1" {tipopago_1}>CREDITO</option>
        <option value="2" {tipopago_2}>CONTADO</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">e-m@il</th>
      <td class="vtabla"><input name="email" type="text" class="vinsert" id="email" onKeyDown="if (event.keyCode == 13) rfc.select()" value="{email}" size="30" maxlength="50"></td>
      <th class="vtabla">D&iacute;as de Cr&eacute;dito</th>
      <td class="vtabla"><input name="diascredito" type="text" class="insert" id="diascredito" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) tiempoentrega.select()" value="{diascred}" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">RFC</th>
      <td class="vtabla"><input name="rfc" type="text" class="vinsert" id="rfc" onKeyDown="if (event.keyCode == 13) contacto.select()" value="{rfc}" size="17" maxlength="25"></td>
      <th class="vtabla">Resta a Compras </th>
      <td class="vtabla"><input name="restacompras" type="radio" value="TRUE" {resta_t}>
        Si
          <input name="restacompras" type="radio" value="FALSE" {resta_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo Persona</th>
      <td class="vtabla"><input name="tipopersona" type="radio" value="TRUE" {tipo_t}>
        F&iacute;sica
          <input name="tipopersona" type="radio" value="FALSE" {tipo_f}>
          Moral</td>
      <th class="vtabla">Tipo de Proveedor </th>
      <td class="vtabla"><select name="idtipoproveedor" class="insert" id="idtipoproveedor">
        <option value="0" {tipopro_0}>PROVEEDOR</option>
        <option value="1" {tipopro_1}>OTROS</option>
        <option value="2" {tipopro_2}>EMPAQUE</option>
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Prioridad</th>
      <td class="vtabla"><input name="prioridad" type="radio" value="TRUE" {prio_t}>
        Alta
          <input name="prioridad" type="radio" value="FALSE" {prio_f}>
          Baja</td>
      <th class="vtabla">Tiempo de Entrega </th>
      <td class="vtabla"><input name="tiempoentrega" type="text" class="insert" id="tiempoentrega" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_proveedor.select()" value="{tiempo}" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto</th>
      <td class="vtabla"><input name="contacto" type="text" class="vinsert" id="contacto" onKeyDown="if (event.keyCode == 13) clave_seguridad.select()" value="{contacto}" size="30" maxlength="100"></td>
      <th class="vtabla">Verificar copias de facturas </th>
      <td class="vtabla"><input name="verfac" type="radio" value="TRUE" {ver_t}>
        Si
          <input name="verfac" type="radio" value="FALSE" {ver_f}>
          No</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Clave Seguridad </th>
      <td class="vtabla"><input name="clave_seguridad" type="text" class="insert" id="clave_seguridad" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) cuenta.select()" value="{clave_seguridad}" size="4" maxlength="4"></td>
      <th class="vtabla">&nbsp;</th>
      <td class="vtabla">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" class="vtabla" scope="row">&nbsp;</td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 1 </th>
      <td colspan="3" class="vtabla"><input name="desc1" type="text" class="rinsert" id="desc1" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) con_desc1.select()" value="{desc1}" size="5" maxlength="5">
        <input name="con_desc1" type="text" class="vinsert" id="con_desc1" onKeyDown="if (event.keyCode == 13) desc2.select()" value="{con_desc1}" size="50" maxlength="100"></td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 2 </th>
      <td colspan="3" class="vtabla"><input name="desc2" type="text" class="rinsert" id="desc2" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) con_desc2.select()" value="{desc2}" size="5" maxlength="5">
        <input name="con_desc2" type="text" class="vinsert" id="con_desc2" onKeyDown="if (event.keyCode == 13) desc3.select()" value="{con_desc2}" size="50" maxlength="100"></td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 3 </th>
      <td colspan="3" class="vtabla"><input name="desc3" type="text" class="rinsert" id="desc3" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) con_desc3.select()" value="{desc3}" size="5" maxlength="5">
        <input name="con_desc3" type="text" class="vinsert" id="con_desc3" onKeyDown="if (event.keyCode == 13) desc4.select()" value="{con_desc3}" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descuento 4 </th>
      <td colspan="3" class="vtabla"><input name="desc4" type="text" class="rinsert" id="desc4" onFocus="tmp.value=this.value;this.select()" onChange="isFloat(this,2,tmp)" onKeyDown="if (event.keyCode == 13) con_desc4.select()" value="{desc4}" size="5" maxlength="5">
        <input name="con_desc4" type="text" class="vinsert" id="con_desc4" onKeyDown="if (event.keyCode == 13) contacto1.select()" value="{con_desc4}" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo documento </th>
      <td colspan="3" class="vtabla"><input name="tipo_doc" type="radio" value="1" {tipo_doc_1}>
        Remisi&oacute;n
          <input name="tipo_doc" type="radio" value="2" {tipo_doc_2}>
          Factura</td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto 1 </th>
      <td colspan="3" class="vtabla"><input name="contacto1" type="text" class="vinsert" id="contacto1" style="width:100%;" onKeyDown="if (event.keyCode == 13) contacto2.select()" value="{contacto1}" maxlength="255"></td>
      </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto 2 </th>
      <td colspan="3" class="vtabla"><input name="contacto2" type="text" class="vinsert" id="contacto2" style="width:100%;" onKeyDown="if (event.keyCode == 13) contacto3.select()" value="{contacto2}" maxlength="255"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto 3 </th>
      <td colspan="3" class="vtabla"><input name="contacto3" type="text" class="vinsert" id="contacto3" style="width:100%;" onKeyDown="if (event.keyCode == 13) contacto4.select()" value="{contacto3}" maxlength="255"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Contacto 4 </th>
      <td colspan="3" class="vtabla"><input name="contacto4" type="text" class="vinsert" id="contacto4" style="width:100%;" onKeyDown="if (event.keyCode == 13) nombre.select()" value="{contacto4}" maxlength="255"></td>
    </tr>
  </table>  
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./fac_pro_mod_v2.php'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Modificar" onClick="validar()"> 
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (f.num_proveedor.value <= 0) {
		alert("Debe especifcar un número para el proveedor");
		f.num_proveedor.select();
		return false;
	}
	else if (f.nombre.value.length < 5) {
		alert("Debe especificar el nombre del proveedor");
		f.nombre.select();
		return false;
	}
	else if (f.trans[0].checked) {
		if (f.san[1].checked) {
			if (f.cuenta.value.length < 18) {
				alert("La CLABE Bancaria debe ser de 18 dígitos");
				f.cuenta.select();
				return false;
			}
			else if (f.plaza_banxico.value.length < 5) {
				alert("La Plaza Banxico debe ser de 5 dígitos");
				f.plaza_banxico.select();
				return false;
			}
			else if (f.sucursal.value.length < 4) {
				alert("La Sucursal debe ser de 4 dígitos");
				f.sucursal.select();
				return false;
			}
		}
		else {
			if (f.cuenta.value.length < 11 || f.cuenta.value.length > 11) {
				alert("La cuenta debe ser de 11 dígitos");
				f.cuenta.select();
				return false;
			}
		}
	}
	
	if (confirm("¿Son correctos los datos?"))
		f.submit();
	else
		return false;
}

window.onload = f.num_proveedor.select();
-->
</script>
<!-- END BLOCK : mod -->
</body>
</html>
