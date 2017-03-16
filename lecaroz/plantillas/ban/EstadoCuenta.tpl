<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Estado de Cuenta</title>

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
<script type="text/javascript" src="jscripts/ban/EstadoCuenta.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css">
#banco {
	padding-left: 20px;
	background-repeat: no-repeat;
}

.option {
	padding: 2px 0px 2px 20px;
	margin: 0px 0px 0px 10px;
	background-repeat: no-repeat;
}

.icon_ {
}

.icon_1 {
	background-image: url(imagenes/Banorte16x16.png);
}

.icon_2 {
	background-image: url(imagenes/Santander16x16.png);
}
</style>

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
<div id="contenedor">
  <div id="titulo"> Estado de Cuenta </div>
  <div id="captura" align="center">
    <form action="" method="get" name="Datos" class="formulario" id="Datos">
    <table class="tabla_captura">
      <tr>
        <th align="left" scope="row">Compa&ntilde;&iacute;a(s)<span style="float:right;"><img src="imagenes/question.png" name="help1" width="16" height="16" id="help1" /></span></th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
      <tr>
        <th align="left" scope="row">Banco</th>
        <td class="linea_on">
		  <select name="banco" id="banco">
            <option value="" class="option icon_" selected="selected"></option>
            <option value="1" class="option icon_1">BANORTE</option>
            <option value="2" class="option icon_2">SANTANDER</option>
          </select>		</td>
      </tr>
      <tr>
        <th align="left" scope="row">Periodo<span style="float:right;"><img src="imagenes/question.png" name="help2" width="16" height="16" id="help2" /></span></th>
        <td class="linea_off">
		  <input name="fecha1" type="text" class="cap toDate alignCenter" id="fecha1" value="{fecha1}" size="10" maxlength="10" />
          al
          <input name="fecha2" type="text" class="cap toDate alignCenter" id="fecha2" value="{fecha2}" size="10" maxlength="10" />		</td>
      </tr>
      <tr>
      	<th align="left" scope="row">Conciliado<span style="float:right;"><img src="imagenes/question.png" name="help2" width="16" height="16" id="help2" /></span></th>
      	<td class="linea_on"><input name="fecha_con1" type="text" class="cap toDate alignCenter" id="fecha_con1" value="" size="10" maxlength="10" />
          al
          <input name="fecha_con2" type="text" class="cap toDate alignCenter" id="fecha_con2" value="" size="10" maxlength="10" /></td>
      	</tr>
      <tr>
        <th rowspan="2" align="left" scope="row">Opciones</th>
        <td class="linea_off">
		  <input name="abonos" type="checkbox" class="checkbox" id="abonos" value="1" checked="checked" />
          Abonos<br />
          <input name="cargos" type="checkbox" class="checkbox" id="cargos" value="1" checked="checked" />
          Cargos		</td>
      </tr>
      <tr>
        <td class="linea_off">
		  <input name="pendientes" type="checkbox" class="checkbox" id="pendientes" value="1" checked="checked" />
          Pendientes<br />
          <input name="conciliados" type="checkbox" class="checkbox" id="conciliados" value="1" checked="checked" />
          Conciliados		</td>
      </tr>
      <tr>
        <th align="left" scope="row">Folio(s)<span style="float:right;"><img src="imagenes/question.png" name="help3" width="16" height="16" id="help3" /></span></th>
        <td class="linea_off">
          <input name="folios" type="text" class="cap toInterval" id="folios" size="40" />
        </td>
      </tr>
      <tr>
        <th align="left" scope="row">Importe(s)<span style="float:right;"><img src="imagenes/question.png" name="help4" width="16" height="16" id="help4" /></span></th>
        <td class="linea_on">
          <input name="importes" type="text" class="cap toInterval" id="importes" size="40" />
        </td>
      </tr>
      <tr>
        <th align="left" scope="row">Concepto(s)<span style="float:right;"><img src="imagenes/question.png" name="help5" width="16" height="16" id="help5" /></span></th>
        <td class="linea_off">
		  <select name="codigos[]" size="10" multiple="multiple" id="codigos" style="width:98%;">
          </select>		</td>
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