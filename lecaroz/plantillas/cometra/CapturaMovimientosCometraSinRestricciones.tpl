<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Captura de Movimientos de Cometra (sin restricciones)</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="../../styles/Popups.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="styles/Popups.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Popups.js"></script>
<script type="text/javascript" src="jscripts/cometra/CapturaMovimientosCometraSinRestricciones.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Captura de Movimientos de Cometra (sin restricciones) </div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Banco</th>
          <td align="center" class="bold center font12">          {nombre_banco}          </td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Comprobante</th>
          <td align="center"><input name="comprobante" type="text" class="valid Focus onlyNumbers center font12 bold" id="comprobante" size="10" /></td>
        </tr>
      </table>
      <br />
      <table class="tabla_captura">
        <tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Tipo</th>
          <th scope="col">Fecha</th>
          <th scope="col">Concepto</th>
          <th scope="col">Importe</th>
        </tr>
        <!-- START BLOCK : row -->
		<tr>
          <td align="center"><input name="num_cia[]" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" />
          <input name="nombre_cia[]" type="text" disabled="disabled" class="disabled" id="nombre_cia" size="30" /></td>
          <td align="center"><input name="cod_mov[]" type="text" class="valid Focus toPosInt center" id="cod_mov" size="3" />
          <input name="descripcion[]" type="text" disabled="disabled" class="disabled" id="descripcion" size="10" />
          <input name="local[]" type="hidden" id="local" />
          <input name="fecha_renta[]" type="hidden" id="fecha_renta" />
		  <input name="es_cheque[]" type="hidden" id="es_cheque" value="FALSE" />
		  <input type="hidden" name="idreciborenta[]" id="idreciborenta" />
		  <input type="hidden" name="idarrendatario[]" id="idarrendatario" /></td>
          <td align="center"><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
          <td align="center"><input name="concepto[]" type="text" class="valid toText cleanText toUpper" id="concepto" size="30" /></td>
          <td align="center"><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" size="12" /></td>
        </tr>
		<!-- END BLOCK : row -->
		<tr>
		  <th colspan="4" align="right">Total</th>
		  <th><input name="total" type="text" disabled="disabled" class="right" id="total" value="0.00" size="12" /></th>
	    </tr>
      </table>
      <p>
        <input name="registrar" type="button" class="boton" id="registrar" value="Registrar nuevo comprobante" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>