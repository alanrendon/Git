<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consulta de Extintores</title>
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
<script type="text/javascript" src="jscripts/fac/ConsultaExtintores.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo"> Consulta de Extintores </div>
  <div id="captura" align="center">
    <form action="ConsultaExtintores.php" method="post" name="Datos" class="formulario" id="Datos">
      <div style="display:none;">
		  <input name="fecha_copy" type="text" class="cap toDate alignCenter" id="fecha_copy" size="10" />
		</div>
		<table class="tabla_captura">
        <tr>
          <th scope="row">Compa&ntilde;&iacute;a</th>
          <td><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" />
          <input name="nombre" type="text" class="disabled" id="nombre" /></td>
        </tr>
      </table>
      <p>
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar" />
      </p>
    </form>
  </div>
  <div align="center" id="Resultado"></div>
</div>
</body>
</html>
