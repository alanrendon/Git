<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css">
<link href="../../styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Listados para Aguinaldos</p>
  <form action="./fac_tra_lis_agu.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;ia</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Puesto</th>
      <td class="vtabla"><select name="cod_puestos" class="insert" id="cod_puestos">
        <option value="" selected>-</option>
		<!-- START BLOCK : puesto -->
		<option value="{cod_puestos}">{cod_puestos} {descripcion}</option>
		<!-- END BLOCK : puesto -->
      </select></td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Turno</th>
      <td class="vtabla"><select name="cod_turno" class="insert" id="cod_turno">
        <option value="" selected>-</option>
		<!-- START BLOCK : turno -->
		<option value="{cod_turno}">{cod_turno} {descripcion}</option>
		<!-- END BLOCK : turno -->
      </select></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Columnas<br>
        Adicionales</th>
      <td class="vtabla"><input name="agu_ant" type="checkbox" id="agu_ant" value="1">
        Aguinaldo Anterior<br>
        <input name="agu_act" type="checkbox" id="agu_act" value="1">
        Aguinaldo Actual <br>
        <input name="status" type="checkbox" id="status" value="1">
        Estatus Aguinaldo<br>
        <input name="notes" type="checkbox" id="notes" value="1">
        Anotaciones</td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Criterios de<br> 
        Ordenaci&oacute;n </th>
      <td class="vtabla">1. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="cod_puestos">Puesto</option>
          <option value="cod_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        2. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="cod_puestos">Puesto</option>
          <option value="cod_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        3. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="cod_puestos">Puesto</option>
          <option value="cod_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        4. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="cod_puestos">Puesto</option>
          <option value="cod_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		window.open("","listado","toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=1024,height=768");
		form.target = "listado";
		form.submit();
	}
</script>
</body>
</html>
