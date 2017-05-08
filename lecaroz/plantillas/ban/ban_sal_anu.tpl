<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Saldo Promedio Anual </p>
  <form action="./ban_sal_anu.php" method="get" name="form" onKeyPress="if (event.keyCode == 13) return false">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) anio.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">A&ntilde;o</th>
      <td class="vtabla"><input name="anio" type="text" class="insert" id="anio" onFocus="temp.value = this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" value="{anio}" size="4" maxlength="4"></td>
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
    <td width="60%" class="print_encabezado" align="center">Saldos Promedio del A&ntilde;o {anio} <br>      </td>
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
      <td width="3%" class="rprint">{num_cia}</td>
      <td width="25%" class="vprint">{nombre_cia}</td>
      <td class="print">{1}</td>
      <td class="print">{2}</td>
      <td class="print">{3}</td>
      <td class="print">{4}</td>
      <td class="print">{5}</td>
      <td class="print">{6}</td>
      <td class="print">{7}</td>
      <td class="print">{8}</td>
      <td class="print">{9}</td>
      <td class="print">{10}</td>
      <td class="print">{11}</td>
      <td class="print">{12}</td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr onMouseOver="overTR(this,'#ACD2DD');" onMouseOut="outTR(this,'');">
      <th colspan="2" class="rprint">Total</th>
      <th class="print">{1}</th>
      <th class="print">{2}</th>
      <th class="print">{3}</th>
      <th class="print">{4}</th>
      <th class="print">{5}</th>
      <th class="print">{6}</th>
      <th class="print">{7}</th>
      <th class="print">{8}</th>
      <th class="print">{9}</th>
      <th class="print">{10}</th>
      <th class="print">{11}</th>
      <th class="print">{12}</th>
    </tr>
    <!--<tr>
      <th colspan="2" class="print">Total</th>
      <th class="print">{1}</th>
      <th class="print">{2}</th>
      <th class="print">{3}</th>
      <th class="print">{4}</th>
      <th class="print">{5}</th>
      <th class="print">{6}</th>
      <th class="print">{7}</th>
      <th class="print">{8}</th>
      <th class="print">{9}</th>
      <th class="print">{10}</th>
      <th class="print">{11}</th>
      <th class="print">{12}</th>
    </tr>-->
  </table>
  
<!-- END BLOCK : listado -->
</body>
</html>
