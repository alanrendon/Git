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
<td align="center" valign="middle"><p class="title">Listado de Pedidos de Panaderias</p>
  <form action="./ped_ped_sta.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) fecha1.select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="admin" class="insert" id="admin">
        <option value="" selected="selected"></option>
		<!-- START BLOCK : idadmin -->
        <option value="{id}">{admin}</option>
		<!-- END BLOCK : idadmin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Periodo</th>
      <td class="vtabla"><input name="fecha1" type="text" class="insert" id="fecha1" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) fecha2.select()" value="{fecha1}" size="10" maxlength="10" />
        a
          <input name="fecha2" type="text" class="insert" id="fecha2" onfocus="tmp.value=this.value;this.select()" onchange="inputDateFormat(this)" onkeydown="if (event.keyCode == 13) codmp.select()" value="{fecha2}" size="10" maxlength="10" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) num_cia.select()" size="3" />
        <input name="nombre_mp" type="text" disabled="disabled" class="vnombre" id="nombre_mp" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Estatus</th>
      <td class="vtabla"><input name="status" type="radio" value="0" checked="checked" />
        Pendientes<br />
        <input name="status" type="radio" value="1" />
        Validados<br />
        <input name="status" type="radio" value="2" />
        Cancelados</td>
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
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->
<!-- START BLOCK : mp -->
mp[{codmp}] = '{nombre}';
<!-- END BLOCK : mp -->

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
<!-- START BLOCK : result -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Pedidos de Panaderias {status} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th colspan="10" class="vprint" scope="row">{num_cia} {nombre} </th>
  </tr>
  <tr>
    <th class="print" scope="row">Fecha</th>
    <th class="print">Producto</th>
    <th class="print">Unidad</th>
    <th class="print">C&oacute;digo</th>
    <th class="print">Cantidad</th>
    <th class="print">Proveedor</th>
    <th class="print">Observaciones</th>
    <th class="print">Pedido</th>
    <th class="print">Autorizado</th>
    <th class="print">Autoriza</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="print" scope="row">{fecha}</td>
    <td class="vprint">{producto}</td>
    <td class="vprint">{unidad}</td>
    <td class="vprint">{codmp} {desc} </td>
    <td class="rprint">{cantidad}</td>
    <td class="vprint">{num_pro} {nombre_pro} </td>
    <td class="vprint">{obs}</td>
    <td class="print">{pedido}</td>
    <td class="print">{aut}</td>
    <td class="print">{user}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <tr>
    <td colspan="10" class="print" scope="row">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<!-- END BLOCK : result -->
</body>
</html>
