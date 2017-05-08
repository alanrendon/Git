<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Directorio de Contactos</title>
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/lightbox.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<link href="styles/lightbox.css" rel="stylesheet" type="text/css" />

<style type="text/css" media="screen">
#tarjeta
{
	float: left;
	width: 250px;
	text-align: center;
}

#datos
{
	float: right;
	width: 600px;
	margin-left: 10px;
}

#nombre
{
	font-size: 11pt;
	font-weight: bold;
	border-bottom: 3px solid #73a8b7;
	background-color: #b7d7dd;
}

#contacto
{
	font-weight: bold;
	margin: 5px 0px 5px 0px;
}

#elemento
{
	border: 1px solid #73a8b7;
	margin: 1px 0px 1px 0px;
	padding: 2px 2px 2px 2px;
	background-color: #b7d7dd;
}

#elemento .titulo
{
	color: #696969;
	font-weight: bold;
	font-size: 8pt;
}

#elemento .tipos
{
	font-size: 9pt;
	text-decoration: underline;
}

ul
{
	list-style: none;
}

li
{
	padding: 0px 0px 10px 0px;
}

li.celular
{
	list-style-image: url(imagenes/celular16x16.png);
}

li.casa
{
	list-style-image: url(imagenes/house16x16.png);
}

li.trabajo
{
	list-style-image: url(imagenes/work16x16.png);
}

li.fax
{
	list-style-image: url(imagenes/fax16x16.png);
}

li.otro
{
	list-style-image: url(imagenes/phone16x16.png);
}

li.email
{
	list-style-image: url(imagenes/email16x16.png);
}

a
{
	font-weight: bold;
}

a:link
{
	color: #000;
}

a:visited
{
	color: #000;
}

a:hover
{
	color: #C00;
}

a:active
{
	color: #00C;
}
</style>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Consulta de Directorio de Contactos </div>
  <div id="captura" align="center">
    <table class="tabla_captura">
      <tr>
        <th align="left" scope="row">&Iacute;ndice</th>
        <td class="linea_on"><a href="ConsultaDirectorio.php">Todos</a>
		<!-- START BLOCK : letter -->
		<a href="ConsultaDirectorio.php?letter={letter}">{letter}</a>
	  <!-- END BLOCK : letter -->
	  </tr>
    </table>
  <table class="tabla_captura" id="Contactos">
  <tr>
    <th scope="row"><img src="imagenes/tool16x16.png" alt="Opciones" title="Opciones" width="16" height="16" /></th>
    <th width="860">Contacto</th>
    <th><img src="imagenes/tool16x16.png" alt="Opciones" title="Opciones" width="16" height="16" /></th>
  </tr>
  <!-- START BLOCK : contacto -->
  <tr class="linea_{color_row}" id="row_{id}">
    <td scope="row"><img src="imagenes/pencil16x16.png" alt="{id}" name="modificar" width="16" height="16" id="modificar" title="Modificar contacto" /><img src="imagenes/delete16x16.png" alt="{id}" name="eliminar" width="16" height="16" id="eliminar" title="Borrar contacto" /></td>
    <td height="75" valign="top" nowrap="nowrap" id="data_{id}"><div style="float:right;font-weight:bold;">#{Numero}</div><div id="nombre">{Nombre}</div>
      <span style="font-weight:bold;">{Contacto}</span><br />
      {Telefono}<br />
      <a href="mailto:{Email}">{Email}</a></td>
    <td><img src="imagenes/plus16x16.png" alt="{id},{offset}" name="expandir" width="16" height="16" id="expandir" longdesc="Expandir datos del contacto" /><img src="imagenes/minus16x16.png" alt="{id}" name="contraer" width="16" height="16" id="contraer" longdesc="Contraer datos del contacto" /></td>
  </tr>
  <!-- END BLOCK : contacto -->
</table>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	$$(document.getElementsByName('expandir')).each(function(img) {
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
			},
			'click': function(e)
			{
				new Event(e).stop();
				
				var tmp = img.getProperty('alt').split(',');
				var id = tmp[0].getVal();
				var offset = tmp[1].getVal();
				
				$('data_' + id).set('html', '<img src="imagenes/wait.gif" alt="Cargando..." />');
				
				new Request.HTML({
					url: 'ConsultaDirectorio.php',
					method: 'get',
					data: 'id=' + id + '&accion=expandir',
					update: $('data_' + id)
				}).send();
			}
		});
	});
	
	$$(document.getElementsByName('contraer')).each(function(img)
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
			},
			'click': function(e)
			{
				new Event(e).stop();
				
				var id = img.getProperty('alt').getVal();
				
				$('data_' + id).set('html', '<img src="imagenes/wait.gif" alt="Cargando..." />');
				
				new Request.HTML({
						url: 'ConsultaDirectorio.php',
						method: 'get',
						data: 'id=' + id + '&accion=contraer',
						update: $('data_' + id)
					}).send();
			}
		});
	});
	
	$$(document.getElementsByName('modificar')).each(function(img)
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
			},
			'click': function(e)
			{
				new Event(e).stop();
				
				var popup;
				
				popup = window.open('ModificarContactoDirectorio.php?id=' + img.getProperty('alt'), 'ModificarContacto', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1024,height=768');
				
				popup.focus();
			}
		});
	});
	
	$$(document.getElementsByName('eliminar')).each(function(img)
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
			},
			'click': function(e)
			{
				new Event(e).stop();
				
				if (confirm('¿Desea eliminar el registro del contacto?'))
				{
					$('data_' + img.getProperty('alt')).set('html', 'Eliminando...');
					
						new Request.HTML({
							url: 'ConsultaDirectorio.php',
							method: 'get',
							data: 'id=' + img.getProperty('alt') + '&accion=eliminar',
							onSuccess: function(result)
							{
								$('row_' + img.getProperty('alt')).dispose();
							}
						}).send();
				}
			}
		});
	});
});

function actualizarContacto(id) {
	new Request.HTML({
		url: 'ConsultaDirectorio.php',
		method: 'get',
		data: 'id=' + id + '&accion=expandir',
		update: $('data_' + id)
	}).send();
}
//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</body>
</html>
