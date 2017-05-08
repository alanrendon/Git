<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="../../styles/tablas.css" rel="stylesheet" type="text/css" />
<link href="../../styles/pages.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- START BLOCK : datos -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de L&iacute;mites de Renta de Expendios</p>
  <form action="pan_lim_ren_exp_con.php" method="get" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <tr>
      <th class="vtabla" scope="row">Compa&ntilde;&iacute;a</th>
      <td class="vtabla"><input name="num_cia" type="text" class="insert" id="num_cia" onfocus="tmp.value=this.value;this.select()" onchange="if (isInt(this,tmp)) cambiaCia()" onkeydown="if (event.keyCode == 13) this.blur()" size="3" />
        <input name="nombre" type="text" disabled="disabled" class="vnombre" id="nombre" size="30" /></td>
    </tr>
  </table>  <p>
    <input type="button" class="boton" value="Siguiente" onclick="validar()" />
</p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form, cia = new Array();
<!-- START BLOCK : c -->
cia[{num_cia}] = '{nombre}';
<!-- END BLOCK : c -->

function cambiaCia() {
	if (f.num_cia.value == '' || f.num_cia.value == '0') {
		f.num_cia.value == '';
		f.nombre.value == '';
	}
	else if (cia[get_val(f.num_cia)] != null)
		f.nombre.value = cia[get_val(f.num_cia)];
	else {
		alert('La compañía no se encuentra en el catálogo');
		f.num_cia.value = f.tmp.value;
		f.num_cia.select();
	}
}

function validar() {
	f.submit();
}

window.onload = f.num_cia.select();
//-->
</script>
<!-- END BLOCK : datos -->
<!-- START BLOCK : consulta -->
<table width="100%"  height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td align="center" valign="middle"><p class="title">Consulta de L&iacute;mites de Renta de Expendios</p>
  <form action="pan_lim_ren_exp_con.php" method="post" name="form">
    <input name="tmp" type="hidden" id="tmp" />
    <table class="tabla">
    <!-- START BLOCK : cia -->
	<tr>
      <th colspan="3" class="vtabla" scope="col">{num_cia} {nombre} </th>
      </tr>
    <tr>
      <th class="tabla">&nbsp;</th>
      <th class="tabla">Nombre</th>
      <th class="tabla">Importe</th>
    </tr>
	<!-- START BLOCK : fila -->
    <tr>
      <td class="tabla"><input name="id[]" type="checkbox" id="id" value="{id}" /></td>
      <td class="vtabla"><input name="idreg[]" type="hidden" id="idreg" value="{id}" />
        {nombre}</td>
      <td class="rtabla"><input name="importe[]" type="text" class="rinsert" id="importe" onfocus="tmp.value=this.value;this.select()" onchange="inputFormat(this,2)" onkeydown="if (event.keyCode == 13 || event.keyCode == 40) importe[{next}].select(); else if (event.keyCode == 38) importe[{back}].select()" value="{importe}" size="10" /></td>
    </tr>
	<!-- END BLOCK : fila -->
    <tr>
      <td colspan="3" class="tabla">&nbsp;</td>
      </tr>
	  <!-- END BLOCK : cia -->
  </table>  
  <p>
    <input type="button" class="boton" value="Regresar" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Borrar" onclick="borrar()" />
&nbsp;&nbsp;
<input type="button" class="boton" value="Actualizar" onclick="actualizar()" />
  </p></form></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
<!--
var f = document.form;

function actualizar() {
	for (var i = 0; i < f.importe.length; i++)
		if (get_val(f.importe[i]) == 0) {
			alert('No pueden quedar importes en cero');
			f.importe[i].select();
			return false;
		}
	
	if (confirm('¿Desea modificar los limites?')) {
		f.action = "pan_lim_ren_exp_con.php?actualizar=1";
		f.submit();
	}
}

function borrar() {
	var cont = 0;
	
	if (f.id.length == undefined) {
		cont += f.id.checked ? 1 : 0;
	}
	else {
		for (var i = 0; i < f.id.length; i++)
			cont += f.id[i].checked ? 1 : 0;
	}
	
	if (cont == 0) {
		alert('Debe seleccionar al menos un registro');
		return false;
	}
	
	if (confirm('¿Desea borrar los limites seleccionados?')) {
		f.action = "pan_lim_ren_exp_con.php?borrar=1";
		f.submit();
	}
}
//-->
</script>
<!-- END BLOCK : consulta -->
</body>
</html>
