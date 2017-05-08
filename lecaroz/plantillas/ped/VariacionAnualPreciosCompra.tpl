<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Variaci&oacute;n Anual de Precios de Compra</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/ped/VariacionAnualPreciosCompra.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Variaci&oacute;n Anual de Precios de Compra </div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
          <td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Administrador</th>
          <td><select name="admin" id="admin">
            <option value=""></option>
			<!-- START BLOCK : admin -->
            <option value="{id}">{nombre}</option>
			<!-- END BLOCK : admin -->
          </select>
          </td>
        </tr>
        <tr class="linea_off">
          <th align="left" scope="row">A&ntilde;o</th>
          <td>{anio}</td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Tipo</th>
          <td><input name="tipo" type="radio" value="1" checked="checked" />
            Anual<br />
            <input name="tipo" type="radio" value="2" />
            Mensual</td>
        </tr>
      </table>
      <br />
      <p>
      <input name="descargar" type="button" id="descargar" value="Descargar Reporte" />
	  &nbsp;&nbsp;
      <input name="generar" type="button" class="boton" id="generar" value="Generar Reporte" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>