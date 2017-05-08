<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Balances de Zapater&iacute;as</title>

<link href="/lecaroz/smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="/lecaroz/smarty/styles/formularios.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/extensiones.js"></script>
<script type="text/javascript" src="jscripts/mootools/formularios.js"></script>
<script type="text/javascript" src="jscripts/bal/ImprimirBalancesZapateriasAnual.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css" media="screen">
.Tip {
	background: #FF9;
	border: solid 1px #000;
	padding: 3px 5px;
}

.tip-title {
	font-weight: bold;
	font-size: 8pt;
	border-bottom: solid 2px #FC0;
	padding: 0 5px 3px 5px;
	margin-bottom: 3px;
}

.tip-text {
	font-weight: bold;
	font-size: 8pt;
	padding: 0 5px;
}
</style>

</head>

<body>
<!-- START BLOCK : normal -->
<div id="contenedor">
  <div id="titulo"> Balances de Zapater&iacute;as Anual</div>
  <div id="captura" align="center">
    <form action="" method="get" name="Datos" class="formulario" id="Datos">
    <table class="tabla_captura">
      <tr class="linea_off">
        <th align="left" scope="row">Compa&ntilde;&iacute;a(s)<span style="float:right;"><img src="imagenes/question.png" name="info" width="16" height="16" id="info" /></span></th>
        <td><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
      <tr class="linea_on">
        <th align="left" scope="row">A&ntilde;o</th>
        <td><input name="anyo" type="text" class="cap toInt alignCenter" id="anyo" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr class="linea_off">
      	<th align="left" scope="row">Ingresos Extraordinarios </th>
      	<td><input name="no_ing" type="checkbox" class="checkbox" id="no_ing" value="1" />
      		No incluir en balances </td>
      	</tr>
    </table>
    <p>
    	<input name="imprimir" type="button" class="boton" id="imprimir" value="Imprimir"{disabled_imprimir} />
    </p>
	</form>
  </div>
</div>
<!-- END BLOCK : normal -->
<!-- START BLOCK : ipad -->
<div id="contenedor">
  <div id="titulo"> Balances de Zapater&iacute;as Anual</div>
  <div id="captura" align="center">
    <form action="" method="get" name="Datos" class="formulario" id="Datos">
    <table class="tabla_captura">
      <tr>
        <th align="left" scope="row">Compa&ntilde;&iacute;a</th>
        <td class="linea_off"><select name="cias" id="cias">
		  <option value=""></option>
          <!-- START BLOCK : cia -->
		  <option value="{num_cia}">{num_cia} {nombre_cia}</option>
		  <!-- END BLOCK : cia -->
        </select>        </td>
      </tr>
      <tr>
        <th align="left" scope="row">A&ntilde;o</th>
        <td class="linea_off"><input name="anyo" type="text" class="cap toInt alignCenter" id="anyo" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
    </table>
    <p>
      <input name="consultar" type="button" class="boton" id="consultar" value="Consultar"{disabled_consultar} />
    </p>
	</form>
  </div>
</div>
<!-- END BLOCK : ipad -->
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>