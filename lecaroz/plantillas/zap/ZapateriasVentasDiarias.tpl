<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Captura de Ventas Diarias de Zapater&iacute;as</title>

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
<script type="text/javascript" src="jscripts/zap/ZapateriasVentasDiarias.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Captura de Ventas Diarias de Zapater&iacute;as </div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th scope="row">Fecha</th>
          <td><input name="fecha" type="text" class="valid Focus toDate center font14" id="fecha" size="10" maxlength="10" /></td>
        </tr>
      </table>
      <br />
      <table class="tabla_captura">
        <tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : row -->
		<tr id="row" class="linea_{color}">
          <td><input name="num_cia[]" type="hidden" id="num_cia" value="{num_cia}" />
          {num_cia} {nombre_cia} </td>
          <td align="center"><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" cia="{num_cia}" size="10" /></td>
        </tr>
		<!-- END BLOCK : row -->
        <tr>
          <th align="right">Total</th>
          <th><input name="total" type="text" disabled="disabled" class="font12 right bold" id="total" value="0.00" size="10" /></th>
        </tr>
      </table>
      <p>
        <input name="registrar" type="button" id="registrar" value="Registrar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>