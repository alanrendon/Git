<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Dep&oacute;sitos de Faltantes</title>

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
<script type="text/javascript" src="jscripts/cometra/DepositosFaltantesCometra.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> 
    <p>Dep&oacute;sitos de Faltantes </p>
  </div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Banco</th>
          <td align="center" class="bold center font12">{nombre_banco}</td>
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
          <th scope="col">Fecha</th>
          <th scope="col">Importe</th>
          <th scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" /></th>
        </tr>
        <!-- START BLOCK : row -->
		<tr>
          <td>{num_cia} {nombre_cia} </td>
          <td align="center">{fecha}</td>
          <td align="right">{importe}</td>
          <td><input name="id[]" type="checkbox" id="id" value="{id}" checked="checked" importe="{importe}" /></td>
        </tr>
		<!-- END BLOCK : row -->
        <tr>
          <th colspan="2" align="right">Total</th>
          <th><input name="total" type="text" disabled="disabled" class="right" id="total" value="{total}" size="10" /></th>
          <th>&nbsp;</th>
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