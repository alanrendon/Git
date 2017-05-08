<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
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
  <div id="titulo">Alta de Tipos de Contacto</div>
  <div id="captura" align="center">
  	<form action="AltaTipoContacto.php" method="post" name="AltaTipoContacto" id="AltaTipoContacto" class="formulario">
  	  <table class="tabla_captura">
      <tr>
        <th align="left" scope="row">Tipo</th>
        <td class="linea_on"><input name="Tipo" type="text" class="cap toText toUpper clean" id="Tipo" size="50" maxlength="50" /></td>
      </tr>
    </table>
    <p>
      <input name="Alta" type="button" class="boton" id="Alta" value="Alta" />
    </p></form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
	var f = new Formulario('AltaTipoContacto');
	
	f.mostrarAlertas = true;
	
	f.form.getElementById('Alta').addEvent('click', function()
	{
		if (f.form.getElementById('Tipo').value.trim().length == '')
		{
			alert('Error: el registro debe contener al menos el nombre del tipo de contacto');
			f.form.getElementById('Tipo').select();
		}
		else if (confirm('¿Son correctos los datos?'))
		{
			f.form.submit();
		}
	});
	
	f.form.addEvent('submit', function(e)
	{
		new Event(e).stop();
	});
	
	f.form.getElementById('Tipo').select();
});

//-->
</script>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
