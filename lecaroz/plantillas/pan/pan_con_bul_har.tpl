<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de Consumos por Bulto de Harina</p>
  <form action="./pan_con_bul_har.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) codmp.select()" size="3" />
        <input name="nombre_cia" type="text" disabled="disabled" class="vnombre" id="nombre_cia" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected></option>
        <!-- START BLOCK : a -->
		<option value="{id}">{admin}</option>
		<!-- END BLOCK : a -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) fecha_corte.select()" size="3" />
        <input name="nombre_mp" type="text" disabled="disabled" class="vnombre" id="nombre_mp" size="30" /></td>
    </tr>
    <tr>
    	<th class="vtabla" scope="row">Fecha de corte</th>
    	<td class="vtabla"><input name="fecha_corte" type="text" class="insert" id="fecha_corte" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) num_cia.select()" value="{fecha_corte}" size="10" maxlength="10" /></td>
    	</tr>
    <tr>
    	<th class="vtabla" scope="row">Turnos</th>
    	<td class="vtabla"><input name="cod_turno[]" type="checkbox" id="cod_turno" value="1" checked="checked" />
    		Frances de d&iacute;a<br />
    		<input name="cod_turno[]" type="checkbox" id="cod_turno" value="2" checked="checked" />
    		Frances de noche<br />
    		<input name="cod_turno[]" type="checkbox" id="cod_turno" value="3" checked="checked" />
    		Bizcochero<br />
    		<input name="cod_turno[]" type="checkbox" id="cod_turno" value="4" checked="checked" />
    		Repostero<br />
    		<input name="cod_turno[]" type="checkbox" id="cod_turno" value="8" checked="checked" />
    		Piconero</td>
    	</tr>
  	</table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array(), mp = new Array();
<!-- START BLOCK : c -->
cia[{num}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : m -->
mp[{cod}] = '{nombre}';
<!-- END BLOCK : m -->

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

function cambiaMP() {
	if (f.codmp.value == '' || f.codmp.value == '0') {
		f.codmp.value = '';
		f.nombre_mp.value = '';
	}
	else if (mp[get_val(f.codmp)] != null)
		f.nombre_mp.value = mp[get_val(f.codmp)];
	else {
		alert('El producto no se encuentra en el catálogo');
		f.codmp.value = f.tmp.value;
		f.codmp.select();
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td align="right" class="rprint_encabezado">{hora}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Consumos por bulto de harina al {fecha}<br />
      {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : pro -->
  <tr>
    <th colspan="{span}" class="vprint" style="font-size:12pt;" scope="col">{codmp} {nombre} </th>
  </tr>
  <tr>
    <th class="print" style="font-size:10pt;" scope="col">Compa&ntilde;&iacute;a</th>
    <!-- START BLOCK : th -->
	<th colspan="2" class="print" style="font-size:10pt;" scope="col">{turno}</th>
	<!-- END BLOCK : th -->
    </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint" style="font-size:10pt;font-weight:bold;">{num_cia} {nombre} </td>
	<!-- START BLOCK : td -->
    <td class="rprint" style="font-size:10pt;color:#{color};">{consumo}</td>
    <td class="rprint" style="font-size:10pt;color:#{color};">{costo}</td>
	<!-- END BLOCK : td -->
  </tr>
  <!-- END BLOCK : fila -->
  <!-- END BLOCK : pro -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
