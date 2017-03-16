<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<!-- START BLOCK : scan -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Digitalizar Documentos</p>
  <p>
        <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://192.168.1.250/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
              <param name="DOWNLOAD_URL" value="http://192.168.1.250/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://192.168.1.250/lecaroz/ban_car_fol_scan.php?accion=upload&id={id}">
			  <param name="UPLOAD_PARAM_NAME" value="doc">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://192.168.1.250/lecaroz/ban_car_fol_scan.php?accion=close">
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
<!-- START BLOCK : close -->
<script language="javascript" type="application/javascript">
<!--
function cerrar() {
	self.close();
}

window.onload = cerrar();
//-->
</script>
<!-- END BLOCK : close -->
</body>
</html>
