<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
<link href="../../styles/impresion.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.highlight {
	font-weight: bold;
	color: #C00;
}
</style>
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listado de Expendios
 </p>
  <form action="pan_exp_lis_v2.php" method="get" name="form" id="form" onClick="validar()">
    <input type="hidden" name="tmp" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compañía</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp))cambiaCia()" onkeydown="if (event.keyCode==13)next.focus()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
  </table>  
  <p>
    <input name="next" type="button" class="boton" id="next" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="application/javascript">
<!--
var f = document.form;

function cambiaCia() {
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
    <td class="print_encabezado">{num_cia}</td>
    <td class="print_encabezado" align="center">{nombre}<br />
      ({nombre_corto})</td>
    <td align="right" class="print_encabezado">{num_cia}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Listado de Expendios</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <tr>
    <th class="print" scope="col">Exp</th>
    <th class="print" scope="col">Ref</th>
    <th class="print" scope="col">Nombre</th>
    <th class="print" scope="col">Tipo</th>
    <th class="print" scope="col">%</th>
    <th class="print" scope="col">Aut. Dev.</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="vprint{class}">{exp}</td>
    <td class="vprint{class}">{ref}</td>
    <td class="vprint{class}">{nombre}</td>
    <td class="print{class}">{tipo}</td>
    <td class="print{class}">{por}</td>
    <td class="print{class}">{dev}</td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
{salto}
<!-- END BLOCK : listado -->
</body>
</html>
