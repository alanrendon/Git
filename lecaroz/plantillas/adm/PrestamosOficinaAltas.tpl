<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Alta de Prestamos de Oficina</title>

<link href="../../smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="../../smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="../../styles/FormStyles.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/layout.css" rel="stylesheet" type="text/css" />
<link href="smarty/styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="styles/font-style.css" rel="stylesheet" type="text/css" />
<link href="styles/FormValidator.css" rel="stylesheet" type="text/css" />
<link href="styles/FormStyles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="jscripts/mootools/mootools-1.2-core.js"></script>
<script type="text/javascript" src="jscripts/mootools/mootools-1.2-more.js"></script>
<script type="text/javascript" src="jscripts/mootools/String.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/Number.implement.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormValidator.js"></script>
<script type="text/javascript" src="jscripts/mootools/FormStyles.js"></script>
<script type="text/javascript" src="jscripts/adm/PrestamosOficinaAltas.js"></script>
<script language="JavaScript" type="text/javascript" src="menus/stm31.js"></script>

<style type="text/css">
select {
	min-width: 300px;
	width: 100%;
}
</style>

</head>

<body>
<div id="contenedor">
  <div id="titulo"> Alta de Prestamos de Oficina</div>
  <div id="captura" align="center">
    <form name="Datos" class="FormValidator FormStyles" id="Datos">
      <table class="tabla_captura" id="TablaCaptura">
        <tr>
          <th scope="col">Compa&ntilde;&iacute;a</th>
          <th scope="col">Empleado</th>
          <th scope="col">Fecha</th>
          <th scope="col">Importe</th>
        </tr>
        <tr>
          <td><input name="num_cia[]" type="text" class="valid Focus toPosInt center" id="num_cia" size="3" /><input name="nombre_cia[]" type="text" disabled="disabled" id="nombre_cia" size="30" /></td>
          <td><select name="empleado[]" id="empleado">
            <option value=""></option>
          </select>
          </td>
          <td><input name="fecha[]" type="text" class="valid Focus toDate center" id="fecha" size="10" maxlength="10" /></td>
          <td><input name="importe[]" type="text" class="valid Focus numberPosFormat right" precision="2" id="importe" size="10" /></td>
        </tr>
      </table>
      <br />
      <p>
        <input name="registrar" type="button" class="boton" id="registrar" value="Registrar" />
      </p>
    </form>
  </div>
</div>
<script language="javascript" type="text/javascript" src="menus/{menucnt}"></script>
</body>
</html>