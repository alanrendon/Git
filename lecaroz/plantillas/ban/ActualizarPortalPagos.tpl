<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Archivo de Pagos para Portal Lecaroz </title>
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Actualizar Datos del Portal de Pagos</div>
  <div id="captura" align="center">
    <p>
      <input name="actualizar" type="button" class="boton" id="actualizar" value="Actualizar Portal" />
    </p>
    <table id="opciones">
      <tr>
        <th>Opciones de actualizaci&oacute;n </th>
      </tr>
      <tr>
        <td><input name="catalogo_proveedores" type="checkbox" id="catalogo_proveedores" value="1" />
        Cat&aacute;logo de proveedores </td>
      </tr>
      <tr>
        <td><input name="catalogo_panaderias" type="checkbox" id="catalogo_panaderias" value="1" />
        Cat&aacute;logo de panaderias </td>
      </tr>
      <tr>
        <td><input name="pagos" type="checkbox" id="pagos" value="1" checked="checked" />
        Pagos</td>
      </tr>
      <tr>
        <td><input name="pendientes" type="checkbox" id="pendientes" value="1" checked="checked" />
        Pendientes</td>
      </tr>
      <tr>
        <td><input name="aclaraciones" type="checkbox" id="aclaraciones" value="1" />
          Aclaraciones</td>
      </tr>
    </table>
    <div id="status">
	   <div id="img" style="display:none;">
	     <img src="imagenes/_loading.gif" />
	   </div>
	   <div id="leyenda">
	   </div>
    </div>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	$('actualizar').addEvent('click', function() {
		if (!confirm('¿Actualizar portal de pagos?'))
			return false;
		
		new Request({
			url: 'ActualizarPortalPagos.php',
			method: 'post',
			data: 'accion=actualizar&' + $('opciones').toQueryString(),
			onRequest: function() {
				$('leyenda').set('html', 'Actualizando datos del portal...');
				$('img').setStyle('display', 'block');
			},
			onSuccess: function(result) {
				$('img').setStyle('display', 'none');
				
				$('leyenda').set('html', result);
			}
		}).send();
	});
});
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
