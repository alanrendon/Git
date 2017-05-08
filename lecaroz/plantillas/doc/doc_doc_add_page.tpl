<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="/lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Agregar hoja a documento</p>
  <p><font face="Geneva, Arial, Helvetica, sans-serif">Paso 2. Digitalizar documentos.</font> </p>
  <p>
        <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://{server_addr}/lecaroz/jtwain/"
			archive="JTwain.jar"
			width="600" height="470">
              <param name="DOWNLOAD_URL" value="http://{server_addr}/lecaroz/jtwain/AspriseJTwain.dll">
			  <param name="DLL_NAME" value="AspriseJTwain.dll">
			  <param name="UPLOAD_URL" value="http://{server_addr}/lecaroz/doc_doc_add_page.php?accion=upload&id_doc={id_doc}">
			  <param name="UPLOAD_PARAM_NAME" value="image[]">
			  <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			  <param name="UPLOAD_OPEN_URL" value="http://{server_addr}/lecaroz/doc_doc_preview.php?id={id_doc}">
			  <param name="UPLOAD_OPEN_TARGET" value="_self">
			  Su navegador no soporta java applets
    </applet>
</p>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="document.location='doc_doc_preview.php?id={id_doc}'">
</p></td>
</tr>
</table>
</body>
</html>
