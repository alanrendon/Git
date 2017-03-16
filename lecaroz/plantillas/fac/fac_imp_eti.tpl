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
<td align="center" valign="middle"><p class="title">Impresi&oacute;n de Etiquetas</p>
  <form action="./fac_imp_eti.php" method="get" name="form"><table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;as</th>
      <td class="vtabla"><input name="num_cia1" type="text" class="insert" id="num_cia1" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia2.select()" size="3" maxlength="3">
        a
          <input name="num_cia2" type="text" class="insert" id="num_cia2" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) etiqueta.select()" size="3" maxlength="3"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Desde etiqueta </th>
      <td class="vtabla"><input name="etiqueta" type="text" class="insert" id="etiqueta" onFocus="temp.value=this.value" onChange="isInt(this,temp)" onKeyDown="if (event.keyCode == 13) num_cia1.select()" size="2" maxlength="2"></td>
    </tr>
    <tr>
      <th class="vtabla" scope="row">Tipo</th>
      <td class="vtabla"><input name="tipo" type="radio" value="1" checked>
        Compa&ntilde;&iacute;as<br>
        <input name="tipo" type="radio" value="2">
        Empleados</td>
    </tr>
	<tr>
      <th class="vtabla" scope="row">Criterios de<br>
        Ordenaci&oacute;n </th>
      <td class="vtabla">1. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
		  <option value="cia_aguinaldos">Compa&ntilde;&iacute;a Mancomunada</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        2. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        3. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select>
        <br>
        4. Por
        <select name="criterio_orden[]" class="insert" id="criterio_orden">
          <option value="" selected>-</option>
		  <option value="num_cia">Compa&ntilde;&iacute;a</option>
          <option value="catalogo_puestos.sueldo DESC, cod_puestos">Puesto</option>
          <option value="catalogo_turnos.orden_turno">Turno</option>
          <option value="ap_paterno, ap_materno, catalogo_trabajadores.nombre">Nombre empleado</option>
          <option value="num_emp">N&uacute;mero de empleado</option>
        </select></td>
    </tr>
    <tr>
        <th class="vtabla">Tipo aguinaldo</th>
        <td class="vtabla">
          <input name="tipo_aguinaldo" type="radio" id="tipo_aguinaldo_0" value="0" checked="checked" /> Todos<br />
          <input name="tipo_aguinaldo" type="radio" id="tipo_aguinaldo_1" value="1" /> Normales<br />
          <input name="tipo_aguinaldo" type="radio" id="tipo_aguinaldo_2" value="2" /> Con insidencias
        </td>
    </tr>
  </table>
    <p style="font-family:Arial, Helvetica, sans-serif; font-weight:bold; color:#CC0000;">Nota importante: solo se imprime en etiquetas modelo #5261 </p
    ><p>
    <input type="button" class="boton" value="Imprimir" onClick="validar(this.form)">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function validar(form) {
		if (form.etiqueta.value < 0 || form.etiqueta.value > 20) {
			alert("EL valor de etiqueta debe de estar entre 1 y 20");
			form.etiqueta.select();
			return false;
		}
		else {
			form.submit();
		}
	}

	window.onload = document.form.num_cia1.select();
</script>
</body>
</html>
