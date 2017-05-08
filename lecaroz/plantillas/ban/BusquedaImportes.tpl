<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>B&uacute;squeda de Importes</title>

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
<script type="text/javascript" src="jscripts/ban/BusquedaImportes.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">B&uacute;squeda de Importes </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
      <tr>
        <th align="left">Banco</th>
        <td class="linea_on"><select name="cuenta" id="cuenta">
          <option value="" selected="selected"></option>
          <option value="1">BANORTE</option>
          <option value="2">SANTANDER</option>
        </select>        </td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_off"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <th align="left">Tipo</th>
        <td class="linea_on"><input name="abonos" type="checkbox" class="checkbox" id="abonos" value="1" checked="checked" />
          Abonos<br />
          <input name="cargos" type="checkbox" class="checkbox" id="cargos" value="1" checked="checked" />
          Cargos</td>
      </tr>
      <tr class="linea_off">
        <th rowspan="10" align="left">Importe(s)</th>
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_on">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_off">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_on">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_off">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_on">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_off">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_on">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
      <tr class="linea_off">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
	  <tr class="linea_on">
        <td><input name="importe[]" type="text" class="cap numPosFormat2 alignRight" id="importe" size="10" /></td>
      </tr>
    </table>
      <p>
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
