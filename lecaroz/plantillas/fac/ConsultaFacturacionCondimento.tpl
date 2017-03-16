<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Actualizar Precios de Compra</title>

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
<script type="text/javascript" src="jscripts/ros/ConsultaFacturacionCondimento.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Consulta de Facturaci&oacute;n de Condimento </div>
  <div id="captura" align="center">
    <form action="ConsultaFacturacionCondimento.php" method="post" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_on"><input name="fecha1" type="text" class="cap toDate" id="fecha1" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap toDate" id="fecha2" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <th align="left">Filtros</th>
        <td class="linea_on"><input name="pendientes" type="checkbox" class="checkbox" id="pendientes" value="1" checked="checked" />
          Pendientes<br />
          <input name="facturados" type="checkbox" class="checkbox" id="facturados" value="1" checked="checked" />
          Facturados</td>
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
