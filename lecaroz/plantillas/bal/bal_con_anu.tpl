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
<td align="center" valign="middle"><p class="title">Comparativo de Promedio de Consumo Anual de Productos</p>
  <form action="./bal_con_anu.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Producto</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaMP()" onkeydown="if (event.keyCode == 13) num_cia[0].select()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="3" maxlength="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="3" maxlength="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="3" maxlength="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="3" maxlength="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Al a&ntilde;o </th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) codmp.select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function cambiaMP() {
}

function validar() {
	if (get_val(f.anio) <= 2000) {
		alert('Debe especificar el año');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.codmp.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Promedio de Consumo  Anual de {mp} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="80%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th width="10%" class="print" scope="col">{anio0}</th>
    <th width="10%" class="print" scope="col">{anio1}</th>
    <th width="10%" class="print" scope="col">{anio2}</th>
    <th width="10%" class="print" scope="col">{anio3}</th>
    <th width="10%" class="print" scope="col">{anio4}</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="rprint">{prom0}</td>
    <td class="rprint">{prom1}</td>
    <td class="rprint">{prom2}</td>
    <td class="rprint">{prom3}</td>
    <td class="rprint">{prom4}</td>
  </tr>
  <!-- END BLOCK : fila -->
  <!-- START BLOCK : totales -->
  <tr>
    <th class="rprint">Totales</th>
    <th class="rprint_total">{prom0}</th>
    <th class="rprint_total">{prom1}</th>
    <th class="rprint_total">{prom2}</th>
    <th class="rprint_total">{prom3}</th>
    <th class="rprint_total">{prom4}</th>
  </tr>
  <!-- END BLOCK : totales -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
