<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<!-- START BLOCK : carta -->
<table width="80%" align="center">
<tr><td><p align="center"><font face="Arial, Helvetica, sans-serif" size="+2"><strong>{oficina}</strong></font></p>
    <p>&nbsp;</p>
<p align="right"><font face="Arial, Helvetica, sans-serif"><strong>M&Eacute;XICO, D.F. A {dia} DE {mes} DE {anio}</strong></font> </p>
<p>&nbsp;</p>
<p><strong><font face="Arial, Helvetica, sans-serif">{banco}</font></strong></p>
<p><strong><font face="Arial, Helvetica, sans-serif">A QUIEN CORRESPONDA </font></strong></p>
<p>&nbsp;</p><p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DE LA PRESENTE ME PERMITO SALUDARLE Y A SU VEZ SOLICITAR QUE SE CANCELEN LOS CHEQUES QUE A CONTINUACI&Oacute;N SE LE MENCIONAN, EN VIRTUD DE QUE FUERON EXTRAVIADOS.</font></p>
<p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SIN MAS POR EL MOMENTO, ME DESPIDO DE USTED.</font><br>
  <br>
</p>
<table width="100%">
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col"><font face="Arial, Helvetica, sans-serif">NO. DE CHEQUE</font> </th>
    <th scope="col"><font face="Arial, Helvetica, sans-serif">IMPORTE</font></th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td align="center"><font face="Arial, Helvetica, sans-serif">{cuenta}</font></td>
    <td><font face="Arial, Helvetica, sans-serif">{nombre_cia}</font></td>
    <td align="center"><font face="Arial, Helvetica, sans-serif">{folio}</font></td>
    <td align="right"><font face="Arial, Helvetica, sans-serif">{importe}</font></td>
  </tr>
  <tr>
    <th align="left"><font face="Arial, Helvetica, sans-serif">BENEFICIARIO:</font></th>
    <td colspan="3"><font face="Arial, Helvetica, sans-serif">{a_nombre}</font></td>
    </tr>
  <tr>
    <th align="left"><font face="Arial, Helvetica, sans-serif">FECHA:</font></th>
    <td colspan="3"><font face="Arial, Helvetica, sans-serif">{fecha}</font></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
<p>&nbsp; </p>
<p>&nbsp; </p>
<p align="center"><font face="Arial, Helvetica, sans-serif"><strong>ATENTAMENTE</strong></font></p>
<p>&nbsp;</p>
<p>&nbsp;</p><p align="center"><font face="Arial, Helvetica, sans-serif">____________________________________________<br>
<strong>{firma}</strong></font></p>
</td></tr>
</table>
<br style="page-break-after:always;">
<!-- END BLOCK : carta -->
<!-- START BLOCK : error -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location = "./ban_che_cer.php?codigo_error=1&num_cia={num_cia}&folio={folio}";
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : error -->
</body>
</html>
