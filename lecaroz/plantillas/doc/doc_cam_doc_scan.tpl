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
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Digitalizaci&oacute;n de Documentos de Camionetas </p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Paso 1. Captura de datos del documento. </font></p>
  <form action="./doc_cam_doc_scan.php" method="post" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Camioneta</th>
      <th class="vtabla"><input name="id" type="hidden" id="id" value="{id}">
        {id} {modelo}       </th>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de Documento</th>
      <td class="vtabla"><select name="tipo_doc" class="insert" id="select">
        <!-- START BLOCK : tipo -->
		<option value="{tipo_doc}">{descripcion}</option>
		<!-- END BLOCK : tipo -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><textarea name="descripcion" cols="30" rows="5" wrap="VIRTUAL" class="insert" id="descripcion"></textarea></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar(this.form)"> 
    </p>
  </form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (confirm("¿Son correctos los datos?"))
			form.submit();
		else
			form.num_cia.select();
	}
	
	window.onload = document.form.num_cia.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : scan -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Digitalizaci&oacute;n de Documentos de Camionetas </p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Paso 2. Digitalizar documentos.</font> </p>
  <p>
    <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://{server_addr}/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
			  <param name="DOWNLOAD_URL" value="http://{server_addr}/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://{server_addr}/lecaroz/doc_cam_doc_scan.php?accion=upload">
			  <param name="UPLOAD_PARAM_NAME" value="image[]">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://{server_addr}/lecaroz/doc_cam_doc_scan.php">
			  <param name="UPLOAD_OPEN_TARGET" value="_self">
			  Su navegador no soporta java applets
    </applet>
</p>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='./doc_cam_doc_scan.php?accion=cancelar'">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Terminar" onClick="document.location='./doc_cam_doc_scan.php?accion=terminar'">
</p></td>
</tr>
</table>
<!-- END BLOCK : scan -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
	function cerrar() {
		window.opener.document.location.reload();
		self.close();
	}
	
	window.onload = cerrar();
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
