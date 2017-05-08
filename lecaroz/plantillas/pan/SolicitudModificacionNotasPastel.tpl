<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Solicitud de Modificaci&oacute;n de Facturas de Pastel</title>
<link href="./smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="./smarty/styles/formularios.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script language="javascript" type="text/javascript" src="jscripts/mootools/tablas.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script language="JavaScript" type="text/javascript" src="jscripts/pan/SolicitudModificacionNotasPastel.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>
</head>

<body>
<div id="contenedor">
  <div id="titulo">Solicitud de Modificaci&oacute;n de Facturas de Pastel</div>
  <div id="captura" align="center">
    <form action="SolicitudModificacionNotasPastel.php" method="post" name="Registro" class="formulario" id="Registro">
      <table class="tabla_captura" id="Captura">
        <tr>
          <th rowspan="3" scope="col">Compa&ntilde;&iacute;a</th>
          <th rowspan="3" scope="col">Factura</th>
          <th rowspan="3" scope="col">Descripci&oacute;n</th>
          <th colspan="8" scope="col">Modificar</th>
        </tr>
        <tr>
          <th colspan="3" scope="col">General</th>
          <th rowspan="2" scope="col">Pan</th>
          <th rowspan="2" scope="col">Fecha<br />
          Factura</th>
          <th rowspan="2" scope="col">Fecha<br />
          Entrega</th>
          <th rowspan="2" scope="col">Cancelar</th>
          <th rowspan="2" scope="col">Extraviada</th>
        </tr>
        <tr>
          <th scope="col">Kilos</th>
          <th scope="col">Precio</th>
          <th scope="col">Base</th>
        </tr>
        <tr style="display:none;">
          <td><input name="num_cia_copy" type="text" class="cap toPosInt alignCenter" id="num_cia_copy" size="1" />
          <input name="nombre_copy" type="text" class="disabled" id="nombre_copy" size="30" /></td>
          <td><input name="letra_folio_copy" type="text" class="cap onlyLetters toUpper alignCenter" id="letra_folio_copy" size="1" maxlength="1" />
          <input name="num_remi_copy" type="text" class="cap toPosInt" id="num_remi_copy" size="8" /></td>
          <td><input name="descripcion_copy" type="text" class="cap onlyText toUpper clean" id="descripcion_copy" size="30" maxlength="100" /></td>
          <td align="center"><input name="kilos_mas_copy" type="checkbox" class="checkbox" id="kilos_mas_copy" value="1" />
          <span style="color:#00C;">+</span>
          <input name="kilos_menos_copy" type="checkbox" class="checkbox" id="kilos_menos_copy" value="1" />
          <span style="color:#C00;">-</span></td>
          <td align="center"><input name="precio_copy" type="checkbox" class="checkbox" id="precio_copy" value="1" /></td>
          <td align="center"><input name="base_copy" type="checkbox" class="checkbox" id="base_copy" value="1" /></td>
          <td align="center"><input name="radiobutton" type="radio" class="checkbox" value="radiobutton" /></td>
          <td align="center"><input name="radiobutton" type="radio" class="checkbox" value="radiobutton" /></td>
          <td align="center"><input name="radiobutton" type="radio" class="checkbox" value="radiobutton" /></td>
          <td align="center"><input name="radiobutton" type="radio" class="checkbox" value="radiobutton" /></td>
          <td align="center"><input name="radiobutton" type="radio" class="checkbox" value="radiobutton" /></td>
        </tr>
      </table>
      <p>
	    <input name="registrar" type="button" class="boton" id="registrar" value="Registrar Solicitudes" />
	  </p>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
