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
<tr><td><p align="center"><font face="Arial, Helvetica, sans-serif" size="+2"><strong>{nombre_cia}</strong></font>
      <br>
<span style="font-size:8pt;font-weight:bold;">{rfc}</span><br>
<span style="font-size:8pt;">{dir}</span></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="right"><font face="Arial, Helvetica, sans-serif"><strong>{dia} DE {mes} DE {anio}</strong></font> </p>
<p>&nbsp;</p>
<p><strong><font face="Arial, Helvetica, sans-serif">{banco}</font></strong></p>
<p>&nbsp;</p><p>&nbsp;</p>
<p>&nbsp;</p>
<p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POR MEDIO DE ESTE CONDUCTO ME PERMITO SALUDARLE Y A SU VEZ SOLICITARLE LA CERTIFICACI&Oacute;N DEL CHEQUE <strong>NO. {folio}</strong> POR LA CANTIDAD DE <strong>${importe} ({importe_escrito})</strong>, DE LA CUENTA <strong>{cuenta}</strong> A FAVOR DE <strong>{a_nombre}</strong>.</font></p>
<p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SE AUTORIZA AL SR.<strong> {nombre}</strong> PARA QUE REALICE LA CERTIFICACI&Oacute;N DE ESTE CHEQUE EN EL <strong>{banco}</strong>.</font></p>
<p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SIN M&Aacute;S POR EL MOMENTO SE DESPIDE DE UD.</font></p>
<p align="justify"><font face="Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SE AUTORIZA EL CARGO POR CERTIFICACI&Oacute;N DEL CHEQUE.</font></p>
<p>&nbsp;</p>
<p align="center"><font face="Arial, Helvetica, sans-serif"><strong>ATENTAMENTE</strong></font></p>
<p>&nbsp;</p>
<p>&nbsp;</p><p align="center"><font face="Arial, Helvetica, sans-serif">____________________________________________<br>
<strong>{nombre_cia}</strong></font></p>
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
