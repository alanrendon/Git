<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/impresion.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Maquinaria</p>
  <form action="./fac_maq_lis.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp">
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_maquina.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">M&aacute;quina</th>
      <td class="vtabla"><input name="num_maquina" type="text" class="insert" id="num_maquina" onFocus="tmp.value=this.value;this.select()" onChange="isInt(this,tmp)" onKeyDown="if (event.keyCode == 13) num_cia.select()" size="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        General<br>
        <input name="tipo" type="radio" value="2">
        Compa&ntilde;&iacute;a</td>
    </tr>
  </table>  
    <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar()"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : listado1 -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Maquinaria</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">No.</th>
    <th class="print" scope="col">Marca</th>
    <th class="print" scope="col">Descripci&oacute;n</th>
    <th class="print" scope="col">Capacidad</th>
    <th class="print" scope="col">No. Serie </th>
    <th class="print" scope="col">Fecha Compra</th>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
    <th class="print" scope="col">Turno</th>
  </tr>
  <!-- START BLOCK : fila1 -->
  <tr>
    <td class="rprint">{num_maquina}</td>
    <td class="vprint">{marca}</td>
    <td class="vprint">{descripcion}</td>
    <td class="rprint">{capacidad}</td>
    <td class="vprint">{num_serie}</td>
    <td class="print">{fecha}</td>
    <td class="vprint">{num_cia} {nombre} </td>
    <td class="vprint">{turno}</td>
  </tr>
  <!-- END BLOCK : fila1 -->
</table>
<!-- END BLOCK : listado1 -->
<!-- START BLOCK : listado2 -->
<table width="100%">
  <tr>
    <td class="print_encabezado">{num_cia}</td>
    <td class="print_encabezado" align="center">{nombre}</td>
    <td class="rprint_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Maquinaria </td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="80%" align="center" class="print">
  <tr>
    <th class="print" scope="col">No</th>
    <th class="print" scope="col">Marca</th>
    <th class="print" scope="col">Descripci&oacute;n</th>
    <th class="print" scope="col">Capacidad</th>
    <th class="print" scope="col">No. Serie</th>
    <th class="print" scope="col">Fecha Compra </th>
    <th class="print" scope="col">Turno</th>
  </tr>
  <!-- START BLOCK : fila2 -->
  <tr>
    <td class="rprint">{num_maquina}</td>
    <td class="vprint">{marca}</td>
    <td class="vprint">{descripcion}</td>
    <td class="rprint">{capacidad}</td>
    <td class="vprint">{num_serie}</td>
    <td class="print">{fecha}</td>
    <td class="vprint">{turno}</td>
  </tr>
  <!-- END BLOCK : fila2 -->
</table>
{salto}
<!-- END BLOCK : listado2 -->
</body>
</html>
