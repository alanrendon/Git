<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Cheques, Transferencias y Otros Pagos</title>

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
<script type="text/javascript" src="jscripts/ban/ConsultaCheques.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">
    <p>Consulta de Cheques, Transferencias y Otros Pagos </p>
  </div>
  <div id="captura" align="center">
  <form name="Datos" class="formulario" id="Datos">
  <table class="tabla_captura">
  <tr class="linea_off">
    <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
    <td align="left"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Proveedor(es)</th>
    <td align="left"><input name="pros" type="text" class="cap toInterval" id="pros" size="40" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Banco</th>
    <td align="left"><select name="cuenta" id="cuenta">
      <option value="" selected="selected"></option>
      <option value="1">BANORTE</option>
      <option value="2">SANTANDER</option>
    </select></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Folio(s)</th>
    <td align="left"><input name="folios" type="text" class="cap toInterval" id="folios" size="40" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Periodo</th>
    <td align="left"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
      al
        <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Cobrado</th>
    <td align="left"><input name="fecha_con1" type="text" class="cap toDate alignCenter" id="fecha_con1" size="10" maxlength="10" />
      al
        <input name="fecha_con2" type="text" class="cap toDate alignCenter" id="fecha_con2" size="10" maxlength="10" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Gasto(s)</th>
    <td align="left"><input name="gastos" type="text" class="cap toInterval" id="gastos" size="40" /></td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Importe</th>
    <td align="left"><input name="importe" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Estatus</th>
    <td align="left"><input name="pendientes" type="checkbox" class="checkbox" id="pendientes" value="1" checked="checked" />
      Pendientes<br />
      <input name="cobrados" type="checkbox" class="checkbox" id="cobrados" value="1" checked="checked" />
      Cobrados<br />
      <input name="cancelados" type="checkbox" class="checkbox" id="cancelados" value="1" checked="checked" />
      Cancelados</td>
  </tr>
  <tr class="linea_on">
    <th align="left" scope="row">Tipo</th>
    <td align="left"><input name="cheques" type="checkbox" class="checkbox" id="cheques" value="1" checked="checked" />
      Cheques<br />
      <input name="transferencias" type="checkbox" class="checkbox" id="transferencias" value="1" checked="checked" />
      Transferencias<br />
      <input name="otros" type="checkbox" class="checkbox" id="otros" value="1" checked="checked" />
      Otros</td>
  </tr>
  <tr class="linea_off">
    <th align="left" scope="row">Opciones</th>
    <td align="left"><input name="sumar_cancelados" type="checkbox" class="checkbox" id="sumar_cancelados" value="1" />
      Sumar cancelados al total</td>
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
