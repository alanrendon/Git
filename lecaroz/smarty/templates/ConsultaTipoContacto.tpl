<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Tipos de Contacto</title>
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.11.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Consulta de Tipos de Contacto</div>
  <div id="captura" align="center">
    <table class="tabla_captura">
  <tr>
    <th scope="col"><img src="imagenes/tool16x16.png" alt="Herramientas" width="16" height="16" longdesc="Herramientas" /></th>
    <th scope="col">Tipo</th>
  </tr>
  <!-- START BLOCK : tipo -->
  <tr class="linea_{color_row}" id="row_{id}">
    <td><img src="imagenes/pencil16x16.png" alt="{id}" name="modificar" width="16" height="16" id="modificar" longdesc="Modificar tipo" /><img src="imagenes/delete16x16.png" alt="{id}" name="eliminar" width="16" height="16" id="eliminar" longdesc="Borrar tipo" /></td>
    <td id="data_{id}">{Tipo}</td>
  </tr>
  <!-- END BLOCK : tipo -->
</table>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	$$('#modificar', '#eliminar').each(function(img)
	{
		img.setOpacity(0.5);
		
		img.addEvents({
			'mouseover': function()
			{
				this.setStyle('cursor', 'pointer');
				img.setOpacity(1);
			},
			'mouseout': function()
			{
				this.setStyle('cursor', 'default');
				img.setOpacity(0.5);
			}
		});
	});
	
	$$('#modificar').each(function(img)
	{
		
	});
	
	$$('#eliminar').each(function(img)
	{
		img.addEvent('click', function(e)
		{
			new Event(e).stop();
			
			if (confirm('¿Desea eliminar el registro del tipo de contacto?'))
			{
				$('data_' + img.getProperty('alt')).setHTML('Eliminando...');
				
				new Ajax('ConsultaTipoContacto.php', {
					method: 'get',
					data: 'id=' + img.getProperty('alt') + '&accion=eliminar',
					onComplete: function()
					{
						$('row_' + img.getProperty('alt')).remove();
					}
				}).request();
			}
		});
	});
});

//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
