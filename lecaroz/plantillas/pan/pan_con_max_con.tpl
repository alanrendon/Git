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
<td align="center" valign="middle"><p class="title">Consulta de Consumos M&aacute;ximos</p>
  <form action="./pan_con_max_con.php" method="get" name="form">
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
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="nombre_mp" type="text" disabled="disabled" class="vnombre" id="nombre_mp" size="30" /></td>
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
    <td width="60%" class="print_encabezado" align="center">Consumos M&aacute;ximos <br />
      {admin}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : pro -->
  <tr>
    <th colspan="6" class="vprint" scope="col">{codmp} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">FD</th>
    <th class="print" scope="col">FN</th>
    <th class="print" scope="col">BZ</th>
    <th class="print" scope="col">RP</th>
    <th class="print" scope="col">PC</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="print">{1}</td>
    <td class="print">{2}</td>
    <td class="print">{3}</td>
    <td class="print">{4}</td>
    <td class="print">{8}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <td colspan="6" class="print">&nbsp;</td>
  </tr>
  <!-- END BLOCK : pro -->
</table>
<!-- END BLOCK : listado -->
</body>
</html>
