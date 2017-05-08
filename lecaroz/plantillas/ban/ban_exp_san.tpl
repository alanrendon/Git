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
<!-- START BLOCK : archivo -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Explorador de Archivos de Santander</p>
  <form action="./ban_exp_san.php" method="post" enctype="multipart/form-data" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Archivo</th>
      <td class="vtabla"><input name="file" type="file" class="insert" size="30"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function validar(form) {
	if (form.file.value.length < 8) {
		alert("Debe especificar el nombre del archivo");
		return false;
	}
	else
		form.submit();
}
-->
</script>
<!-- END BLOCK : archivo -->
<!-- START BLOCK : listado -->
<table width="100%">
  <tr>
    <td>&nbsp;</td>
    <td class="print_encabezado" align="center">Oficinas Administrativas Mollendo S. de R.L. y C.V. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="20%">&nbsp;</td>
    <td width="60%" class="print_encabezado" align="center">Archivo '{nombre_archivo}' <br>
    {hash}</td>
    <td width="20%">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" class="print">
  <!-- START BLOCK : cia -->
  <tr>
    <th class="print" scope="col" style="font-size: 12pt;">{num_cia}</th>
    <th colspan="4" class="print" scope="col" style="font-size: 12pt;">{cuenta}</th>
    <th colspan="4" class="print" scope="col" style="font-size: 12pt;">{nombre_cia}</th>
  </tr>
  <tr>
    <th class="print">Fecha</th>
    <th class="print">Abono</th>
    <th class="print">Cargo</th>
    <th class="print">Saldo</th>
    <th class="print">Folio</th>
    <th class="print">C&oacute;digo</th>
    <th class="print">Descripci&oacute;n</th>
    <th class="print">Concepto</th>
    <th class="print">Hash</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td class="print">{fecha}</td>
    <td class="rprint" style="color: #0000CC;">{abono}</td>
    <td class="rprint" style="color: #CC0000;">{cargo}</td>
    <td class="rprint" style="color: #FF6600; font-weight: bold;">{saldo}</td>
    <td class="print">{folio}</td>
    <td class="print">{cod_banco}</td>
    <td class="vprint">{descripcion}</td>
    <td class="vprint">{concepto}</td>
    <td class="vprint">{hash}</td>
  </tr>
  <!-- END BLOCK : row -->
  <tr>
    <th class="print">Total</th>
    <th class="print" style="color: #0000CC; font-weight: bold;">{abonos}</th>
    <th class="print" style="color: #CC0000; font-weight: bold;">{cargos}</th>
    <th colspan="6" class="print">&nbsp;</th>
  </tr>
  <tr>
    <td colspan="9">&nbsp;</td>
  </tr>
  <!-- END BLOCK : cia -->
</table>
<!-- END BLOCK : listado -->
<p align="center">
  <input type="button" class="boton" onClick="document.location='./ban_exp_san.php'" value="Regresar">
</p>
</body>
</html>
