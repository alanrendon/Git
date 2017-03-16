<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
</head>

<body>
<!-- START BLOCK : scan -->
<div id="contenedor">
  <div id="titulo">Escanear Tarjeta de Contacto </div>
  <div id="captura" align="center">
  	<p>
  	  <applet code="com.asprise.util.jtwain.web.UploadApplet.class" 
			codebase="http://asprise.com/product/jtwain/files/" name="scan"
			width="600" height="470"
			archive="JTwain.jar" id="scan">
  	    <param name="DOWNLOAD_URL" value="http://asprise.com/product/jtwain/files/AspriseJTwain.dll">
  	    <param name="DLL_NAME" value="AspriseJTwain.dll">
  	    <param name="UPLOAD_URL" value="http://192.168.1.250/lecaroz/EscanearTarjetaContacto.php?accion=upload">
  	    <param name="UPLOAD_PARAM_NAME" value="tarjeta">
  	    <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
  	    <param name="UPLOAD_OPEN_URL" value="http://192.168.1.250/lecaroz/EscanearTarjetaContacto.php?accion=cerrar">
  	    <param name="UPLOAD_OPEN_TARGET" value="_self">
  	    Su navegador no soporta java applets
      </applet>
    </p>
  	<p>
  	  <input type="button" class="boton_no_form" value="Cerrar" />
</p>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
});

//-->
</script>
<!-- END BLOCK : scan -->
<!-- START BLOCK : cerrar -->
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	window.opener.$('tarjeta').set('html', '<img src="Tarjeta.php?width=240" class="tarjeta" />');
	self.close();
});

//-->
</script>
<!-- END BLOCK : cerrar -->
</body>
</html>
