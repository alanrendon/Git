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
  <div id="titulo"> Archivo de Pagos para Portal Lecaroz </div>
  <div id="captura" align="center">
    <form action="" method="get" name="Pagos" class="formulario" id="Pagos">
      <input name="botonPagos" type="button" class="boton" id="botonPagos" value="Archivo de Pagos" />
    </form>
	<br />
	<form action="" method="get" name="Pendientes" class="formulario" id="Pendientes">
      <input name="botonPendientes" type="button" class="boton" id="botonPendientes" value="Archivo de Pendientes" />
    </form>
	<br />
    <form action="" method="get" name="Catalogo" class="formulario" id="Catalogo">
      <input name="botonCatalogo" type="button" class="boton" id="botonCatalogo" value="Cat&aacute;logo de Proveedores" />
    </form>
	<br />
	<div id="status">
	  <div id="img" style="display:none;">
	    <img src="imagenes/ajax-loader.gif" />
	  </div>
	  <div id="leyenda">
	  </div>
	</div>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
var fpagos, fcatalogo;

window.addEvent('domready', function() {
	fpagos = new Formulario('Pagos');
	fpendientes = new Formulario('Pendientes');
	fcatalogo = new Formulario('Catalogo');
	
	fpagos.form.botonPagos.addEvent('click', function() {
		new Request({
			url: 'ArchivoPagos.php',
			method: 'get',
			data: {
				pagos: 1
			},
			onRequest: function() {
				$('leyenda').set('html', 'Cargando archivo de pagos');
				$('img').setStyle('display', 'block');
			},
			onSuccess: function(result) {
				$('img').setStyle('display', 'none');
				
				if (result.getVal() < 0)
					switch (result.getVal()) {
						case -1:
							$('leyenda').set('html', 'No hay pagos para cargar al portal');
						break;
						case -2:
							$('leyenda').set('html', 'ERROR: No se pudo crear el archivo de datos');
						break;
						case -3:
							$('leyenda').set('html', 'ERROR: No se pudo conectar al servidor FTP');
						break;
						case -4:
							$('leyenda').set('html', 'ERROR: Usuario y contraseña incorrectos para el servidor FTP');
						break;
						case -5:
							$('leyenda').set('html', 'ERROR: No se pudo cambiar a directorio destino');
						break;
						case -6:
							$('leyenda').set('html', 'ERROR: No se pudo borrar el archivo destino');
						break;
						case -7:
							$('leyenda').set('html', 'ERROR: No se pudo subir el archivo');
						break;
					}
				else {
					$('leyenda').set('html', '¡¡¡Carga de archivo completa!!!');
				}
			}
		}).send();
	});
	
	fpendientes.form.botonPendientes.addEvent('click', function() {
		new Request({
			url: 'ArchivoPagos.php',
			method: 'get',
			data: {
				pendientes: 1
			},
			onRequest: function() {
				$('leyenda').set('html', 'Cargando archivo de pagos');
				$('img').setStyle('display', 'block');
			},
			onSuccess: function(result) {
				$('img').setStyle('display', 'none');
				
				if (result.getVal() < 0)
					switch (result.getVal()) {
						case -1:
							$('leyenda').set('html', 'No hay pendientes para cargar al portal');
						break;
						case -2:
							$('leyenda').set('html', 'ERROR: No se pudo crear el archivo de datos');
						break;
						case -3:
							$('leyenda').set('html', 'ERROR: No se pudo conectar al servidor FTP');
						break;
						case -4:
							$('leyenda').set('html', 'ERROR: Usuario y contraseña incorrectos para el servidor FTP');
						break;
						case -5:
							$('leyenda').set('html', 'ERROR: No se pudo cambiar a directorio destino');
						break;
						case -6:
							$('leyenda').set('html', 'ERROR: No se pudo borrar el archivo destino');
						break;
						case -7:
							$('leyenda').set('html', 'ERROR: No se pudo subir el archivo');
						break;
					}
				else {
					$('leyenda').set('html', '¡¡¡Carga de archivo completa!!!');
				}
			}
		}).send();
	});
	
	fcatalogo.form.botonCatalogo.addEvent('click', function() {
		new Request({
			url: 'ArchivoPagos.php',
			method: 'get',
			data: {
				catalogo: 1
			},
			onRequest: function() {
				$('leyenda').set('html', 'Cargando archivo de pagos');
				$('img').setStyle('display', 'block');
			},
			onSuccess: function(result) {
				$('img').setStyle('display', 'none');
				
				if (result.getVal() < 0)
					switch (result.getVal()) {
						case -1:
							$('leyenda').set('html', 'No hay pendientes para cargar al portal');
						break;
						case -2:
							$('leyenda').set('html', 'ERROR: No se pudo crear el archivo de datos');
						break;
						case -3:
							$('leyenda').set('html', 'ERROR: No se pudo conectar al servidor FTP');
						break;
						case -4:
							$('leyenda').set('html', 'ERROR: Usuario y contraseña incorrectos para el servidor FTP');
						break;
						case -5:
							$('leyenda').set('html', 'ERROR: No se pudo cambiar a directorio destino');
						break;
						case -6:
							$('leyenda').set('html', 'ERROR: No se pudo borrar el archivo destino');
						break;
						case -7:
							$('leyenda').set('html', 'ERROR: No se pudo subir el archivo');
						break;
					}
				else {
					$('leyenda').set('html', '¡¡¡Carga de archivo completa!!!');
				}
			}
		}).send();
	});
});
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
