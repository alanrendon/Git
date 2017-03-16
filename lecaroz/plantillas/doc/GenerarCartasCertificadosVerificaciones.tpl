<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cartas para Certificados de Verificaci&oacute;n </title>

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
<script type="text/javascript" src="jscripts/doc/GenerarCartasCertificadosVerificaciones.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Cartas para Certificados de Verificaci&oacute;n </div>
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
        <th align="left">Veh&iacute;culo(s)</th>
        <td class="linea_off"><input name="vehiculos" type="text" class="cap toInterval" id="vehiculos" size="50" /></td>
      </tr>
      <tr>
        <th align="left">A&ntilde;o</th>
        <td class="linea_on"><input name="anio" type="text" class="cap toInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_off"><input name="periodo" type="radio" class="checkbox" value="1" checked="checked" />
          1er. periodo<br />
          <input name="periodo" type="radio" class="checkbox" value="2" />
          2do. periodo </td>
      </tr>
      <tr>
        <th align="left">Color</th>
        <td class="linea_off"><select name="color" id="color">
          <option value="" style="background-color:#FFF;" selected="selected"></option>
          <option value="1" style="background-color:#FF8;font-weight:bold;">AMARILLO</option>
          <option value="2" style="background-color:#FCC;font-weight:bold;">ROSA</option>
          <option value="3" style="background-color:#F66;font-weight:bold;">ROJO</option>
          <option value="4" style="background-color:#AFA;font-weight:bold;">VERDE</option>
          <option value="5" style="background-color:#09F;font-weight:bold;">AZUL</option>
        </select></td>
      </tr>
    </table>
      <p>
        <input name="generar" type="button" class="boton" id="generar" value="Generar Cartas" />
      </p>
    </form>
	<div id="result"></div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
