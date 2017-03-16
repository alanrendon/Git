<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Facturas Electr&oacute;nicas de Oficinas y Talleres </title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="../../styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="styles/calendar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/fac/FacturasOficinasTalleres.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Facturas Electr&oacute;nicas de Oficinas y Talleres </div>
  <div id="captura" align="center">
    <form action="" method="post" name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
      <tr class="linea_off">
        <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
        <td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Anio</th>
        <td><input name="anio" type="text" class="valid Focus toInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Mes</th>
        <td><select name="mes" id="mes">
          <option value="1"{1}>ENERO</option>
          <option value="2"{2}>FEBRERO</option>
          <option value="3"{3}>MARZO</option>
          <option value="4"{4}>ABRIL</option>
          <option value="5"{5}>MAYO</option>
          <option value="6"{6}>JUNIO</option>
          <option value="7"{7}>JULIO</option>
          <option value="8"{8}>AGOSTO</option>
          <option value="9"{9}>SEPTIEMBRE</option>
          <option value="10"{10}>OCTUBRE</option>
          <option value="11"{11}>NOVIEMBRE</option>
          <option value="12"{12}>DICIEMBRE</option>
        </select>
        </td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Emisor(es)</th>
        <td><input name="emisor[]" type="checkbox" id="emisor" value="&#123;&quot;emisor&quot;:700,&quot;tipo&quot;:&quot;oficinas&quot;&#125;" checked="checked" />
          Oficinas<br />
          <input name="emisor[]" type="checkbox" id="emisor" value="&#123;&quot;emisor&quot;:800,&quot;tipo&quot;:&quot;talleres&quot;&#125;" checked="checked" />
          Talleres<br />
		  <input name="emisor[]" type="checkbox" id="emisor" value="&#123;&quot;emisor&quot;:700,&quot;tipo&quot;:&quot;capacitacion&quot;&#125;" />
          Centro de capacitaci&oacute;n</td>
      </tr>
    </table>
      <p>
        <input name="generar" type="button" id="generar" value="Generar Facturas Electr&oacute;nicas" />
      </p>
    </form>
	<div id="resultado">
    </div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>