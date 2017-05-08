<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Memorandums</title>

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
<script type="text/javascript" src="jscripts/ban/ban_con_mem.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Consulta de Memorandums </div>
  <div id="captura" align="center">
    <form method="post" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a</th>
        <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" />
          <input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" /></td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_on"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
		  <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
          </select>
		</td>
      </tr>
      <tr>
        <th align="left">Folio</th>
        <td class="linea_off"><input name="folio" type="text" class="cap toPosInt alignCenter" id="folio" size="5" /></td>
      </tr>
      <tr>
        <th align="left">Periodo</th>
        <td class="linea_on"><input name="fecha1" type="text" class="cap" id="fecha1" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap" id="fecha2" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <th align="left">Incluir aclarados </th>
        <td class="linea_off"><input name="aclarados" type="checkbox" class="checkbox" id="aclarados" value="1" />
          Si</td>
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