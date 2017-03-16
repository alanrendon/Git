<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Registro de Efectivos Completos</title>

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
<script type="text/javascript" src="jscripts/pan/RegistroEfectivosCompletos.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">
    <p>Registro de Efectivos Completos </p>
  </div>
  <div id="captura" align="center">
  <form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
  <tr class="linea_off">
    <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
    <td align="left" class="linea_off"><select name="cias[]" size="5" multiple="MULTIPLE" id="cias">
    </select>
    </td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Fecha de corte </th>
    <td align="left" class="linea_on"><input name="fecha" type="text" class="cap toDate alignCenter" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
  </tr>
</table>
  <p style="font-weight:bold;font-size:8pt;">
  Bloqueos: [E]
  Tickets de error, [C]
  Clientes, [EC] Tickets y clientes.
  </p>
  <p>
    <input name="registrar" type="button" class="boton" id="registrar" value="Registrar" />
  </p>
  </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
