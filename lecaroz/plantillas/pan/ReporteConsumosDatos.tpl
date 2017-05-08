<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reporte de Consumos</title>

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
<script type="text/javascript" src="jscripts/pan/ReporteConsumos.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Reporte de Consumos </div>
  <div id="captura" align="center">
    <form action="ReporteConsumos.php?accion=getReporte" method="post" name="Datos" target="reporte" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr>
        <th align="left">Compa&ntilde;&iacute;a</th>
        <td class="linea_off"><input name="num_cia" type="text" class="cap toPosInt alignCenter" id="num_cia" size="1" />
          <input name="nombre_cia" type="text" class="disabled" id="nombre_cia" size="30" /></td>
      </tr>
      <tr>
        <th align="left">Administrador</th>
        <td class="linea_off"><select name="admin" id="admin">
          <option value="" selected="selected"></option>
		  <!-- START BLOCK : admin -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : admin -->
        </select>
        </td>
      </tr>
      <!-- <tr>
        <th align="left">A&ntilde;o</th>
        <td class="linea_on"><input name="anio" type="text" class="cap toPosInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr>
        <th align="left">Mes</th>
        <td class="linea_off"><select name="mes" id="mes">
          <option value="1"{1}>ENERO</option>
          <option value="2"{2} style="background-color:#EEE;">FEBRERO</option>
          <option value="3"{3}>MARZO</option>
          <option value="4"{4} style="background-color:#EEE;">ABRIL</option>
          <option value="5"{5}>MAYO</option>
          <option value="6"{6} style="background-color:#EEE;">JUNIO</option>
          <option value="7"{7}>JULIO</option>
          <option value="8"{8} style="background-color:#EEE;">AGOSTO</option>
          <option value="9"{9}>SEPTIEMBRE</option>
          <option value="10"{10} style="background-color:#EEE;">OCTUBRE</option>
          <option value="11"{11}>NOVIEMBRE</option>
          <option value="12"{12} style="background-color:#EEE;">DICIEMBRE</option>
        </select>        </td>
      </tr> -->
      <tr>
        <th align="left">Fecha de corte</th>
        <td class="linea_on"><input name="fecha" type="text" class="cap toDate alignCenter" id="fecha" value="{fecha}" size="10" maxlength="10" /></td>
      </tr>
      <tr class="linea_on">
      	<th align="left">Incluir diferencias</th>
      	<td><input name="diferencias" type="checkbox" class="checkbox" id="diferencias" value="1" checked="checked" /></td>
      	</tr>
      <tr class="linea_off">
      	<th align="left">Mes terminado</th>
      	<td class="linea_off"><input name="terminado" type="checkbox" class="checkbox" id="terminado" value="1" checked="checked" /></td>
      	</tr>
    </table>
      <p>
        <input name="consultar" type="button" class="boton" id="consultar" value="Consultar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
