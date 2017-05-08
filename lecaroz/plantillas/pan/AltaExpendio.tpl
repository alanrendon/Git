<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Expendios</title>

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
<script type="text/javascript" src="jscripts/pan/AltaExpendio.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Alta de Expendios </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a</th>
        <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="3" />
          <input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" /></td>
      </tr>
      <tr>
        <th align="left">No. expendio</th>
        <td class="linea_on"><input name="num_expendio" type="text" class="cap toPosInt alignCenter" id="num_expendio" size="3" /></td>
      </tr>
      <tr>
        <th align="left">No. expendio (panader&iacute;a) </th>
        <td class="linea_off"><input name="num_referencia" type="text" class="cap toPosInt alignCenter" id="num_referencia" size="3" /></td>
      </tr>
      <tr>
        <th align="left">Nombre expendio </th>
        <td class="linea_on"><input name="nombre" type="text" class="cap toText toUpper clean" id="nombre" size="45" maxlength="45" /></td>
      </tr>
      <tr>
        <th align="left">Tipo</th>
        <td class="linea_off"><select name="tipo" id="tipo">
          <!-- START BLOCK : tipo -->
		  <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : tipo -->
        </select></td>
      </tr>
      <tr>
        <th align="left">Direcci&oacute;n</th>
        <td class="linea_on"><textarea name="direccion" cols="30" rows="3" class="cap toText toUpper clean" id="direccion" style="width:98%;"></textarea></td>
      </tr>
      <tr>
        <th align="left">% de ganancia </th>
        <td class="linea_off"><input name="porciento_ganancia" type="text" class="cap numPosFormat2 alignRight" id="porciento_ganancia" value="0.00" size="5" maxlength="5"{readonly} />
        {leyenda}</td>
      </tr>
      <tr>
        <th align="left">Importe fijo </th>
        <td class="linea_on"><input name="importe_fijo" type="text" class="cap numPosFormat2 alignRight" id="importe_fijo" size="10" /></td>
      </tr>
      <tr>
        <th align="left">Total fijo </th>
        <td class="linea_off"><input name="total_fijo" type="checkbox" class="checkbox" id="total_fijo" value="1" />
          Si</td>
      </tr>
      <tr>
        <th align="left">Expendio por notas de pastel </th>
        <td class="linea_on"><input name="notas" type="checkbox" class="checkbox" id="notas" value="1" />
          Si</td>
      </tr>
      <tr>
        <th align="left">Autoriza devoluci&oacute;n </th>
        <td class="linea_off"><input name="aut_dev" type="checkbox" class="checkbox" id="aut_dev" value="1"{disabled} />
          Si{leyenda}</td>
      </tr>
      <tr>
        <th align="left">Autoriza devoluci&oacute;n total en fin de mes</th>
        <td class="linea_off"><input name="devolucion_fin_mes" type="checkbox" class="checkbox" id="devolucion_fin_mes" value="1"{disabled} />
          Si{leyenda}</td>
      </tr>
      <tr>
        <th align="left">Tipo de devoluci&oacute;n </th>
        <td class="linea_on">
          <input name="tipo_devolucion" type="radio" id="tipo_devolucion_0" value="0" checked="checked" />Porcentaje<br />
          <input name="tipo_devolucion" type="radio" id="tipo_devolucion_1" value="1" />Importe<br />
        </td>
      </tr>
      <tr>
        <th align="left"><span id="tipo_devolucion_span">Porcentaje</span> de devoluci&oacute;n m&aacute;ximo</th>
        <td class="linea_off"><input name="devolucion_maxima" type="text" class="cap numPosFormat2 alignRight" id="devolucion_maxima" size="10" /></td>
      </tr>
      <tr>
        <th align="left">Agente de ventas </th>
        <td class="linea_on"><select name="idagven" id="idagven">
          <option value=""></option>
          <!-- START BLOCK : agente -->
		  <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : agente -->
        </select>
        </td>
      </tr>
      <tr>
        <th align="left">Reparte a </th>
        <td class="linea_off"><input name="num_cia_exp" type="text" class="cap toPosInt alignCenter" id="num_cia_exp" size="3" />
          <input name="nombre_cia_exp" type="text" class="disabled" id="nombre_cia_exp" size="30" /></td>
      </tr>
      <tr>
        <th align="left">Paga renta</th>
        <td class="linea_on"><input name="paga_renta" type="radio" class="checkbox" value="FALSE" checked="checked" />
          No
          <input name="paga_renta" type="radio" class="checkbox" value="TRUE" />
          Si</td>
      </tr>
      </table>
      <p>
        <input name="alta" type="button" class="boton" id="alta" value="Alta" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
