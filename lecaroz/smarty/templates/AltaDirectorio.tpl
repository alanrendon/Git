<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Contacto en Directorio</title>
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<!--<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.11.js"></script>-->
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css" media="screen">
#TipoTel
{
	padding-left: 20px;
	background-repeat: no-repeat;
}

.tipoTel
{
	padding: 2px 0px 2px 20px;
	margin: 0px 0px 0px 10px;
	background-repeat: no-repeat;
}

.celular
{
	background-image: url(imagenes/celular16x16.png);
}

.casa
{
	background-image: url(imagenes/house16x16.png);
}

.trabajo
{
	background-image: url(imagenes/work16x16.png);
}

.fax
{
	background-image: url(imagenes/fax16x16.png);
}

.otro
{
	background-image: url(imagenes/phone16x16.png);
}

.tarjeta
{
	border: solid 2px #000;
}
</style>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Alta de Contacto en Directorio </div>
  <div id="captura" align="center">
    <form action="AltaDirectorio.php" method="post" name="AltaDirectorio" id="AltaDirectorio" class="formulario">
	<table class="tabla_captura">
      <tr class="linea_off">
        <th align="left" scope="row">Nombre o Raz&oacute;n Social </th>
        <td><input name="Nombre" type="text" class="cap toText toUpper clean" id="Nombre" size="50" maxlength="100" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Contacto</th>
        <td><input name="Contacto" type="text" class="cap toText toUpper clean" id="Contacto" size="50" maxlength="100" /></td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Puesto</th>
        <td><input name="Puesto" type="text" class="cap toText toUpper clean" id="Puesto" size="50" maxlength="100" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Tipo</th>
        <td>
		  <!-- START BLOCK : campo_tipo -->
		  <select name="Tipo[]" id="Tipo">
            <option value="" selected="selected"></option>
            <!-- START BLOCK : tipo -->
		    <option value="{id}">{tipo}</option>
		    <!-- END BLOCK : tipo -->
          </select>
		  {salto}
		  <!-- END BLOCK : campo_tipo -->		</td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Tel&eacute;fono</th>
        <td>
		  <input name="Telefono[]" type="text" class="cap phoneNumber" id="Telefono" size="20" maxlength="20" />
          <select name="TipoTel[]" id="TipoTel">
            <option value="NULL" selected="selected"></option>
            <option value="1" class="tipoTel celular">CELULAR</option>
            <option value="2" class="tipoTel casa">CASA</option>
            <option value="3" class="tipoTel trabajo">TRABAJO</option>
            <option value="4" class="tipoTel fax">FAX</option>
            <option value="5" class="tipoTel otro">OTRO</option>
          </select>
          <input name="ObsTel[]" type="text" class="cap toText toUpper clean" id="ObsTel" maxlength="100" />
          <br />
		  <input name="Telefono[]" type="text" class="cap phoneNumber" id="Telefono" size="20" maxlength="20" />
          <select name="TipoTel[]" id="TipoTel">
            <option value="NULL" selected="selected"></option>
            <option value="1" class="tipoTel celular">CELULAR</option>
            <option value="2" class="tipoTel casa">CASA</option>
            <option value="3" class="tipoTel trabajo">TRABAJO</option>
            <option value="4" class="tipoTel fax">FAX</option>
            <option value="5" class="tipoTel otro">OTRO</option>
          </select>
          <input name="ObsTel[]" type="text" class="cap toText toUpper clean" id="ObsTel" maxlength="100" />
          <br />
		  <input name="Telefono[]" type="text" class="cap phoneNumber" id="Telefono" size="20" maxlength="20" />
          <select name="TipoTel[]" id="TipoTel">
            <option value="NULL" selected="selected"></option>
            <option value="1" class="tipoTel celular">CELULAR</option>
            <option value="2" class="tipoTel casa">CASA</option>
            <option value="3" class="tipoTel trabajo">TRABAJO</option>
            <option value="4" class="tipoTel fax">FAX</option>
            <option value="5" class="tipoTel otro">OTRO</option>
          </select>
          <input name="ObsTel[]" type="text" class="cap toText toUpper clean" id="ObsTel" maxlength="100" />
<br />
		  <input name="Telefono[]" type="text" class="cap phoneNumber" id="Telefono" size="20" maxlength="20" />
          <select name="TipoTel[]" id="TipoTel">
            <option value="NULL" selected="selected"></option>
            <option value="1" class="tipoTel celular">CELULAR</option>
            <option value="2" class="tipoTel casa">CASA</option>
            <option value="3" class="tipoTel trabajo">TRABAJO</option>
            <option value="4" class="tipoTel fax">FAX</option>
            <option value="5" class="tipoTel otro">OTRO</option>
          </select>
          <input name="ObsTel[]" type="text" class="cap toText toUpper clean" id="ObsTel" maxlength="100" />
<br />
		  <input name="Telefono[]" type="text" class="cap phoneNumber" id="Telefono" size="20" maxlength="20" />
          <select name="TipoTel[]" id="TipoTel">
            <option value="NULL" selected="selected"></option>
            <option value="1" class="tipoTel celular">CELULAR</option>
            <option value="2" class="tipoTel casa">CASA</option>
            <option value="3" class="tipoTel trabajo">TRABAJO</option>
            <option value="4" class="tipoTel fax">FAX</option>
            <option value="5" class="tipoTel otro">OTRO</option>
          </select>
          <input name="ObsTel[]" type="text" class="cap toText toUpper clean" id="ObsTel" maxlength="100" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Email</th>
        <td><input name="Email" type="text" class="cap eMail" id="Email" size="50" maxlength="100" /></td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Observaciones</th>
        <td><textarea name="Observaciones" cols="20" rows="4" class="cap toText toUpper clean" id="Observaciones" style="width:100%;"></textarea></td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row"><div style="float:left;">Tarjeta</div><div style="float:right;"><img src="imagenes/scanner16x16.png" name="scan" width="16" height="16" id="scan" />&nbsp;<img src="imagenes/delete16x16.png" name="delete" width="16" height="16" id="delete" /></div></th>
        <td id="tarjeta">&nbsp;</td>
      </tr>
    </table>
    <p>
      <input name="Alta" type="button" class="boton" id="Alta" value="Alta" />
    </p>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	var f = new Formulario('AltaDirectorio');
	
	f.mostrarAlertas = true;
	
	f.form.getElementById('Observaciones').addEvent('blur', function()
	{
		this.value = this.value.toUpperCase();
		this.value = this.value.clean();
		
		if (this.value.length > 255)
		{
			this.value = this.value.substr(0, 255);
		}
	});
	
	$('scan').setOpacity(0.5);
	$('scan').addEvents({
		'mouseover': function()
		{
			this.setStyle('cursor', 'pointer');
			this.setOpacity(1);
		},
		'mouseout': function()
		{
			this.setStyle('cursor', 'default');
			this.setOpacity(0.5);
		},
		'click': function()
		{
			var win = window.open('EscanearTarjetaContacto.php', '', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1024,height=768');
			win.focus();
		}
	});
	
	$('delete').setOpacity(0.5);
	$('delete').addEvents({
		'mouseover': function()
		{
			this.setStyle('cursor', 'pointer');
			this.setOpacity(1);
		},
		'mouseout': function()
		{
			this.setStyle('cursor', 'default');
			this.setOpacity(0.5);
		},
		'click': function()
		{
			if (confirm('¿Desea eliminar la tarjeta escaneada?')) {
				new Request({
					url: 'AltaDirectorio.php',
					method: 'get',
					data: 'del=1',
					onSuccess: function(result)
					{
						$('tarjeta').set('html', '');
					}
				}).send();
			}
		}
	});
	
	f.form.getElementById('Alta').addEvent('click', function()
	{
		if (f.form.getElementById('Nombre').value.trim().length == '')
		{
			alert('Error: el registro debe contener al menos el nombre del contacto');
			f.form.getElementById('Nombre').select();
		}
		else if (confirm('¿Son correctos los datos?'))
		{
			f.form.submit();
		}
	});
	
	f.form.getElements('#TipoTel').each(function(tipo) {
		tipo.addEvent('change', function()
		{
			var style = null;
			
			switch(this.selectedIndex)
			{
				case 1:
					style = 'url(imagenes/celular16x16.png)';
					break;
				case 2:
					style = 'url(imagenes/house16x16.png)';
					break;
				case 3:
					style = 'url(imagenes/work16x16.png)';
					break;
				case 4:
					style = 'url(imagenes/fax16x16.png)';
					break;
				case 5:
					style = 'url(imagenes/phone16x16.png)';
					break;
				default:
					style = 'none';
			}
			
			this.setStyle('background-image', style);
		});
	});
	
	f.form.addEvent('submit', function(e)
	{
		new Event(e).stop();
	});
	
	f.form.getElementById('Nombre').select();
});

//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</body>
</html>
