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
<td align="center" valign="middle"><p class="title">Digitalizar Factura </p>
  <p>
    <applet code="com.asprise.util.jtwain.web.UploadApplet.class"
			codebase="http://{server_addr}/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
			  <param name="DOWNLOAD_URL" value="http://{server_addr}/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://{server_addr}/lecaroz/fac_fac_ord_ser_scan.php?accion=upload&num_pro={num_pro}&num_fact={num_fact}&folio={folio}">
			  <param name="UPLOAD_PARAM_NAME" value="factura">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://{server_addr}/lecaroz/fac_fac_ord_ser_scan.php?accion=cerrar&i={i}">
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
<script language="javascript" type="application/javascript">
	function cerrar(i) {
		/*var parentForm = window.opener.document.form;
		var boton = f.scan.length == undefined ? f.scan : f.scan[i];
		var num_fact = f.num_fact.length == undefined ? f.num_fact : f.num_fact[i];
		var num_pro = f.num_pro.length == undefined ? f.num_pro : f.num_pro[i];
		var ok = f.ok.length == undefined ? f.ok : f.ok[i];
		boton.disabled = true;
		num_fact.readOnly = true;
		num_pro.readOnly = true;
		ok.value = 1;*/
		self.close();
	}

	window.onload = cerrar({i});
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
