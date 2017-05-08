<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Prima de Riesgo de Trabajo</title>

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
<script type="text/javascript" src="jscripts/ban/PrimaRiesgoTrabajo.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">
    <p>Prima de Riesgo de Trabajo </p>
  </div>
  <div id="captura" align="center">
  <form name="Datos" class="formulario" id="Datos">
    <table class="tabla_captura">
      <tr>
        <th scope="col">Compa&ntilde;&iacute;a</th>
        <th scope="col">Reg. Patronal </th>
        <th scope="col">Prima</th>
      </tr>
      <!-- START BLOCK : row -->
	  <tr id="row" class="linea_{color}">
        <td><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
          {num_cia} {nombre_cia} </td>
        <td align="center">{no_imss}</td>
        <td><input name="prima[]" type="text" class="cap numPosFormat5 alignRight" id="prima" value="{prima}" size="9" maxlength="9" /></td>
      </tr>
	  <!-- END BLOCK : row -->
    </table>
    <p>
      <input name="actualizar" type="submit" class="boton" id="actualizar" value="Actualizar" />
    </p>
  </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
