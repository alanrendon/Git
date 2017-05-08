<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Archivo de Cheques para Contadores</title>

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
<script type="text/javascript" src="jscripts/ban/ArchivoChequesContadores.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo">Archivo de Cheques para Contadores </div>
  <div id="captura" align="center">
    <form name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
      <tr class="linea_off">
        <th align="left">Compa&ntilde;&iacute;a(s)</th>
        <td class="linea_off"><input name="cias" type="text" class="cap toInterval" id="cias" size="40" /></td>
      </tr>
	  <tr class="linea_on">
        <th align="left">Contador</th>
        <td><select name="contador" id="contador">
		  <option value="" selected="selected"></option>
          <!-- START BLOCK : contador -->
          <option value="{id}">{nombre}</option>
		  <!-- END BLOCK : contador -->
        </select>        </td>
      </tr>
	  <tr class="linea_off">
        <th align="left">Auditor</th>
        <td><select name="auditor" id="auditor">
          <option value="" selected="selected"></option>
          <!-- START BLOCK : auditor -->
          <option value="{id}">{nombre}</option>
          <!-- END BLOCK : auditor -->
        </select></td>
      </tr>
      <tr class="linea_on">
        <th align="left">A&ntilde;o</th>
        <td><input name="anio" type="text" class="cap toInt alignCenter" id="anio" value="{anio}" size="4" maxlength="4" /></td>
      </tr>
      <tr class="linea_off">
        <th align="left">Mes</th>
        <td><select name="mes" id="mes">
          <option value="1"{1}>ENERO</option>
          <option value="2"{2}>FEBRERO</option>
          <option value="3"{3}>MARZO</option>
          <option value="4"{4}>ABRIL</option>
          <option value="5"{5}>MAYO</option>
          <option value="6"{6}>JUNIO</option>
          <option value="7"{7}>JULIO</option>
          <option value="8"{8}>AGOSTO</option>
          <option value="9"{9}>SEPTIEMBRE</option>
          <option value="10"{10}>OCTUBRE</option>
          <option value="11"{11}>NOVIEMBRE</option>
          <option value="12"{12}>DICIEMBRE</option>
        </select>        </td>
      </tr>
      <tr class="linea_on">
        <th align="left">Banco</th>
        <td><select name="banco" id="banco">
          <option value=""></option>
          <option value="1">BANORTE</option>
          <option value="2">SANTANDER</option>
        </select>        </td>
      </tr>
      <tr class="linea_off">
        <th align="left">Incluir</th>
        <td><input name="pagados" type="checkbox" class="checkbox" id="pagados" value="1" checked="checked" />
          Pagados<br />
          <input name="cancelados" type="checkbox" class="checkbox" id="cancelados" value="1" checked="checked" />
          Cancelados</td>
      </tr>
    </table>
      <p>
        <input name="generar" type="button" class="boton" id="generar" value="Generar Archivo" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>
