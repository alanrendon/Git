<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Extintores</title>
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
<script type="text/javascript" src="jscripts/fac/AltaExtintores.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Alta de Extintores </div>
  <div id="captura" align="center">
    <form action="AltaExtintores.php" method="post" name="Altas" class="formulario" id="Altas">
      <table class="tabla_captura" id="Datos">
        <tr>
          <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
          <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" />
          <input name="nombre" type="text" class="disabled" id="nombre" size="30" /></td>
        </tr>
        <tr>
          <th align="left" scope="row">Caducidad</th>
          <td class="linea_on"><input name="fecha_caducidad_general" type="text" class="cap toDate alignCenter" id="fecha_caducidad_general" size="10" maxlength="10" /></td>
        </tr>
        <tr>
          <th align="left" scope="row"># Extintores </th>
          <td class="linea_off"><input name="num_ext" type="text" class="cap toPosInt alignCenter" id="num_ext" size="5" /></td>
        </tr>
      </table>
      <div style="display:none;">
		  <input name="fecha_caducidad_copy" type="text" class="cap toDate alignCenter" id="fecha_caducidad_copy" size="10" maxlength="10" />
		</div>
      <p>
        <input name="alta" type="button" class="boton" id="alta" value="Alta" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
