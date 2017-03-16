<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/tablas.css" rel="stylesheet" type="text/css">
<link href="file:///C|/Documents%20and%20Settings/John%20Talbain/Escritorio/Lecaroz/styles/pages.css" rel="stylesheet" type="text/css">
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Modificaci&oacute;n de Materias Primas</p>
  <form action="" method="get" name="form">
  <input name="temp" type="hidden">
  <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">C&oacute;digo de Materia Prima </th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" size="5" maxlength="5"></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onClick="valida_registro()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro() {
		if (document.form.codmp.value <= 0) {
			alert("Debe especificar un código");
			document.form.codmp.select();
			return false;
		}
		else
			document.form.submit();
	}
	
	window.onload = document.form.codmp.select();
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : modificar -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle">
<p class="title">Modificaci&oacute;n de Materia Prima</p>
<form name="form" method="post" action="./fac_mat_mod.php">
<input name="temp" type="hidden">
<table class="tabla">
    <tr>
      <th class="vtabla">C&oacute;digo de materia prima</th>
      <td class="vtabla"><input name="codmp" type="text" class="insert" id="codmp" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.nombre.select();
else if (event.keyCode == 38) form.entregafinmes.select();" value="{codmp}" size="5" maxlength="5" readonly="true"></td>
    </tr>
    <tr>
      <th class="vtabla">Nombre Materia Prima </th>
      <td class="vtabla"><input name="nombre" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.porcientoincremento.select();
else if (event.keyCode == 38) form.codmp.select();" value="{nombre}" size="50" maxlength="50"></td>
    </tr>
    <tr>
      <th class="vtabla">Unidad de Consumo </th>
      <td class="vtabla">
        <select name="unidadconsumo" class="insert" id="unidadconsumo">
	  <!-- START BLOCK : unidad -->
	  <option value="{valueunidad}" {selected}>{nameunidad}</option>
          <!-- END BLOCK : unidad -->
        </select>
	  </td>
    </tr>
    <tr>
      <th class="vtabla">Tipo de Materia Prima </th>
	   <td class="vtabla">
        <select name="tipo" class="insert" id="tipo">
	  <!-- START BLOCK : tipo -->
	  <option value="{valuetipo}" {selected}>{nametipo}</option>
          <!-- END BLOCK : tipo -->
        </select>
	  </td>
    </tr>
    <tr>
      <th class="vtabla">Materia prima controlada</th>
      <td class="vtabla"><p>
        <label>
        <input name="controlada" type="radio" value="FALSE" {controlada_false}>
  No</label>

        <label>
        <input type="radio" name="controlada" value="TRUE" {controlada_true}>
  Si</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">Presentaci&oacute;n</th>
      <td class="vtabla">
	    <select name="presentacion" class="insert" id="presentacion">
	  <!-- START BLOCK : presentacion -->
	  <option value="{valuepresentacion}" {selected}>{namepresentacion}</option>
          <!-- END BLOCK : presentacion -->
        </select>
      </td>
    </tr>
    <tr>
      <th class="vtabla">Proceso autom&aacute;tico de pedidos</th>
      <td class="vtabla"><p>
        <label>
        <input name="procpedautomat" type="radio" value="FALSE" {aut_false}>
  No</label>
        
        <label>
        <input name="procpedautomat" type="radio" value="TRUE" {aut_true}>
  Si</label>
        <br>
      </p></td>
    </tr>
    <tr>
      <th class="vtabla">% de incremento al promedio </th>
      <td class="vtabla"><input name="porcientoincremento" type="text" class="insert" id="porcientoincremento" onFocus="form.temp.value=this.value" onChange="isFloat(this,2,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.entregafinmes.select();
else if (event.keyCode == 38) form.nombre.select();" value="{porcientoincremento}" size="6" maxlength="6"></td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de entregas para el pedido de fin de mes </th>
      <td class="vtabla"><input name="entregafinmes" type="text" class="insert" id="entregafinmes" onFocus="form.temp.value=this.value" onChange="isInt(this,form.temp)" onKeyDown="if (event.keyCode == 13 || event.keyCode == 40) form.codmp.select();
else if (event.keyCode == 38) form.porcientoincremento.select();" value="{entregafinmes}" size="6" maxlength="6"></td>
    </tr>
    <tr>
      <th class="vtabla">Materia prima para</th>
      <td class="vtabla"><input name="tipo_cia" type="radio" value="TRUE" {cia_true}>
        Panader&iacute;a&nbsp;&nbsp;
        <input name="tipo_cia" type="radio" value="FALSE" {cia_false}>
        Rosticer&iacute;a</td>
    </tr>
    <tr>
      <th class="vtabla">N&uacute;mero de orden </th>
      <td class="vtabla"><input name="orden" type="text" class="insert" id="orden" value="{orden}" size="4" maxlength="4"></td>
    </tr>
</table>
<p>
    <img src="./menus/delete.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Cancelar" onclick='history.back()'> 
    &nbsp;&nbsp;
	<img src="./menus/insert.gif" align="middle">&nbsp;&nbsp;<input type="button" class="boton" value="Modificar" onclick='valida_registro(document.form)'>
</p>
</form>
</td>
</tr>
</table>
<script language="javascript" type="text/javascript">
	function valida_registro(form) {
		if (form.nombre.value == "") {
			alert("Debe especificar el nombre de la materia prima");
			form.nombre.select();
			return false;
		}
		else if (form.porcientoincremento.value <= 0) {
			alert("Debe especificar el pocentaje de incremento");
			form.porcientoincremento.select();
			return false;
		}
		else if (form.entregafinmes.value <= 0) {
			alert("Debe especificar el número de entregas");
			form.entregafinmes.select();
			return false;
		}
		else
			if (confirm("¿Desea modificar la materia?"))
				form.submit();
			else
				form.nombre.select();
	}
</script>
<!-- END BLOCK : modificar -->
</body>
</html>
