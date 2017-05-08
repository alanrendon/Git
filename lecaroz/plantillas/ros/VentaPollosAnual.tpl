<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Venta de Pollos Anual</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script type="text/javascript" src="jscripts/ros/VentaPollosAnual.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Venta de Pollos Anual </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_on"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
		  <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
        </select>        </td>
      </tr>
      <tr>
        <th align="left">A&ntilde;o(s)</th>
        <td class="linea_off"><input name="anios" type="text" class="cap toInterval" id="anios" value="{anio}" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Pollos</th>
        <td class="linea_on"><input name="codmp[]" type="checkbox" class="checkbox" id="codmp" value="160" checked="checked" />
          Normales<br />
          <input name="codmp[]" type="checkbox" class="checkbox" id="codmp" value="700" checked="checked" />
          Grandes<br />
          <input name="codmp[]" type="checkbox" class="checkbox" id="codmp" value="600" checked="checked" />
          Chicos</td>
      </tr>
    </table>
      <p>
        <input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
