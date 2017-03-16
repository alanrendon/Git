<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">

<style>
.natural {
  border-top-style: solid 4px #000;
  border-bottom-style: solid 4px #000;

}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Porcentajes de Gas contra Producci&oacute;n </p>
  <form action="./bal_gas_pro.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Administrador</th>
      <td class="vtabla"><select name="idadmin" class="insert" id="idadmin">
        <option value="" selected></option>
        <!-- START BLOCK : admin -->
		<option value="{id}">{nombre}</option>
		<!-- END BLOCK : admin -->
      </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Opciones</th>
      <td class="vtabla"><input name="litros" type="checkbox" id="litros" value="TRUE">
        Litros</td>
    </tr>
  </table>
  <p>
    <input type="button" class="boton" value="Siguente" onClick="valida_registro(form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.anio.value < 2005) {
			alert("Debe especificar el año");
			form.anio.select();
			return false;
		}
		else
			form.submit();
	}

	window.onload = document.form.num_cia.select();
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
    <td width="60%" class="print_encabezado" align="center">Porcentajes de Gas contra Producci&oacute;n del {anio} <br>      </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
  <br>
  <table width="99%" align="center" class="print">
    <tr>
      <th colspan="2" class="print" scope="col">Compa&ntilde;&iacute;a</th>
      <th width="6%" class="print" scope="col">Enero</th>
      <th width="6%" class="print" scope="col">Febrero</th>
      <th width="6%" class="print" scope="col">Marzo</th>
      <th width="6%" class="print" scope="col">Abril</th>
      <th width="6%" class="print" scope="col">Mayo</th>
      <th width="6%" class="print" scope="col">Junio</th>
      <th width="6%" class="print" scope="col">Julio</th>
      <th width="6%" class="print" scope="col">Agosto</th>
      <th width="6%" class="print" scope="col">Septiembre</th>
      <th width="6%" class="print" scope="col">Octubre</th>
      <th width="6%" class="print" scope="col">Noviembre</th>
      <th width="6%" class="print" scope="col">Diciembre</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <td width="3%" class="rprint"{natural}>{num_cia}</td>
      <td width="25%" class="vprint" style="color:{color};{natural2}">{nombre_cia}</td>
      <td class="print{natural}"{natural}>{1}</td>
      <td class="print{natural}"{natural}>{2}</td>
      <td class="print{natural}"{natural}>{3}</td>
      <td class="print{natural}"{natural}>{4}</td>
      <td class="print{natural}"{natural}>{5}</td>
      <td class="print{natural}"{natural}>{6}</td>
      <td class="print{natural}"{natural}>{7}</td>
      <td class="print{natural}"{natural}>{8}</td>
      <td class="print{natural}"{natural}>{9}</td>
      <td class="print{natural}"{natural}>{10}</td>
      <td class="print{natural}"{natural}>{11}</td>
      <td class="print{natural}"{natural}>{12}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <th colspan="2" class="print" align="right">Promedio Gas L.P.</th>
      <th class="print">{pg1}</th>
      <th class="print">{pg2}</th>
      <th class="print">{pg3}</th>
      <th class="print">{pg4}</th>
      <th class="print">{pg5}</th>
      <th class="print">{pg6}</th>
      <th class="print">{pg7}</th>
      <th class="print">{pg8}</th>
      <th class="print">{pg9}</th>
      <th class="print">{pg10}</th>
      <th class="print">{pg11}</th>
      <th class="print">{pg12}</th>
    </tr>
    <tr>
      <th colspan="2" class="print" align="right">Promedio Gas Natural</th>
      <th class="print">{pn1}</th>
      <th class="print">{pn2}</th>
      <th class="print">{pn3}</th>
      <th class="print">{pn4}</th>
      <th class="print">{pn5}</th>
      <th class="print">{pn6}</th>
      <th class="print">{pn7}</th>
      <th class="print">{pn8}</th>
      <th class="print">{pn9}</th>
      <th class="print">{pn10}</th>
      <th class="print">{pn11}</th>
      <th class="print">{pn12}</th>
    </tr>
    <tr>
      <th colspan="2" class="print" align="right">Promedio General</th>
      <th class="print">{p1}</th>
      <th class="print">{p2}</th>
      <th class="print">{p3}</th>
      <th class="print">{p4}</th>
      <th class="print">{p5}</th>
      <th class="print">{p6}</th>
      <th class="print">{p7}</th>
      <th class="print">{p8}</th>
      <th class="print">{p9}</th>
      <th class="print">{p10}</th>
      <th class="print">{p11}</th>
      <th class="print">{p12}</th>
    </tr>
  </table>

<!-- END BLOCK : listado -->
</body>
</html>
