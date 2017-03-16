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
<!-- START BLOCK : adquirir -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Escaneo de Documentos </p>
  <p class="title">Paso 1. Adquirir Im&aacute;gen</p>  <table>
    <tr>
      <td scope="col">
	  <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://asprise.com/product/jtwain/files/"
			archive="JTwain.jar"
			width="600" height="470">
			<param name="DOWNLOAD_URL" value="http://asprise.com/product/jtwain/files/AspriseJTwain.dll">
			<param name="DLL_NAME" value="AspriseJTwain.dll">
			<param name="UPLOAD_URL" value="./imageUpload.php">
			<param name="UPLOAD_PARAM_NAME" value="file[]">
			<param name="UPLOAD_EXTRA_PARAMS" value="A=B">
			<param name="UPLOAD_OPEN_URL" value="./imageUpload.php">
			<param name="UPLOAD_OPEN_TARGET" value="mainFrame">
			Su navegador no soporta java applets
		</applet>
	  </td>
      </tr>
  </table></td>
</tr>
</table>
<!-- END BLOCK : adquirir -->
<!-- START BLOCK : guardar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Escaneo de Documentos</p>
  <form action="imageUpload.php" method="post" name="form">
  <input name="filename" type="hidden" value="{fileName}">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="textfield" type="text" class="insert" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Descripci&oacute;n</th>
      <td class="vtabla"><input name="textfield2" type="text" class="insert" size="50" maxlength="100"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo de documento </th>
      <td class="vtabla"><select name="select" class="insert">
        <option value="1">DESCONOCIDO</option>
      </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Cancelar" onClick="doccument.location='./imageUpload.php'">
&nbsp;&nbsp;    
<input name="Submit" type="submit" class="boton" value="Guardar">
  </p></form></td>
</tr>
</table>
<!-- END BLOCK : guardar -->
</body>
</html>
