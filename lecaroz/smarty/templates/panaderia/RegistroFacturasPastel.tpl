<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Registro de Facturas de Pastel</title>

<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>

</head>

<body>
<!-- START BLOCK : captura -->
<div id="contenedor">
  <div id="titulo">Registro de Facturas de Pastel</div>
  <div id="captura" align="center">
    <form action="plantillas/pan/RegistroFacturasPastel.php" method="post" name="RegistroFacturasPastel" id="RegistroFacturasPastel" class="formulario">
	<table cellspacing="1" class="tabla_captura">
      <tr>
        <th align="left" class="encabezado" scope="row">Compa&ntilde;&iacute;a</th>
        <td width="200" align="left" class="linea_on"><input name="num_cia" type="text" class="cap toPosInt" id="num_cia" style="width:22px;" size="3" />
        <!--<input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="40" />--><span id="nombre_cia"></span></td>
      </tr>
      <tr>
        <th align="left" class="encabezado" scope="row">Fecha</th>
        <td align="left" class="linea_on"><input name="fecha" type="text" class="cap toDate" id="fecha" style="width:70px;" size="10" maxlength="10" /></td>
      </tr>
    </table>
    <table cellspacing="1" class="tabla_captura" id="tabla_captura">
	  <tr>
	    <th align="center"><img src="imagenes/tool16x16.png" alt="Herramientas" width="16" height="16" /></th>
        <th align="center">Factura</th>
        <th align="center">Exp</th>
        <th align="center">Kilos</th>
        <th align="center">Precio<br />Unidad</th>
        <th align="center">Pan</th>
        <th align="center">Base</th>
        <th align="center">A cuenta </th>
        <th align="center">Devoluci&oacute;n<br />de Base </th>
        <th align="center">Resta</th>
        <th align="center">Fecha<br />Entrega</th>
        <th align="center">Pastillaje</th>
        <th align="center">Otros</th>
      </tr>
      <!-- START BLOCK : fila -->
	  <tr class="linea_{color_row}">
	    <td align="center"><img src="imagenes/BulletGray16x16.png" width="16" height="16" /></td>
        <td align="center"><input name="letra_folio[]" type="text" class="cap toText" id="letra_folio" style="width:10px; text-align:center;" size="1" maxlength="1" />
        <input name="num_remi[]" type="text" class="cap toPosInt" id="num_remi" style="width:35px;" size="5" /></td>
        <td align="center"><input name="exp[]" type="text" class="cap toPosInt" id="exp" size="3" /></td>
        <td align="center"><input name="kilos[]" type="text" class="cap numPosFormat2" id="kilos" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="precio_unidad[]" type="text" class="cap numPosFormat2" id="precio_unidad" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="otros[]" type="text" class="cap numPosFormat2" id="otros" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="base[]" type="text" class="cap numPosFormat2" id="base" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="cuenta[]" type="text" class="cap numPosFormat2" id="cuenta" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="dev_base[]" type="text" class="cap numPosFormat2" id="dev_base" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="resta[]" type="text" class="cap numPosFormat2" id="resta" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="fecha_entrega[]" type="text" class="cap toDate" id="fecha_entrega" style="width:70px;" size="10" maxlength="10" /></td>
        <td align="center"><input name="pastillaje[]" type="text" class="cap numPosFormat2" id="pastillaje" style="text-align:right;" size="6" /></td>
        <td align="center"><input name="otros_efectivos[]" type="text" class="cap numPosFormat2" id="otros_efectivos" style="text-align:right;" size="6" /></td>
		<!-- END BLOCK : fila -->
      </tr>
    </table>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript">
<!--
window.addEvent('domready', function() {
		var f = new Formulario('RegistroFacturasPastel');
		var t = new Tabla();
		f.form.getElementById('num_cia').addEvent('change', function(e) {
			new Event(e).stop();
			
			f.obtenerCampoCatalogo({tabla:'catalogo_companias', campo:'nombre_corto', criterio:'num_cia', valor:f.form.getElementById('num_cia').getValue(), actualiza:'nombre_cia'});
		});
		
		f.form.num_cia.select();
	});

//-->
</script>
<!-- END BLOKC : captura -->
<!-- START BLOCK : validacion -->
<div id="contenedor">

</div>
<!-- END BLOCK : validacion -->
</body>
</html>
