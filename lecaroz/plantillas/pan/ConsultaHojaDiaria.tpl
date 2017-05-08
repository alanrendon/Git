<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Hoja Diaria</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="../../styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="../../styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="styles/Tips.css" rel="stylesheet" type="text/css" />
<link href="styles/calendar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/pan/ConsultaHojaDiaria.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Consulta de Hoja Diaria </div>
  <!-- START BLOCK : normal -->
  <div id="captura" align="center">
    <form  name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
      <tr class="linea_off">
        <th align="left" scope="row">Compa&ntilde;&iacute;a(s)<span style="float:right;"><img src="imagenes/question.png" name="help1" width="16" height="16" id="help1" /></span></th>
        <td><input name="cias" type="text" class="valid Focus toInterval" id="cias" size="30" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">Administrador</th>
        <td><select name="admin" id="admin">
          <option value=""></option>
          <!-- START BLOCK : admin -->
		  <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
        </select>
        </td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Periodo <span style="float:right;"><img src="imagenes/question.png" name="help2" width="16" height="16" id="help2" /></span></th>
        <td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
      </tr>
    </table>
	<p>
	  <input name="consultar" type="button" id="consultar" value="Consultar" />
	</p>
    </form>
  </div>
  <!-- END BLOCK : normal -->
  <!-- START BLOCK : ipad -->
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
	<table class="tabla_captura">
      <tr class="linea_off">
        <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
        <td><select name="cias" id="cias">
		  <option value=""></option>
		  <!-- START BLOCK : cia -->
          <option value="{num_cia}">{num_cia} {nombre_cia}</option>
		  <!-- END BLOCK : cia -->
        </select>        </td>
      </tr>
	  <tr class="linea_on">
        <th align="left" scope="row">Administrador</th>
        <td><select name="admin" id="admin">
          <option value=""></option>
          <!-- START BLOCK : admin_ipad -->
		  <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin_ipad -->
        </select>
        </td>
      </tr>
      <tr class="linea_off">
        <th align="left" scope="row">Periodo</th>
        <td><input name="fecha1" type="text" class="valid Focus toDate center" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
        al
          <input name="fecha2" type="text" class="valid Focus toDate center" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
      </tr>
    </table>
	<p>
	  <input name="consultar" type="button" id="consultar" value="Consultar" />
	</p>
	</form>
  </div>
  <!-- END BLOCK : ipad -->
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>