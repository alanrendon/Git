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
<td align="center" valign="middle"><p class="title">Cheques Reservados</p>
  <form action="./ban_che_res_v2.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) anio.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Banco</th>
      <td class="vtabla"><select name="cuenta" class="insert" id="cuenta">
        <option value="1">BANORTE</option>
        <option value="2" selected="selected">SANTANDER</option>
      </select>
      </td>
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
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

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

function validar() {
	if (get_val(f.anio) <= 0) {
		alert('Debe especificar el año de reserva');
		f.anio.select();
		return false;
	}
	else if (confirm('¿Son correctos los datos?'))
		f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : result -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Cheques Reservados</p>
  <form action="./ban_che_res_v2.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <input name="cuenta" type="hidden" id="cuenta" value="{cuenta}" />
    <table class="tabla">
      <tr>
        <th class="tabla" scope="col">Banco</th>
      </tr>
      <tr>
        <th class="tabla" style="font-size:12pt;">{banco}</th>
      </tr>
    </table>
    <br />
    <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="6" class="vtabla" scope="col" style="font-size:12pt;">{num_cia} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla">Folio</th>
      <th class="tabla">Fecha</th>
      <th class="tabla">Proveedor</th>
      <th class="tabla">Gasto</th>
      <th class="tabla">Concepto</th>
      <th class="tabla">Importe</th>
    </tr>
    <!-- START BLOCK : folio -->
	<tr>
      <td class="tabla"><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
      <input name="folio[]" type="hidden" id="folio" value="{folio}" />
        {folio}</td>
      <td class="tabla"><input name="fecha[]" type="hidden" id="fecha" value="{fecha}" />
        {fecha}</td>
      <td class="tabla"><input name="num_pro[]" type="text" class="insert" id="num_pro" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaPro({i})" onkeydown="movCursor(event.keyCode,codgastos{index},null,codgastos{index},num_pro{back},num_pro{next})" value="{num_pro}" size="3" />
        <input name="nombre_pro[]" type="text" class="vnombre" id="nombre_pro" value="{nombre_pro}" size="30" readonly="true" /></td>
      <td class="tabla"><input name="codgastos[]" type="text" class="insert" id="codgastos" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCod({i})" onkeydown="movCursor(event.keyCode,concepto{index},num_pro{index},concepto{index},codgastos{back},codgastos{next})" value="{codgastos}" size="3" />
        <input name="desc[]" type="text" disabled="disabled" class="vnombre" id="desc" value="{desc}" size="30" /></td>
      <td class="tabla"><input name="concepto[]" type="text" class="vinsert" id="concepto" onkeydown="movCursor(event.keyCode,importe{index},codgastos{index},importe{index},concepto{back},concepto{next})" value="{concepto}" size="30" /></td>
      <td class="tabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.select();this.select()" onchange="inputFormat(this,2,tmp)" onkeydown="movCursor(event.keyCode,num_pro{next},concepto{index},null,importe{back},importe{next})" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : folio -->
    <tr>
      <td colspan="6" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" onclick="document.location='./ban_che_res_v2.php'" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Siguiente" onclick="validar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, pro = new Array(), cod = new Array();
<!-- START BLOCK : p -->
pro[{num_pro}] = '{nombre}';
<!-- END BLOCK : p -->
<!-- START BLOCK : cod -->
cod[{cod}] = '{desc}';
<!-- END BLOCK : cod -->

function cambiaPro(i) {
	var num_pro = f.num_pro.length == undefined ? f.num_pro : f.num_pro[i];
	var nombre_pro = f.nombre_pro.length == undefined ? f.nombre_pro : f.nombre_pro[i];
	
	if (num_pro.value == '' || num_pro.value == '0') {
		num_pro.value = '';
		nombre_pro.value = '';
	}
	else if (pro[get_val(num_pro)] != null)
		nombre_pro.value = pro[get_val(num_pro)];
	else {
		alert('El proveedor no se encuentra en el catálogo');
		num_pro.value = f.tmp.value;
		num_pro.select();
	}
}

function cambiaCod(i) {
	var codgastos = f.codgastos.length == undefined ? f.codgastos : f.codgastos[i];
	var desc = f.desc.length == undefined ? f.desc : f.desc[i];
	
	if (codgastos.value == '' || codgastos.value == '0') {
		codgastos.value = '';
		desc.value = '';
	}
	else if (cod[get_val(codgastos)] != null)
		desc.value = cod[get_val(codgastos)];
	else {
		alert('El código no se encuentra en el catálogo');
		codgastos.value = f.tmp.value;
		codgastos.select();
	}
}

function validar() {
	if (confirm('¿Son correctos todos los datos?'))
		f.submit();
}

function movCursor(keyCode, enter, lt, rt, up, dn) {
	if (keyCode == 13 && enter && enter != null) enter.select();
	else if (keyCode == 37 && lt && lt != null) lt.select();
	else if (keyCode == 39 && rt && rt != null) rt.select();
	else if (keyCode == 38 && up && up != null) up.select();
	else if (keyCode == 40 && dn && up != null) dn.select();
}

window.onload = f.num_pro.length == undefined ? f.num_pro.select() : f.num_pro[0].select();
//-->
</script>
<!-- END BLOCK : result -->
</body>
</html>
