<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Aclaraci&oacute;n de Facturas de Proveedores</title>

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
<script type="text/javascript" src="jscripts/ban/AclaracionFacturasProveedores.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css">
<!--
#proveedor {
	padding: 6px 6px;
	background-color: #e1f0f0;
	margin: 6px 8px;
	border: medium solid #000;
}

#proveedor #nombre_pro {
	padding: 4px 4px;
	margin-bottom: 15px;
	font-size: 12pt;
	font-weight: bold;
	border: thin solid #000;
	background-color: #73a8b7;
	text-align: left;
}

#proveedor #facturas {
	display: none;
}

#proveedor #factura {
	padding: 4px 4px;
	margin-bottom: 15px;
	border: medium solid #000;
	background-color: #e1f0f0;
}

#proveedor #factura #detalle {
	display: none;
}

#proveedor #factura table {
	border-spacing: 0 4px;
	border: 1px solid #000;
	margin-top: 4px;
	empty-cells: show;
}

#proveedor #factura th {
	border-right: 2px groove;
	background-color: #73a8b7;
}

#proveedor #factura td {
	border-right: 2px groove;
	background-color: #ecfffc;
}

#proveedor #factura #bloque_comentarios {
	text-align: left;
	margin: 5px 12px;
}
-->
</style>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Aclaraci&oacute;n de Facturas de Proveedores </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Proveedor(es)</th>
        <td class="linea_on"><input name="pros" type="text" class="cap toInterval" id="pros" size="50" /></td>
      </tr>
      <tr>
        <th align="left">Periodo de solicitud </th>
        <td class="linea_off"><input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" size="10" maxlength="10" /></td>
      </tr>
      <tr>
        <th align="left">Estatus</th>
        <td class="linea_on"><input name="pendientes" type="checkbox" class="checkbox" id="pendientes" value="1" checked="checked" />
          Pendientes<br />
          <input name="aclaradas" type="checkbox" class="checkbox" id="aclaradas" value="1" />
          Aclaradas</td>
      </tr>
    </table>
      <p>
        <input name="nuevo" type="button" class="boton" id="nuevo" value="Nueva B&uacute;squeda" />
        &nbsp;&nbsp;&nbsp;
        <input name="buscar" type="button" class="boton" id="buscar" value="Buscar Facturas" />
        <img src="imagenes/_loading.gif" name="loading" width="16" height="16" id="loading" /></p>
    </form>
	<div id="result"></div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
