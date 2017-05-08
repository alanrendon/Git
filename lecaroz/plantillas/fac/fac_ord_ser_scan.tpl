<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : scan -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Digitalizar Orden de Servicio </p>
  <p>
    <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://192.168.1.250/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
			  <param name="DOWNLOAD_URL" value="http://192.168.1.250/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://192.168.1.250/lecaroz/fac_ord_ser_scan.php?accion=upload&folio={folio}">
			  <param name="UPLOAD_PARAM_NAME" value="orden">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://192.168.1.250/lecaroz/fac_ord_ser_scan.php?accion=cerrar">
			  <param name="UPLOAD_OPEN_TARGET" value="_self">
			  Su navegador no soporta java applets
    </applet>
</p>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
  </p></td>
</tr>
</table>
<!-- END BLOCK : scan -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		var f = window.opener.document.form;
		var boton = f.scan_orden;
		boton.disabled = true;
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
