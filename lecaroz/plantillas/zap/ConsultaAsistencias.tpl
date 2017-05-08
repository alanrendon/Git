<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Asistencia</title>

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
<script type="text/javascript" src="jscripts/zap/ConsultaAsistencias.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Reporte de Asistencias</div>
  <div id="captura" align="center">
  <form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
  <tr class="linea_off">
    <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
    <td align="left"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Periodo</th>
    <td align="left"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
      al
        <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Empleado(s)</th>
    <td align="left"><select name="idemp[]" size="10" multiple="multiple" id="idemp" style="width:100%;">
      <option value=""></option>
    </select>
    </td>
  </tr>
</table>
  <p>
    <input name="generar" type="button" class="boton" id="generar" value="Generar Reporte" />
  </p>
  </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
