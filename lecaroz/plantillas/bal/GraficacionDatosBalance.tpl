<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Graficaci&oacute;n de Datos de Balance</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="../../styles/calendar.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="styles/calendar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/mootools/Calendar.js"></script>
<script type="text/javascript" src="jscripts/bal/GraficacionDatosBalance.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Graficaci&oacute;n de Datos de Balance </div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura">
        <tr class="linea_off">
          <th align="left" scope="row">Compa&ntilde;&iacute;a(s)</th>
          <td><input name="cias" type="text" class="valid toInterval" id="cias" size="30" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Administrador</th>
          <td><select name="admin" id="admin">
            <option value=""></option>
			<!-- START BLOCK : admin -->
            <option value="{id}">{nombre}</option>
			<!-- END BLOCK : admin -->
          </select>          </td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">A&ntilde;o(s)</th>
          <td><input name="anios" type="text" class="valid toInterval" id="anios" value="{anio}" size="30" /></td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Dato de balance </th>
          <td><select name="campo" id="campo">
            <option value="venta_puerta">Venta en Puerta</option>
            <option value="bases">Bases</option>
            <option value="barredura">Barredura</option>
            <option value="pastillaje">Pastillaje</option>
            <option value="abono_emp">Abono Empleados</option>
            <option value="otros">Otros</option>
            <option value="total_otros">Total Otros</option>
            <option value="abono_reparto">Abono Reparto</option>
            <option value="errores">Errores</option>
            <option value="ventas_netas" style="font-weight:bold;">Ventas Netas</option>
            <option value="inv_ant">Inventario Anterior</option>
            <option value="compras">Compras</option>
            <option value="mercancias">Mercancias</option>
            <option value="inv_act">Inventario Actual</option>
            <option value="mat_prima_utilizada">Mat. Prima Utilizada</option>
            <option value="mano_obra">Mano de Obra</option>
            <option value="panaderos">Panaderos</option>
            <option value="gastos_fab">Gastos de Fabricaci&oacute;n</option>
            <option value="costo_produccion" style="font-weight:bold;">Costo de Producci&oacute;n</option>
            <option value="utilidad_bruta" style="font-weight:bold;">Utilidad Bruta</option>
            <option value="pan_comprado">Pan Comprado</option>
            <option value="gastos_generales">Gastos Generales</option>
            <option value="gastos_caja">Gastos de Caja</option>
            <option value="reserva_aguinaldos">Reserva para Aguinaldos</option>
            <option value="gastos_otras_cias">Gastos Pagados por Otras Compa&ntilde;&iacute;as</option>
            <option value="total_gastos" style="font-weight:bold;">Total de Gastos</option>
            <option value="ingresos_ext" style="font-weight:bold;">Ingresos Extraordinarios</option>
            <option value="utilidad_neta" style="font-weight:bold;">Utilidad del Mes</option>
            <option value="produccion_total" style="font-weight:bold;">Producci&oacute;n Total</option>
            <option value="faltante_pan" style="font-weight:bold;">Faltante de Pan</option>
            <option value="rezago_ini" style="font-weight:bold;">Rezago Inicial</option>
            <option value="rezago_fin" style="font-weight:bold;">Rezago Final</option>
            <option value="efectivo" style="font-weight:bold;">Efectivo</option>
          </select>          </td>
        </tr>
        <tr class="linea_on">
          <th align="left" scope="row">Tipo</th>
          <td><input name="tipo" type="radio" value="lineal" checked="checked" />
            Lineal<br />
            <input name="tipo" type="radio" value="barras" />
            Barras<br />
            <input name="tipo" type="radio" value="circular" />
            Circular</td>
        </tr>
      </table>
      <br />
      <p>
        <input name="generar" type="button" class="boton" id="generar" value="Generar Reporte" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>