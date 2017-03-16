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
-->
</style>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Pago de facturas por otras compa&ntilde;&iacute;as </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
	  <table class="tabla_captura">
  <tr>
    <th scope="col">Proveedor</th>
    <th scope="col">Factura</th>
    <th scope="col">Compa&ntilde;&iacute;a</th>
    <th scope="col">Banco</th>
    <th scope="col">Folio</th>
  </tr>
  <!-- START BLOCK : row -->
  <tr>
    <td><input name="num_pro[]" type="text" id="num_pro" size="3" />
      <input name="nombre_pro[]" type="text" id="nombre_pro" size="30" /></td>
    <td><input name="num_fact[]" type="text" id="num_fact" size="10" /></td>
    <td><input name="num_cia[]" type="text" id="num_cia" size="3" />
      <input name="nombre_cia[]" type="text" id="nombre_cia" size="30" /></td>
    <td><select name="cuenta[]" id="cuenta">
      <option value="1">BANORTE</option>
      <option value="2">SANTANDER</option>
    </select>
    </td>
    <td><input name="folio[]" type="text" id="folio" size="10" /></td>
  </tr>
  <!-- END BLOCK : row -->
</table>
      <p>
        <input name="siguiente" type="button" class="boton" id="siguiente" value="Siguiente" />
      <img src="imagenes/_loading.gif" name="loading" width="16" height="16" id="loading" /></p>
    </form>
	<div id="result"></div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
