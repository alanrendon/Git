<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Autorizaci&oacute;n de Balances</title>

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
<script type="text/javascript" src="jscripts/adm/AutorizacionBalances.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Autorizaci&oacute;n de Balances </div>
  <div id="captura" align="center">
    <form action="" method="get" name="Datos" class="formulario" id="Datos">
      <table class="tabla_captura">
        <tr>
          <th scope="col">Usuario</th>
          <th scope="col">Privilegios</th>
        </tr>
        <!-- START BLOCK : usuario -->
		<tr class="linea_{color}">
          <td>{usuario}</td>
          <td><select name="nivel[]" id="nivel">
            <option value="{iduser}|2" style="color:#00C;"{2}>GENERAR E IMPRIMIR</option>
            <option value="{iduser}|1" style="color:#060;"{1}>IMPRIMIR</option>
            <option value="{iduser}|0" style="color:#C00;"{0}>SIN PRIVILEGIOS</option>
          </select>
          </td>
        </tr>
		<!-- END BLOCK : usuario -->
      </table>
	</form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>