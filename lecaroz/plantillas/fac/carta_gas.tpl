<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style>
<link href="./styles/impresion.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : carta -->
<p align="center"><span style="font-size:16pt; font-weight:bold;">{nombre_cia}</span>
  <br />
<span style="font-size:8pt;">{dir}</span>
</p>
<p>&nbsp;</p>
<p align="right" style="font-weight:bold;">{fecha}</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="font-weight:bold;">{nombre_pro}<br />
PRESENTE</p>
<p>&nbsp;</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DE LA PRESENTE ME PERMITO SALUDARLO Y A SU VEZ SOLICITARLE LA SIGUIENTE INFORMACION:</p>
<ul>
  <li><strong>ULTRASONIDO</strong></li>
  <li><strong> DICTAMEN TECNICO</strong></li>
  <li><strong>CARTA RESPONSIVA</strong></li>
</ul>
<p> DE NUESTRO NEGOCIO, AL CUAL USTEDES ESTAN SUMINISTRANDO GAS. AGRADECIDO DE ANTEMANO Y ESPERANDO SU PRONTA RESPUESTA.</p>
<p>&nbsp;</p>
<p align="center">ATENTAMENTE</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center" style="text-decoration:overline; font-weight:bold;">JESUS MARIA ZUBIZARRETA CEBERIO </p>
<br style="page-break-after:always;">
<!-- END BLOCK : carta -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">{nombre_pro}</td>
    <td class="rprint_encabezado">{fecha}</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Responsiva de Gaseras</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table width="80%" align="center" class="print">
  <tr>
    <th class="print" scope="col">Compa&ntilde;&iacute;a</th>
  </tr>
  <!-- START BLOCK : cia -->
  <tr>
    <td class="vprint">{num_cia} {nombre} </td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
{salto}
<!-- END BLOCK : listado -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
function cerrar() {
	alert('No hay cartas por imprimir');
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
