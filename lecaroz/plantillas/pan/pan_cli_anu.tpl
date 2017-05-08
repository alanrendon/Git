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
<td align="center" valign="middle"><p class="title">Comparativo de Clientes Anual</p>
  <form action="./pan_cli_anu.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as</th>
      <td class="vtabla"><input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[1].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[2].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[3].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[4].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[5].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[6].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[7].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[8].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia[9].select()" size="3" />
	  <input name="num_cia[]" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" /></td>
    </tr>

    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onfocus="tmp.value=this.value;this.select()" onchange="isInt(this,tmp)" onkeydown="if (event.keyCode == 13) num_cia[0].select()" value="{anio}" size="4" maxlength="4" /></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Exportar a Excel </th>
      <td class="vtabla"><input name="excel" type="checkbox" id="excel" value="1" />
        Si</td>
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

function validar() {
	if (get_val(f.anio) <= 2000) {
		alert('Debe especificar el año');
		f.anio.select();
	}
	else
		f.submit();
}

window.onload = f.num_cia[0].select();
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
    <td width="60%" class="print_encabezado" align="center">Comparativo de Clientes {anio} </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Ene</th>
    <th class="print" scope="col">Feb</th>
    <th class="print" scope="col">Mar</th>
    <th class="print" scope="col">Abr</th>
    <th class="print" scope="col">May</th>
    <th class="print" scope="col">Jun</th>
    <th class="print" scope="col">Jul</th>
    <th class="print" scope="col">Ago</th>
    <th class="print" scope="col">Sep</th>
    <th class="print" scope="col">Oct</th>
    <th class="print" scope="col">Nov</th>
    <th class="print" scope="col">Dic</th>
    <th class="print" scope="col">Total</th>
    <th class="print" scope="col">Prom</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <td class="vprint" style="font-weight:bold;">{num_cia} {nombre} </td>
    <td class="rprint" style="color:#C00;">{1}</td>
    <td class="rprint" style="color:#C00;">{2}</td>
    <td class="rprint" style="color:#C00;">{3}</td>
    <td class="rprint" style="color:#C00;">{4}</td>
    <td class="rprint" style="color:#C00;">{5}</td>
    <td class="rprint" style="color:#C00;">{6}</td>
    <td class="rprint" style="color:#C00;">{7}</td>
    <td class="rprint" style="color:#C00;">{8}</td>
    <td class="rprint" style="color:#C00;">{9}</td>
    <td class="rprint" style="color:#C00;">{10}</td>
    <td class="rprint" style="color:#C00;">{11}</td>
    <td class="rprint" style="color:#C00;">{12}</td>
    <td class="rprint" style="font-weight:bold;color:#00C;">{total_cia}</td>
    <td class="rprint" style="font-weight:bold;color:#00C;">{prom_cia}</td>
  </tr>
  <!-- END BLOCK : cia -->
  <tr>
    <th class="print">Total Mensual </th>
    <th class="rprint_total">{1}</th>
    <th class="rprint_total">{2}</th>
    <th class="rprint_total">{3}</th>
    <th class="rprint_total">{4}</th>
    <th class="rprint_total">{5}</th>
    <th class="rprint_total">{6}</th>
    <th class="rprint_total">{7}</th>
    <th class="rprint_total">{8}</th>
    <th class="rprint_total">{9}</th>
    <th class="rprint_total">{10}</th>
    <th class="rprint_total">{11}</th>
    <th class="rprint_total">{12}</th>
    <th class="rprint_total">{total_cias}</th>
    <th class="rprint_total">{total_prom}</th>
  </tr>
</table>
<style type="text/css" media="print">
#boton {
	display: none;
}
</style>
<div id="boton">
<p align="center">
<input type="button" class="boton" value="Regresar" onClick="document.location='./pan_cli_anu.php'">
</p>
</div>
<!-- END BLOCK : listado -->
</body>
</html>
