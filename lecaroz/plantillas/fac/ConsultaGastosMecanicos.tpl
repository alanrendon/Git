<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Gastos de Mec&aacute;nicos</title>
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
<script type="text/javascript" src="jscripts/fac/ConsultaGastosMecanicos.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Consulta de Gastos de Mec&aacute;nicos </div>
  <div id="captura" align="center">
    <form action="ConsultaGastosMecanicos.php" method="get" name="Datos" target="gastos" class="formulario" id="Datos">
      <input name="accion" type="hidden" id="accion" value="getQuery" />
      <table class="tabla_captura">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="3" />
          <input name="nombre" type="text" class="disabled" id="nombre" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Administrador</th>
          <td class="linea_on"><select name="admin" id="admin">
            <option value="" selected="selected"></option>
				<option value="-1" style="font-weight:bold;">AGRUPAR POR ADMINISTRADOR</option>
				<!-- START BLOCK : admin -->
            <option value="{id}">{nombre}</option>
				<!-- END BLOCK : admin -->
            </select>
			 </td>
        </tr>
        <tr>
          <th align="left" scope="row">Periodo</th>
          <td class="linea_off"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
            al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" /></td>
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
