<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Errores en Clientes</title>

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
<script type="text/javascript" src="jscripts/adm/ErroresClientes.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Errores en Clientes </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
      <tr>
        <th align="left">Operadora</th>
        <td class="linea_on"><select name="op" id="op">
          <option value="" selected="selected"></option>
		  <!-- START BLOCK : op -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : op -->
        </select>        </td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_off"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
          <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
          <!-- END BLOCK : admin -->
        </select></td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_on"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" size="10" maxlength="10" /></td>
      </tr>
    </table>
      <p>
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar" />
      </p>
    </form>
	<div id="result">
	</div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
