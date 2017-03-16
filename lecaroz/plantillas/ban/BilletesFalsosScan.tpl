<applet code="com.asprise.util.jtwain.web.UploadApplet.class" codebase="/lecaroz/jtwain/" archive="JTwain.jar" width="600" height="470">
	<param name="DOWNLOAD_URL" value="http://{host}/lecaroz/jtwain/AspriseJTwain.dll">
	<param name="DLL_NAME" value="AspriseJTwain.dll">
	<param name="UPLOAD_URL" value="http://{host}/lecaroz/BilletesFalsos.php?accion=cargarImagen&doc={doc}">
	<param name="UPLOAD_PARAM_NAME" value="image[]">
	<param name="UPLOAD_EXTRA_PARAMS" value="">
	<param name="UPLOAD_OPEN_URL" value="http://{host}/lecaroz/BilletesFalsos.php?accion=actualizar&doc={doc}">
	<param name="UPLOAD_OPEN_TARGET" value="hidden_data">
	Su navegador no soporta java applets
</applet>
<p>
	<input type="button" name="cancelar" id="cancelar" value="Cancelar" />
</p>
