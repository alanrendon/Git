<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Actualizar Precios de Compra</title>

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
<script type="text/javascript" src="jscripts/ros/ActualizarPreciosCompra.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Actualizar Precios de Compra </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Omitir compa&ntilde;&iacute;a(s)</th>
        <td class="linea_on"><input name="omitir" type="text" class="cap toInterval" id="omitir" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Proveedor(es)</th>
        <td class="linea_off"><input name="pros" type="text" class="cap toInterval" id="pros" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Producto</th>
        <td class="linea_on"><input name="codmp" type="text" class="cap toPosInt alignCenter" id="codmp" size="1" />
          <input name="nombre_mp" type="text" class="disabled" id="nombre_mp" size="30" /></td>
      </tr>
      <tr>
        <th align="left">Precio de Compra </th>
        <td class="linea_off"><input name="precio_compra" type="text" class="cap numPosFormat2" id="precio_compra" size="10" /></td>
      </tr>
    </table>
      <p>
        <input name="actualizar" type="button" class="boton" id="actualizar" value="Actualizar Precios" />
        <img src="../../imagenes/_loading.gif" name="loading" width="16" height="16" id="loading" /></p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
