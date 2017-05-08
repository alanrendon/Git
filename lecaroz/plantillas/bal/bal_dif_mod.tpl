<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Correcci&oacute;n de Diferencias</p>
  <form action="./bal_dif_mod.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) codmp.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="desc" type="text" disabled="disabled" class="vnombre" id="desc" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Mes</th>
      <td class="vtabla"><select name="mes" class="insert" id="mes">
        <option value="1"{1}>ENERO</option>
        <option value="2"{2}>FEBRERO</option>
        <option value="3"{3}>MARZO</option>
        <option value="4"{4}>ABRIL</option>
        <option value="5"{5}>MAYO</option>
        <option value="6"{6}>JUNIO</option>
        <option value="7"{7}>JULIO</option>
        <option value="8"{8}>AGOSTO</option>
        <option value="9"{9}>SEPTIEMBRE</option>
        <option value="10"{10}>OCTUBRE</option>
        <option value="11"{11}>NOVIEMBRE</option>
        <option value="12"{12}>DICIEMBRE</option>
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), mp = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : cod -->
mp[{codmp}] = '{desc}';
<!-- END BLOCK : cod -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value = '';
		f.nombre.value = '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function cambiaMP() {
	if (f.codmp.value == '' || f.codmp.value == '0') {
		f.codmp.value = '';
		f.desc.value = '';
	}
	else if (mp[get_val(f.codmp)] != null)
		f.desc.value = mp[get_val(f.codmp)];
	else {
		alert('El producto no se encuentra en el catálogo');
		f.codmp.value = f.tmp.value;
		f.codmp.select();
	}
}

function validar() {
	if (get_val(f.num_cia) <= 0) {
		alert('Debe especificar la compañía');
		f.num_cia.select();
		return false;
	}
	else if (get_val(f.codmp) <= 0) {
		alert('Debe especificar el producto');
		f.codmp.select();
		return false;
	}
	else if (get_val(f.anio) <= 2004) {
		alert('Debe especificar el año');
		f.anio.select();
		return false;
	}
	else
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : mod -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Correcci&oacute;n de Diferencias</p>
  <form action="./bal_dif_mod.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="num_cia" type="hidden" id="num_cia" value="{num_cia}" />
    <input name="fecha" type="hidden" id="fecha" value="{fecha}" />
    <input name="codmp" type="hidden" id="codmp" value="{codmp}" />
    <input name="precio" type="hidden" id="precio" value="{precio}" />
    <input name="dif" type="hidden" id="dif" value="{dif}" />
    <input name="id" type="hidden" id="id" value="{id}" />
    <input name="idinv" type="hidden" id="idinv" value="{idinv}" />
    <input name="tipo_mov" type="hidden" id="tipo_mov" value="{tipo_mov}" />
    <input name="mes" type="hidden" id="mes" value="{_mes}" />
    <input name="anio" type="hidden" id="anio" value="{anio}" />
    <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Compa&ntilde;&iacute;a</th>
      <th class="tabla" scope="col">Mes</th>
      <th class="tabla" scope="col">A&ntilde;o</th>
      <th class="tabla" scope="col">Producto</th>
    </tr>
    <tr>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{num_cia} {nombre} </td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{mes}</td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{anio}</td>
      <td class="tabla" style="font-size:14pt; font-weight:bold;">{codmp} {desc} </td>
    </tr>
  </table>  
  <br />
  <table class="tabla">
    <tr>
      <th class="tabla" scope="col">Existencia Final<br />
        Actual</th>
      <th class="tabla" scope="col">Nueva<br /> 
        Existencia Final </th>
    </tr>
    <tr>
      <td class="tabla"><input name="existencia" type="text" class="nombre" id="existencia" value="{existencia}" size="10" readonly="true" /></td>
      <td class="tabla"><input name="new_existencia" type="text" class="insert" id="new_existencia" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2,true)" onkeydown="if(event.keyCode==13){this.blur();this.select()}" value="{existencia}" size="10" /></td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Cancelar" onclick="document.location='./bal_dif_mod.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	if (confirm('¿Es correcta la nueva existencia final del producto?'))
		f.submit();
	else
		f.new_existencia.select();
}

window.onload = f.new_existencia.select();
//-->
</script>
<!-- END BLOCK : mod -->
</body>
</html>
