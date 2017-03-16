<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Resultado de Reporte Anual</title>

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
<script type="text/javascript" src="jscripts/bal/ResultadoReporteAnual.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Resultado de Reporte Anual </div>
  <div id="captura" align="center">
    <form action="ResultadoReporteAnual.php?accion=reporte" method="post" name="Datos" target="reporte" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;as</th>
        <td class="linea_off">
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />&nbsp;
		  <input name="num_cia[]" type="text" class="cap toInt alignCenter" id="num_cia" size="3" />		</td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_on"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
		  <option value="-1" style="font-weight:bold;border-bottom:solid 1px #000;">ORDERNAR POR ADMINISTRADOR</option>
		  <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
        </select>        </td>
      </tr>
      <tr>
        <th align="left">A&ntilde;os</th>
        <td class="linea_off">
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />&nbsp;
		  <input name="anio[]" type="text" class="cap toInt alignCenter" id="anio" size="4" maxlength="4" />		</td>
      </tr>
      <tr>
        <th align="left">Campo</th>
        <td class="linea_off"><select name="campo" id="campo">
          <option value="utilidad_neta" selected="selected">Utilidad</option>
          <option value="ventas_netas">Ventas</option>
          <option value="produccion_total">Producci&oacute;n</option>
		  <option value="clientes">Clientes</option>
		  <option value="pastel_kilos">Kilos de pastel</option>
		  <option value="bultos">Bultos de harina</option>
		  <option value="mat_prima_utilizada">Materia prima utilizada</option>
		  <option value="mp_pro">% Materia Prima / Producci&oacute;n</option>
		  <option value="sueldo_empleados">Sueldo empleados</option>
		  <option value="encargados">Encardos</option>
		  <option value="intereses-impuestos">Intereses - Impuestos</option>
		  <option value="ide">IDE 3%</option>
        </select>
        </td>
      </tr>
      <tr>
      	<th align="left">Banco</th>
      	<td class="linea_off"><select name="cuenta" id="cuenta">
      		<option value="" selected="selected"></option>
      		<option value="1">BANORTE</option>
      		<option value="2">SANTANDER</option>
      		</select></td>
      	</tr>
      <tr>
        <th align="left">Ordenado por </th>
        <td class="linea_on"><input name="tipo" type="radio" class="checkbox" value="1" checked="checked" />
          A&ntilde;o (horizontal)<br />
          <input name="tipo" type="radio" class="checkbox" value="2" />
          Compa&ntilde;&iacute;a (vertical) </td>
      </tr>
    </table>
      <p>
        <input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
      &nbsp;&nbsp;
      <input name="exportar" type="button" class="boton" id="exportar" value="Exportar a Excel" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
