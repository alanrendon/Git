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
<!-- START BLOCK : altas -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><form action="./ros_emp_altas_v2.php" method="post" name="form"><table class="tabla">
  <tr>
    <th class="tabla" scope="col">Nombre</th>
    <th class="tabla" scope="col">Apellido Paterno </th>
    <th class="tabla" scope="col">Apellido Materno </th>
    <th class="tabla" scope="col">Puesto</th>
  </tr>
  <!-- START BLOCK : fila -->
  <tr>
    <td class="tabla"><input name="nombre[]" type="text" class="vinsert" id="nombre" onKeyDown="if (event.keyCode == 13) ap_paterno[{i}].select()" value="{nombre}" size="20" maxlength="20"></td>
    <td class="tabla"><input name="ap_paterno[]" type="text" class="vinsert" id="ap_paterno" onKeyDown="if (event.keyCode == 13) ap_materno[{i}].select()" value="{ap_paterno}" size="20" maxlength="20"></td>
    <td class="tabla"><input name="ap_materno[]" type="text" class="vinsert" id="ap_materno" onKeyDown="if (event.keyCode == 13) nombre[{next}].select()" value="{ap_materno}" size="20" maxlength="20"></td>
    <td class="tabla"><select name="cod_puestos[]" class="insert" id="cod_puestos">
	  <option value="9" {9}>ENCAR. ROSTICERIA</option>
      <option value="10" {10}>EMPL. ROSTICERIA</option>
      <option value="11" {11}>SEGUNDO ROSTICERO</option>
    </select></td>
  </tr>
  <!-- END BLOCK : fila -->
</table>
  <p>
    <input type="button" class="boton" value="Cancelar" onClick="self.close()">
&nbsp;&nbsp;    
<input type="button" class="boton" value="Siguiente" onClick="validar()">
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var form = document.form, main_form = window.opener.document.form;

function validar() {
	var message = "¿Desea dar de alta los empleados (este proceso no es reversible)?";
	if (confirm(message)) {
		form.submit();
	}
	else {
		form.nombre[0].select();
	}
}

window.onload = form.nombre[0].select();
-->
</script>
<!-- END BLOCK : altas -->
<!-- START BLOCK : update -->
<script language="javascript" type="text/javascript">
<!--
var emp = new Array();
<!-- START BLOCK : emp -->
emp[{i}] = new Array();
emp[{i}]['num_emp'] = {num_emp};
emp[{i}]['id'] = {id};
emp[{i}]['nombre'] = "{nombre}";
<!-- END BLOCK : emp -->

function update() {
	var mw = window.opener;
	
	if (emp.length > 0) {
		for (var i = 0; i < emp.length; i++) {
			mw.emp[emp[i]['num_emp']] = new Array();
			mw.emp[emp[i]['num_emp']]['id'] = emp[i]['id'];
			mw.emp[emp[i]['num_emp']]['nombre'] = emp[i]['nombre'];
		}
		document.location = "ros_emp_con.php";
	}
	else {
		self.close();
	}
}

window.onload = update();
-->
</script>
<!-- END BLOCK : update -->
</body>
</html>
